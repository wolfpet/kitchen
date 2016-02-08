<?php
/*$Id: profile_inc.php 942 2013-09-01 12:10:18Z dmitriy $*/
?>
<p/>
<table> 
<tr>
<td valign="top" nowrap>
<form action="<?php print( $root_dir . $page_update ); ?>" method="post">
<table>
<tr>
<td>Password <font color="grey">(16 characters maximum, 4 minimum)</font></td>
<td><input id="password" name="password" type="password" maxlength="16" autocomplete="off"/></td>
</tr>
<tr>
<td>Retype password: </td>
<td><input id="password2" name="password2" type="password" maxlength="16" autocomplete="off"/></td>
</tr>
<tr>
<td>Email</td>
<td><input id="email" name="email" type="text" maxlength="80" value="<?php print($email); ?>"/></td>
</tr>
<tr>
<td>Retype email: </td>
<td><input id="email2" name="email2" type="text" maxlength="80" value="<?php print($email2); ?>"/></td>
</tr>
<tr>
<td>Bold titles in thread mode: </td>
<td><input id="profile_bold" name="profile_bold" type="checkbox" maxlength="80" <?php if (!is_null($profile_bold) && $profile_bold > 0) { print("checked"); } ?>/></td>
</tr>
<tr>
<td>Show smileys as icons: </td>
<td><input id="show_smileys" name="show_smileys" type="checkbox" maxlength="80" <?=$smileys ? "checked" : ""?>/></td>
</tr>
<tr>
<td>Send email about a reply to your post: </td>
<td><input id="reply_to_email" name="reply_to_email" type="checkbox" maxlength="80" <?=$reply_to_email ? "checked" : ""?>/></td>
</tr>
<tr>
<td>Time zone:</td>
<td>
<select id="tz" name="tz">
<option value="-11" <?=$prop_tz == "-11" ? "selected=\"selected\"" : "" ?>>(GMT-11) Samoa</option>
<option value="-10" <?=$prop_tz == "-10" ? "selected=\"selected\"" : "" ?>>(GMT-10) Hawaii</option>
<option value="-9" <?=$prop_tz == "-9" ? "selected=\"selected\"" : "" ?>>(GMT-9) Alaska</option>
<option value="-8" <?=$prop_tz == "-8" ? "selected=\"selected\"" : "" ?>>(GMT-8) Vancouver</option>
<option value="-7" <?=$prop_tz == "-7" ? "selected=\"selected\"" : "" ?>>(GMT-7) Calgary</option>
<option value="-6" <?=$prop_tz == "-6" ? "selected=\"selected\"" : "" ?>>(GMT-6) Regina</option>
<option value="-5" <?=$prop_tz == "-5" ? "selected=\"selected\"" : "" ?>>(GMT-5) Ottawa, Toronto</option>
<option value="-4" <?=$prop_tz == "-4" ? "selected=\"selected\"" : "" ?>>(GMT-4) Atlantic Time</option>
<option value="-3" <?=$prop_tz == "-3" ? "selected=\"selected\"" : "" ?>>(GMT-3) Greenland Time</option>
<option value="-2" <?=$prop_tz == "-2" ? "selected=\"selected\"" : "" ?>>(GMT-2) Mid-Atlantic </option>
<option value="-1" <?=$prop_tz == "-1" ? "selected=\"selected\"" : "" ?>>(GMT-1) Azores</option>
<option value="0" <?=$prop_tz == "0" ? "selected=\"selected\"" : "" ?>>(GMT+0) London, Dublin</option>
<option value="1" <?=$prop_tz == "1" ? "selected=\"selected\"" : "" ?>>(GMT+1) Amsterdam</option>
<option value="2" <?=$prop_tz == "2" ? "selected=\"selected\"" : "" ?>>(GMT+2) Kyiv, Jerusalem</option>
<option value="3" <?=$prop_tz == "3" ? "selected=\"selected\"" : "" ?>>(GMT+3) St. Petersburg</option>
<option value="4" <?=$prop_tz == "4" ? "selected=\"selected\"" : "" ?>>(GMT+4) Yerevan</option>
<option value="5" <?=$prop_tz == "5" ? "selected=\"selected\"" : "" ?>>(GMT+5) Yekaterinburg</option>
<option value="6" <?=$prop_tz == "6" ? "selected=\"selected\"" : "" ?>>(GMT+6) Novosibirsk</option>
<option value="7" <?=$prop_tz == "7" ? "selected=\"selected\"" : "" ?>>(GMT+7) Krasnoyarsk</option>
<option value="8" <?=$prop_tz == "8" ? "selected=\"selected\"" : "" ?>>(GMT+8) Irkutsk, Beijing</option>
<option value="9" <?=$prop_tz == "9" ? "selected=\"selected\"" : "" ?>>(GMT+9) Tokyo, Seoul</option>
<option value="10" <?=$prop_tz == "10" ? "selected=\"selected\"" : "" ?>>(GMT+10) Vladivostok</option>
<option value="11" <?=$prop_tz == "11" ? "selected=\"selected\"" : "" ?>>(GMT+11) Magadan</option>
<option value="12" <?=$prop_tz == "12" ? "selected=\"selected\"" : "" ?>>(GMT+12) New Zealand</option>
<option value="13" <?=$prop_tz == "13" ? "selected=\"selected\"" : "" ?>>(GMT+13) Phoenix Islands</option>
<option value="14" <?=$prop_tz == "14" ? "selected=\"selected\"" : "" ?>>(GMT+14) Line Island</option>
</select>
</td>
</tr>
<tr>
<td colspan="2"> <input type="submit" value="Update"/></td>
</tr>
</table>
</form>

</td>
<td valign="top">
<!-- Start ignore tablre -->

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
<INPUT type="submit" value="Save" id="Save">
<!--</form>-->
</td> </tr>
</table>
<!-- end ignore table -->
</td> </tr> </table>
</body>
</html>

