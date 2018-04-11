<?php
/*$Id: profile_inc.php 942 2013-09-01 12:10:18Z dmitriy $*/

  $colorquery = "SELECT color_ribbon, color_ribbon_background, color_icon_hover, color_group_border, color_topics_unread, color_topics_hover, color_topics_visited, color_titles from confa_users where id = " . $user_id;
  //die($colorquery);
  
  $colorresult = mysql_query($colorquery);
  
  if ($colorresult) {
    $colorrow = mysql_fetch_assoc($colorresult);
    $ribbonColor=$colorrow['color_ribbon'];
    $ribbonBackground=$colorrow['color_ribbon_background'];
    $mickeyMouse = $colorrow['color_icon_hover'];
    $groupBorder=$colorrow['color_group_border'];
    $color_topics_unread= $colorrow['color_topics_unread'];
    $color_topics_hover=$colorrow['color_topics_hover'];
    $color_topics_visited=$colorrow['color_topics_visited'];
    $color_titles=$colorrow['color_titles'];
  }
?>
<script>
function restoreColors(){
    $("#ribbonBackground").spectrum("set",  "#0080c0");
    $("#ribbonColor").spectrum("set",  "#FFFFFF");
    $("#ribbonIconBg").spectrum("set",  "#0090c0");
    $("#ribbonGroupBorder").spectrum("set",  "#0090c0");
    $("#ribbonGroupBorder").spectrum("set",  "#0090c0");
    $("#textHover").spectrum("set",  "#ff0000");
    $("#textUnread").spectrum("set",  "#0000ff");
    $("#textRead").spectrum("set",  "#0080c0");
    $("#textTitles").spectrum("set",  "#0080c0");
}


$(document).ready(function() {
    /*Load custom colors*/
    $("#ribbonColor").spectrum("set", "<?=$ribbonColor?>");
    $("#ribbonBackground").spectrum("set", "<?=$ribbonBackground?>");
    $("#ribbonColor").spectrum("set",  "<?=$ribbonColor?>");
    $("#ribbonIconBg").spectrum("set", "<?=$mickeyMouse?>");
    $("#ribbonGroupBorder").spectrum("set",  "<?=$groupBorder?>");
    $("#textHover").spectrum("set", "<?=$color_topics_hover?>");
    $("#textUnread").spectrum("set", "<?=$color_topics_unread?>");
    $("#textRead").spectrum("set",  "<?=$color_topics_visited?>");
    $("#textTitles").spectrum("set", "<?=$color_titles?>");
    //init local settings from cookies
    initiateLocalSettings();
});

function initiateLocalSettings()
{
  //layout
  var layout = parent.getCookie("verticalLayout");
  if(layout=='false') document.getElementById("verticalLayout").checked = false;
  else document.getElementById("verticalLayout").checked = true;
  //font type
  var font = parent.getCookie("font");
  if(font=='')font='Verdana'
  document.getElementById("Font").value=font;
  //font size
  var fontSize = parent.getCookie("fontSize");
  if(fontSize=='')fontSize=10;
  document.getElementById("FontSize").value=fontSize;
}

function saveLocalSettings()
{
var layout=false;
if(document.getElementById("verticalLayout").checked)layout=true;
//save layout
document.cookie = "verticalLayout="+layout+"; expires=01 Jan 2040 12:00:00 UTC; path=/";

var font;
font= document.getElementById("Font").value;
document.cookie = "font="+font+"; expires=01 Jan 2040 12:00:00 UTC; path=/";

var fontSize;
fontSize =document.getElementById("FontSize").value;
document.cookie = "fontSize="+fontSize+"; expires=01 Jan 2040 12:00:00 UTC; path=/";

//reload
top.location.reload();
}


</script>
<ul class="tab">
  <li><a class="tablinks" onclick="openTab(event, 'AcrossDevices')">Account</a></li>
  <li><a class="tablinks" onclick="openTab(event, 'ThisDevice')">This Device</a></li>
  <li><a class="tablinks" onclick="openTab(event, 'Colors')">Colors</a></li>
  <li><a class="tablinks" onclick="openTab(event, 'Ignore')">Ignore</a></li>
