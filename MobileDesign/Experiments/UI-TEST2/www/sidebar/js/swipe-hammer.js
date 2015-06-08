/*
 * @license Copyright (C) 2014 Intel Corporation. All rights reserved.
 */

(function($) {
  'use strict';

  var getDataAnim = function($bar) {
    var attr = $bar.attr('data-anim');
    var json = dquote(attr);
    var obj = {};

    try {
      obj = JSON.parse(json);
    } catch(err) {
      return null;
    }

    return obj;
  };

  var getCurrentPosition = function($bar, args) {
    return parseInt($bar.css(args[1]));
  };

  var getCurrentDelta = function($bar, args, e) {
    var delta = (args[1] == 'left' || args[1] == 'right') ? 'deltaX' : 'deltaY';

    var delta_v = e.gesture[delta];
    if (args[1] === 'right' || args[1] === 'bottom') {
      delta_v = -1 * delta_v;
    }

    return delta_v;
  };

  var getClosedValue = function($bar, args) {
    var dim = (args[1] == 'left' || args[1] == 'right') ? 'width' : 'height';
    return -1 * ($bar[dim]() - args[4]);
  };

  var getCloseDirection = function(args) {
    switch(args[1]) {
      case 'left':   return Hammer.DIRECTION_LEFT;  break;
      case 'right':  return Hammer.DIRECTION_RIGHT; break;
      case 'top':    return Hammer.DIRECTION_UP;    break;
      case 'bottom': return Hammer.DIRECTION_DOWN;  break;
    }
  };

  var isClosing = function($bar, args, e) {
    var fastClose = (getCloseDirection(args) === e.gesture.direction) &&
      Math.abs(e.gesture.velocity) > 0.5;

    var pos = getCurrentPosition($bar, args);
    var closed = getClosedValue($bar, args);
    return fastClose || (0 - pos > pos - closed);
  };

  var clearSelection = function() {
    window.getSelection().removeAllRanges();
  };

  var clamp = function(val, min, max) {
    return Math.min(Math.max(val, min), max);
  };

  var dquote = function(str) {
    return str.replace(/'/g, "\"");
  };

  var isThisOpen = function() {
    var $bar = $(this);
    var args = uib_sb.get_dispatch_arg_list($bar);

    var closed_v = getClosedValue($bar, args);
    return getCurrentPosition($bar, args) !== closed_v;
  };

  var isThisClosed = function() {
    return !isThisOpen.call(this);
  };

  var closeThisSidebar = function() {
    return uib_sb.close_sidebar($(this));
  };

  var openThisSidebar = function() {
    return uib_sb.open_sidebar($(this));
  };

  $(function() {

    var $upage = $('.upage').hammer();
    if (!$upage.length) { return; }

    $upage.data('hammer').get('swipe').set({
      direction: Hammer.DIRECTION_ALL
    });

    $upage.on('swipe', function(e) {

      var $lefts = $('.swipe.leftbar', this);
      var $rights = $('.swipe.rightbar', this);
      var $tops = $('.swipe.topbar', this);
      var $bottoms = $('.swipe.botbar', this);

      // when swiping the page, hide any visible sidebars on the side opposite
      // the direction first
      //
      // on the next swipe, open any applicable sidebars

      e.gesture = e.gesture || {};

      if (e.gesture.direction === Hammer.DIRECTION_LEFT) {
        if ($lefts.filter(isThisOpen).length) {
          $lefts.filter(isThisOpen).each(closeThisSidebar);
        } else {
          $rights.filter(isThisClosed).each(openThisSidebar)
        }
      }

      else if (e.gesture.direction === Hammer.DIRECTION_RIGHT) {
        if ($rights.filter(isThisOpen).length) {
          $rights.filter(isThisOpen).each(closeThisSidebar);
        } else {
          $lefts.filter(isThisClosed).each(openThisSidebar)
        }
      }

      else if (e.gesture.direction === Hammer.DIRECTION_UP) {
        if ($tops.filter(isThisOpen).length) {
          $tops.filter(isThisOpen).each(closeThisSidebar);
        } else {
          $bottoms.filter(isThisClosed).each(openThisSidebar)
        }
      }

      else if (e.gesture.direction === Hammer.DIRECTION_DOWN) {
        if ($bottoms.filter(isThisOpen).length) {
          $bottoms.filter(isThisOpen).each(closeThisSidebar);
        } else {
          $tops.filter(isThisClosed).each(openThisSidebar)
        }
      }
    });

    var $swipe = $('.swipe').hammer();
    if (!$swipe.length) { return; }

    $swipe.data('hammer').get('pan').set({
      direction: Hammer.DIRECTION_ALL
    });

    $swipe.hammer().on('panstart', function(e) {

      // keep track of where the sidebar was when this pan started
      var $bar = $(this);
      var args = uib_sb.get_dispatch_arg_list($bar);
      $bar.data('init_v', getCurrentPosition($bar, args));

    }).on('pan', function(e) {

      var $bar = $(this);
      var args = uib_sb.get_dispatch_arg_list($bar);
      var anim = getDataAnim($bar);

      var delta_v = getCurrentDelta($bar, args, e);
      var init_v = $bar.data('init_v');
      var target_v = clamp(init_v + delta_v, -1 * anim.v, 0);

      // move the sidebar with the cursor
      anim.dur = 1;
      uib_sb.move_sidebar($bar, anim, target_v, null, args[1], args[2], args[3], args[4]);

    }).on('panend', function(e) {

      var $bar = $(this);
      var args = uib_sb.get_dispatch_arg_list($bar);
      var anim = getDataAnim($bar);

      // reset the saved initial value
      $bar.data('init_v', null);
      anim.dur = 150;

      // "snap" the sidebar to the closest side (opened or closed), or force a
      // direction with a high swipe velocity
      var closing = isClosing($bar, args, e);
      var closed_v = getClosedValue($bar, args);

      var finishSwipe = function() {
        uib_sb.move_sidebar($bar, anim, (closing ? closed_v : 0), function() {
          if (!this) { setTimeout(finishSwipe, 1); } // try, try again
        }, args[1], args[2], args[3], args[4]);

        clearSelection();
      };

      finishSwipe();
    });

  });

})(jQuery);
