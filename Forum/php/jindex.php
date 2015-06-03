<?php

?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="css/disc2.css">
<script src="js/jquery-1.10.2.min.js"></script>
<script>

$selected_menu_id = '';

function strip_italic(ital) {
  return ital.replace("<I>", "");
}

function onNewMsgsClick() {
      $("#new_msgs").html("<I>New messages</I>");
      var str= "<I>Expanded</I>";
      str = str.replace("<I>", "");
      $("#expanded").html(str);
   $("#new_msg").html("ReLoaded");
      e.preventDefault();

}

function processJSON(data)
{
  $("#status").html("Yes");

		  $.each(data, function(i, field){
                   $("#status").html(field + " ");
                  });

}
function sendJSON(json) {

                //$("#Result").html("&nbsp;");
		$.getJSON("json.php", json, function(data){
                  processJSON(data);
                });

}

/** Function to process GET data **/
function onDocumentLoaded() {
   $("#new_msg").html("Loaded");
                sendJSON({'23' : '23', 'test2': '4'});
}

$(document).ready(function() {
   onDocumentLoaded();
   $('a[href = "?new_msgs&id=test"]').click(function(){
     onNewMsgsClick();
   });
            $('#Send').click(function(e) {
                $("#Result").html("&nbsp;");
                sendJSON({'23' : '23', 'test2': '4'});
            });
        });

</script>
</head>
<body>

<div id="menu">
  <table width="100%">
    <tr>
      <td align="left">
        <div id="main_menu">
          [ <A class="menu" id="new_msgs" href="?new_msgs&id=test" onclick="return false;">New messages</A> |  
          <A class="menu" href="#"id="expanded" ><I>Expanded</I></A> |  
          <A class="menu" href="#" >Collapsed</A> ] 
        </div>
      </td>
      <td align="right">
        <div id="login_menu">
          [ <A class="menu" id="new_msg" href="#">New Message</A> |
            <A class="menu" href="#">Search</A> ]&nbsp;&nbsp;
          [ <A class="menu" href="#" >Logout</A> |
          <A class="menu" href="#"><font color="lightgrey"><B>A. Fig Lee</B></font></A> ] 
        </div>
      <td>
    </tr>
    <tr>
      <td>
        <div id="private_menu">

       </div>
     </td>
  </table>
</div>
<div id="status">
</div>
<div id="main">
  <div id="sticky">
  </div>
  <div id="main_area">

  </div>
</div>
<!--
<div id="Result">?</div>
<input type="button" value="Click" id="Send">Click me</input>
-->
</body>
</html>