</ul>
<div id="AcrossDevices" class="tabcontent">
<table> 
<tr>
<td valign="top" nowrap>
<form action="<?php print( $root_dir . $page_update ); ?>" method="post">
<table>
<tr>
<td align="right">Password <font color="grey">(4-16 characters)</font></td>
<td><input id="password" name="password" type="password" maxlength="16" autocomplete="off"/></td>
</tr>
<tr>
<td align="right">Retype password: </td>
<td><input id="password2" name="password2" type="password" maxlength="16" autocomplete="off"/></td>
</tr>
<tr>
<tr>
<td></td>
<td>Leave the fields blank if you <br>don't want to change your password<br><br></td>
</tr>
<td align="right">Email</td>
<td><input id="email" name="email" type="text" maxlength="80" value="<?php print($email); ?>"/></td>
</tr>
<tr>
<td align="right">Retype email: </td>
<td><input id="email2" name="email2" type="text" maxlength="80" value="<?php print($email2); ?>"/></td>
</tr>
<tr>
<td align="right">Bold titles in thread mode: </td>
<td><input id="profile_bold" name="profile_bold" type="checkbox" maxlength="80" <?php if (!is_null($profile_bold) && $profile_bold > 0) { print("checked"); } ?>/></td>
</tr>
<tr>
<td align="right">Show smileys as icons: </td>
<td><input id="show_smileys" name="show_smileys" type="checkbox" maxlength="80" <?=$smileys ? "checked" : ""?>/></td>
</tr>
<tr>
<td align="right">Reply email notification: </td>
<td><input id="reply_to_email" name="reply_to_email" type="checkbox" maxlength="80" <?=$reply_to_email ? "checked" : ""?>/></td>
</tr>
<tr>
<td align="right">Classic forum menu: </td>
<td><input id="menu_style" name="menu_style" type="checkbox" maxlength="80" <?=isset($menu_style) && $menu_style > 0 ? "checked" : ""?>/></td>
</tr>
<tr>
<td align="right">Show NSFW content:</td>
<td><input id="safe_mode" type="checkbox" <?=!isset($safe_mode) || $safe_mode == 0 ? "checked" : ""?>></input></td>
</tr>
<tr>
<td align="right">Time zone:</td>
<td>
<select id="tz" name="tz">
<?php
 $tz_list = get_tz_list();
 foreach($tz_list AS $key => $tz) {
?>
  <option value="<?=$key?>" <?=$prop_tz_name == $key ? "selected=\"selected\"" : "" ?>>(UTC<?=$tz['offset'] < 0 ? '' : '+'?><?=$tz['offset']?>) <?=$tz['name']?></option>
<?php
}
?>
</select>
</td>
</tr>
<tr>
<td colspan="2"><br/> <input type="submit" value="Update"/></td>
</tr>
</table>
</form>

</td>
<td valign="top">
<!-- Start ignore table -->

</td></tr>
</table>
</div>

<div id="ThisDevice" class="tabcontent">
<table> 
<tr>
<tr>
<td align="right">Font Type: </td>
<td><input id="Font" name="Font" type="text" maxlength="80" value=""/></td>
</tr>
<tr>

<td align="right">Font Size: </td>
<td><input id="FontSize" name="FontSize" type="text" maxlength="2" value="" style="width:50px"/></td>
</tr>

<tr>
<td align="right">Vertical Pane Layout </td>
<td><input id="verticalLayout" name="verticalLayout" type="checkbox" /></td>
</tr>
<tr>
<td colspan="2"><br/> <input type="button" value="Update" onclick="saveLocalSettings();"/></td>
</tr>

</table>
</div>

<div id="Colors" class="tabcontent">
<table>
<tbody>

<tr>
      <td colspan="4" align="center" style="color:<?=$ribbonColor?> ;" bgcolor="<?=$ribbonBackground?>">Main Menu Ribbon colors</td>
</tr>
<tr>
<td align="right">Icons</td>
<td>
<input type='text' id="ribbonColor"/>
</td>
<td align="right">Background</td>
<td>
<input type='text' id="ribbonBackground"/>
</td>
</tr>

