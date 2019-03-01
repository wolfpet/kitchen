<?php
/*$Id: top.php 378 2009-11-02 19:36:24Z dmitriy $*/
require_once('head_inc.php');
require_once('html_head_inc.php');
require_once('mysql_log.php');
?>
<base target="bottom">
<script type="text/javascript" src="<?=autoversion('js/controls.js')?>"></script>
<script type="text/javascript" src="js/jquery-ui.js"></script>
<script>
function expand()
{
  verticalLayout = getCookie("verticalLayout");
  if(verticalLayout=='true')return;
  document.getElementById("frame1").style.height='0vh';
  document.getElementById("frame2").style.height='88vh';
  document.getElementById("hr1").style.display='none';
}
function restore()
{
  verticalLayout = getCookie("verticalLayout");
  if(verticalLayout=='true')return;
  document.getElementById("frame1").style.height='40vh';
  document.getElementById("frame2").style.height='48vh';
  document.getElementById("hr1").style.display='block';
}
function expanded() 
{
  return document.getElementById("hr1").style.display == 'none';
}
function toggle() 
{
  if (expanded()) {
    restore();
  } else {
    expand();
  }
}
/*
$( function() { $( "#slider" ).draggable({ containment: "#slider-area", scroll: false, axis: "y", 
      start: function() {
                    document.getElementById("resizer").style.fill="red";
                    document.getElementById("hr1").style.borderColor="red";
            },
      drag: function() {
                    var h = $("#slider").position().top;
                    var vhpx = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
                    var menu_h = $("#Ribbon").height();
                    var frame1_vh = ((((h+menu_h - 10) * 100)/vhpx) || 0);
                    document.getElementById("frame1").style.height=frame1_vh + 'vh';
                    var frame2_vh = 100 - 10 - frame1_vh;
                    document.getElementById("frame2").style.height= frame2_vh  + 'vh';
            },
      stop:  function() {
                    document.getElementById("resizer").style.fill="grey";
                    document.getElementById("hr1").style.borderColor="grey";
            } 
          });
});
*/
</script>
<!-- iOS compatibility -->
<style>
  html, body{
      height: 100%;
  }

  .url-wrapper{
      height: 100%;
  }

  .url-wrapper iframe{
      height: 100%;
      width: 100%;
  }

  .url-wrapper.ios{
      overflow-y: auto;
      -webkit-overflow-scrolling:touch !important;
      height: 100%;
  }

  .url-wrapper.ios iframe{
      height: 100%;
      min-width: 100%;
      width: 100px;
      *width: 100%;
  }
  
  #scroll2top {
      position: fixed;
      top: 4.6em !important;
      left: 0.3em !important;
      display:none;
      z-index:9999;
  }
  
  #scroll2top span {
    padding:0.3em;
  }
  
  #scroll2top span img {
   -webkit-filter: invert(1);
   filter: invert(1);
  }  
</style>
<script type="text/javascript">
var lastY = 0; // Needed in order to determine direction of scroll.

function create_iframe(id, name, url) {
    var wrapper = jQuery('#'+id);

    if(navigator.userAgent.match(/(iPod|iPhone|iPad)/)){
        wrapper.addClass('ios');
        var scrolling = 'no';
    }else{
        var scrolling = 'yes';
    }

    jQuery('<iframe>', {
        src: url,
        id:  name,
        'name': name,
        frameborder: 0,
        scrolling: scrolling
    }).appendTo(wrapper);
}
function init() {
  create_iframe('frame1', 'contents', 'threads.php');
  create_iframe('frame2', 'bottom', 'welc.php');  
  
  // fix scrolling issues for div
  $(".url-wrapper").on('touchstart', function(event) {
      lastY = event.touches[0].clientY;
  });

  $(".url-wrapper").on('touchmove', function(event) {
      var top = event.touches[0].clientY;

      // Determine scroll position and direction.
      var scrollTop = $(event.currentTarget).scrollTop();
      var direction = (lastY - top) < 0 ? "up" : "down";

      // FIX IT!
      if (scrollTop == 0 && direction == "up") {
        // Prevent scrolling up when already at top as this introduces a freeze.
        event.preventDefault();
      } else if (scrollTop >= (event.currentTarget.scrollHeight - event.currentTarget.outerHeight()) && direction == "down") {
        // Prevent scrolling down when already at bottom as this also introduces a freeze.
        event.preventDefault();
      }

      lastY = top;
  });    
}

function scroller2Top() {
  $("#frame1").scrollTop(0);
}

function load_more() {
  
 var div = document.getElementById("contents");
 var parent = document.getElementById("frame1");
 
 if (parent == null || div == null) {
  console.log("Something is not right: threads or html body element is not found");
  return;
 }
 
 var content = div.getBoundingClientRect(); 
 var frame = parent.getBoundingClientRect();
  
 var scroller = document.getElementById("scroll2top"); 
 if (scroller != null) {
    if (content.top < 0) {
      scroller.style.display = "block";
    } else {
      scroller.style.display = "none";
    }
  }
  
  var result = frame.bottom >= content.bottom - 300;
  
  // document.getElementById("message1").innerHTML = "scroller y=" + frame.top + " yOffset=" + frame.bottom + " threadsY=" + content.top + " " + content.bottom + " " + result;
  return result;
}
</script>
<!-- end -->
<title><?=$title?></title>
</head>
<body id="html_body" style="overflow: hidden;" onload="init();">
  <div id="scroll2top"><span style="cursor: pointer; color:blue" onclick="javascript:scroller2Top();"><img border=0 src="images/up.png" alt="Up" title="Back to top"></span></div>
  <!--<div id="message1" style="color:gray;position:fixed;left: 0px;top:5em;width: 100%;height: 100%;z-index: 9999;text-align: right;display:block"></div>-->
<?php
require('menu_inc.php');
?>
<div class="url-wrapper" id="frame1" style="position: static; height: calc(50vh - 54px); background-color: white;display: inline-block;width: 100vw;"></div>
<hr id="hr1"><!--
<div id="slider-area">
    <div id="slider" class="draggable ui-widget-content"><svg viewBox="0 0 24 24" preserveAspectRatio="xMidYMid meet" class="style-scope iron-icon" style="pointer-events: none; display: block; width: 100%; height: 100%;"><g><path id="resizer" fill="grey" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM6.5 9L10 5.5 13.5 9H11v4H9V9H6.5zm11 6L14 18.5 10.5 15H13v-4h2v4h2.5z"></path></g></svg></div>
</div>-->
<div class="url-wrapper" id="frame2" style="position: relative;height: 48vh; background-color: white;display: inline-block; width: 100vw;"></div>
<?php
require('gallery_inc.php');
require('overlay_inc.php');
require_once('tail_inc.php');
?>
</body>
</html>
