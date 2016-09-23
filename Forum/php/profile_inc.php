<?php
/*$Id: profile_inc.php 942 2013-09-01 12:10:18Z dmitriy $*/
?>

<ul class="tab">
  <li><a class="tablinks" onclick="openTab(event, 'General')">General</a></li>
  <li><a class="tablinks" onclick="openTab(event, 'Notifications')">Notifications</a></li>
  <li><a class="tablinks" onclick="openTab(event, 'Ignore')">Ignore</a></li>
</ul>
<div style="position:absolute;left: 0px;top: 0px;width: 100%;z-index: 9999;text-align: right;"><input id="safe_mode" type="checkbox" <?=!isset($safe_mode) || $safe_mode == 0 ? "checked" : ""?>><i>Show NSFW content</i></input>&nbsp;</div>

<div id="General" class="tabcontent">
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
</html>