<tr>
<td align="right">Icon Hover Background</td>
<td>
<input type='text' id="ribbonIconBg"/>
</td>
<td align="right">Ribbon Group Border</td>
<td>
<input type='text' id="ribbonGroupBorder"/>
</td>
</tr>

<tr>
    <td colspan="4" align="center" style="color:<?=$ribbonColor?> ;" bgcolor="<?=$ribbonBackground?>">Topics and text colors</td>
</tr>
<tr>
<td align="right">Unread Topics</td>
<td>
<input type='text' id="textUnread"/>
</td>
<td align="right">Topics Hover</td>
<td>
<input type='text' id="textHover"/>
</td>
</tr>

<tr>
<td align="right">Visited Topics</td>
<td>
<input type='text' id="textRead"/>
</td>
<td align="right">Titles</td>
<td>
<input type='text' id="textTitles"/>
</td>
</tr>

<tr>
<td align="right">
</td>
<td>
</td>
</tr>

<tr>
    <td colspan="4" align="center">&nbsp;&nbsp;<button id="colors_restore" onclick="restoreColors();">Reset to default</button><button id="colors_update" onclick="updateColors();">Save and refresh</button></td>
</tr>

</table>
</div>

<div id="Ignore" class="tabcontent">

<!-- Start ignore table -->

<?php
    $query = "SELECT i.ignored, i.ignored_by, u.username, u.id from confa_users u, confa_ignor i where i.ignored_by=" . $user_id . " and i.ignored=u.id order by username";
    $result = mysql_query($query);
    if (!$result) {
        mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed');
      }
    $ignored = array();
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
      $ignored[$row['ignored']] = $row['username'];
    }
?>
<br/>
<table>
<tr> <td align="center">
<B> Not Ignored users </B>
</td>
<td/>
<td align="center"> <B> Ignored users </B>
</td> </tr>
<tr> <td valign="top"> 
<select name="drop1" id="lstNoIgnor" size="10" style="width: 200px;" multiple="multiple">
<?php
    $query = "SELECT username, id from confa_users where status=1 order by username";
    $result = mysql_query($query);
    if (!$result) {
        mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed');
      }
    $users = array();
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$users[$row['id']] = $row['username'];
        if (!array_key_exists($row['id'], $ignored) && $user_id != $row['id']) {
          print("<option value=\"" . $row['id'] . "\" id=" . $row['id'] . " >" . $row['username'] . "</option>\n");
        }
    }
?>
</select>
</td>
<td align="center" valign="middle">
<input type="button" id="btnIgnor" value="->"><br />
<input type="button" id="btnNoIgnor" value="<-">
</td>
<td>
<!--<form method="post" action"api.php">-->
<select name="drop2" id="lstIgnor" size="10" style="width: 200px;" multiple="multiple">
<?php
  while (list($key, $val) = each($ignored)) {
    print("<option value=\"" . $key . "\" id=" . $key . " >" . $val . "</option>\n"); 
  }
?>
</select>
<!--<BR>-->
</td> </tr>
<?php
      $query = "SELECT show_hidden from confa_users where id=" . $user_id;
      $result = mysql_query($query);
      if (!$result) {
        mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed');
      }
      $checked = "";
      $row = mysql_fetch_array($result, MYSQL_ASSOC);
      if ($row['show_hidden'] == 1) {
        $checked = "checked";
      }
?>
<tr><td colspan=3>
<input type="checkbox" name="show_hidden" value="show_hidden" id="show_hidden" <?php print ($checked); ?>>Show placeholder for hidden messages<br>
</br>
<INPUT type="submit" value="Save" id="Save">
<!--</form>-->
</td> </tr>
</table>
</div>
<script>

openTab(event, 'AcrossDevices'); //open general tab by default
tablinks = document.getElementsByClassName("tablinks");
tablinks[0].className += " active";

function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    try{
    evt.currentTarget.className += " active";
	}catch(err){} //ignore on the initial load since there is no click and no currentTarget defined
}
</script>
<br><br><br>
</html>

