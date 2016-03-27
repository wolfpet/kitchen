<?php
/*$Id: html_head_inc.php 942 2013-09-01 12:10:18Z dmitriy $*/

require_once('head_inc.php');

    if (is_null($doc_type)) {
        print('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">');
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
<title><?php 
/*    if (is_null($page_title)) {
        print($title);
    } else {
        print($page_title);
    } */?>"Forum Kitchen"</title>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-Frame-Options" content="SAMEORIGIN">
<meta http-equiv="X-Content-Type-Options" content="nosniff">
<link rel="stylesheet" type="text/css" href="<?=autoversion('css/'.$css)?>">
<link rel="stylesheet" type="text/css" href="<?=autoversion('css/common.css')?>">
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
  if (bydate !== null) {
    var update_bydate_counter = function() {
      var url1 = "./api/messages?mode=bydate&format=count_only";
      console.log("calling bydate("+url1+")");
      $.ajax({
             type: "GET",
             url: url1,
             success: function(obj1) {
                console.log("bydate object=" + obj1);
                var count = obj1.count;
                console.log("bydate=" + count);
                var text = bydate.innerHTML;
                var braket = text.indexOf("(");
                if (braket >= 0) text = text.substring(0, braket);
                if (count > 0) text += "(<b>" + count + "</b>)";
                bydate.innerHTML = text;
                // adjust frequency of calls if necessary
                if (bydate_count == 10) {
                  window.clearInterval(bydate_timer);
                  bydate_timer = window.setInterval(function() {update_bydate_counter();}, 5*60000);                   
                  console.log('Checking bydate every 5 min');
                } else if (bydate_count == 20) {
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
</script>
