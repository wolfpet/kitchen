<?php
/*$Id: html_head_inc.php 942 2013-09-01 12:10:18Z dmitriy $*/
require_once('head_inc.php');
    if (is_null($doc_type)) {
        print('<!DOCTYPE html>');
    } else {
        print($doc_type);
    }
$css = 'disc2.css';
if (!is_null($user_id) && $user_id != null) {
  $query = "SELECT css from confa_users where id = " . $user_id;
  $result = mysql_query($query);
  if ($result) {
    $row = mysql_fetch_assoc($result);
    $css = $row['css'];
  } 
}
?>
<html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-Frame-Options" content="SAMEORIGIN">
<meta http-equiv="X-Content-Type-Options" content="nosniff">
<meta name="viewport" content="width=device-width, minimum-scale=1, initial-scale=1, user-scalable=no">
<link rel="stylesheet" type="text/css" href="<?=autoversion('css/'.$css)?>">
<link rel="stylesheet" type="text/css" href="<?=autoversion('css/common.css')?>">
<?php if (!isset($classic_menu) && (!isset($menu_style) || $menu_style == 0)) { ?>
<link rel="stylesheet" type="text/css" href="<?=autoversion('css/ribbon.css')?>">
<?php } ?>
<script src="js/jquery-1.10.2.min.js"></script>
<script>
var selected_id = "";

function selectMsg(id) {
  id = "sp_" + id;
  console.log('selectMsg  id=' + id);
  if (selected_id != "") {
    // reset selection
    console.log('resetting selected id=' + selected_id);
    var selected = document.getElementById(selected_id);
    if (selected != null) {
      selected.className = null;
    }  
  }
  var selected = document.getElementById(id);
  if (selected != null) {
    console.log('selected element id=' + id);
    // select message
    selected.className = "selected";
    selected_id = id;
  } else {
    // message not found
    selected_id = "";
    console.log('id=' + id + "not found");
  }
}

var bydate_timer = -1;
var bydate_count = 0;

$( document ).ready(function() {
  var bydate = document.getElementById('bydate');
  
  var newPostsBadge = null;

  if (bydate == null) {
    newPostsBadge = document.getElementById('newPostsBadge');
  }
  
  if (bydate !== null || newPostsBadge != null) {
    var update_bydate_counter = function() {
      var url1 = "./api/messages?mode=bydate&format=count_only";
      console.log("calling bydate("+url1+")");
      bydate_count++;
      var start_time = new Date();
      $.ajax({
             type: "GET",
             url: url1,
             success: function(obj1) {
                var end_time = new Date();
                console.log("bydate object=" + obj1);
                var count = obj1.count;
                console.log("bydate=" + count);                
                if (bydate != null) {
                  bydate.innerHTML = addCounter(bydate.innerHTML, count, true, false);
                } else if (newPostsBadge != null) {
                  if (count > 0) {
                    newPostsBadge.innerHTML = count;
                    newPostsBadge.style.display = 'block';
                  } else {
                    newPostsBadge.style.display = 'hidden';
                  }
                }
                // update answered badge or element
                var newAnswersBadge = document.getElementById('newAnswersBadge');
                var answered = null;
                if (newAnswersBadge == null) {
                  answered = document.getElementById('answered');
                }
                if (newAnswersBadge != null || answered != null) {
                  $.ajax({
                    type: "GET",
                    url: "./api/messages?mode=answered&format=count_only",
                    success: function(obj2) {
                      var count = obj2.count;
                      console.log("answered=" + count);
                      if (newAnswersBadge != null) {
                        if (count > 0) {
                          newAnswersBadge.innerHTML = count;
                          newAnswersBadge.style.display = 'block';
                        } else {
                          newAnswersBadge.style.display = 'hidden';
                        }
                      } else if (answered != null) {
                        answered.innerHTML = addCounter(answered.innerHTML, count, true, false);                        
                      }
                    }
                  });                             
                }                
                // update title
                var newTitle = addCounter(window.parent.document.title, count, false, true);
                console.log(newTitle);
                window.parent.document.title = newTitle;
                // call user function, if defined
                if (typeof onNewMessageCount !== 'undefined') {
                  console.log('calling user function');
                  onNewMessageCount(count, end_time.getTime() - start_time.getTime());
                }
                // adjust frequency of calls if necessary
                if (bydate_count == 15) {
                  window.clearInterval(bydate_timer);
                  bydate_timer = window.setInterval(function() {update_bydate_counter();}, 5*60000);                   
                  console.log('Checking bydate every 5 min');
                } else if (bydate_count == 35) {
                  window.clearInterval(bydate_timer);
                  bydate_timer = window.setInterval(function() {update_bydate_counter();}, 15*60000);                   
                  console.log('Checking bydate every 15 min');
                }                   
             }
           });      
    };
    window.setTimeout( function() {update_bydate_counter();}, 1000 );
    bydate_timer = window.setInterval(function(){update_bydate_counter();}, 60000); 
    console.log('Checking bydate every minute');
  }
});

function addCounter(text, count, bold, pad) {
    var prefix = pad ? " (" : "(";
    var braket = text.indexOf(prefix);
    if (braket >= 0) text = text.substring(0, braket);
    if (count > 0) text += prefix + (bold?"<b>":"") + count + (bold?"</b>":"") +")";
    return text;                 
}

var focused = null;
// shift - select
$(document).ready(function(){
 // add click function to checkboxes
 $(document).find(':checkbox').each(function() {
    $(this).click(function(e) {
      if (e.shiftKey) {
        if (focused != null) {
          var checked = this.checked;
          var current = this;
          // make all checkboxes between 'focused' and 'current' same as 'current'
          var inside = false;
          $(document).find(':checkbox').each(function() {
            if (this.value == current.value || this.value == focused.value) {
              this.checked = checked;
              inside = !inside;
              if (!inside) return;
            } else if (inside) {
              this.checked = checked;
            }
          });
        }
      } else {
        focused = this;
      }
    });
  });
});
  
// Back navigation on Backspace
document.addEventListener("keydown", function (event) {
  
  if (event.keyCode == 8) {
    var e = document.activeElement;
    
    if (e != null && (e.tagName == 'INPUT' || e.tagName == 'TEXTAREA')) {
      // console.log('navigation cancelled');
      return;
    }
    
    event.preventDefault();
    history.go(-1);
   }
});  
</script>