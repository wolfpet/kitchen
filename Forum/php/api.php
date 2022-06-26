<?php
/*$Id: profile.php 379 2009-11-02 19:40:34Z dmitriy $*/
require_once('dump.php');
require_once('head_inc.php');

$status = 401;
$text = "Not authorized";

// mysql_log(__FILE__, "check");
if ($logged_in) {

    $request = "";

    if (array_key_exists('request', $_POST)) {
        $request = trim($_POST["request"]);
    }

    if (strcmp($request, "ignore.user_ids") == 0) {
      $query = "DELETE from confa_ignor where ignored_by=" . $user_id;
      $result = mysql_query($query);
      if (!$result) {
        mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed');
      }
    }
    if (array_key_exists('update_show_hidden', $_POST)) {
      $update_show_hidden = intval(trim($_POST['update_show_hidden']), 10);
      $query = "UPDATE confa_users set show_hidden=" . $update_show_hidden . " where id=" . $user_id;
      $result = mysql_query($query);
      if (!$result) {
        mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed');
      }
    }
    if (array_key_exists('update_safe_mode', $_POST)) {
      $update_safe_mode = intval(trim($_POST['update_safe_mode']), 10);
      $query = "UPDATE confa_sessions set safe_mode=" . $update_safe_mode . " where user_id=" . $user_id . ' and hash =\'' . $auth_cookie . '\'';
      $result = mysql_query($query);
      if (!$result) {
        mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed');
      }
    }

    if (array_key_exists('update_colors', $_POST)) {
      $update_colors = $_POST['update_colors'];
      $ribbonColor = intval($update_colors[0]);
      $ribbonBackground = intval($update_colors[1]);
      $ribbonIconBg = intval($update_colors[2]);
      $ribbonGroupBorder = intval($update_colors[3]);
      $textUnread = intval($update_colors[4]);
      $textHover = intval($update_colors[5]);
      $textRead = intval($update_colors[6]);
      $textTitles = intval($update_colors[7]);
      // Sanity check
      if ($ribbonColor + $ribbonBackground > 0 && $ribbonIconBg + $ribbonGroupBorder > 0) {
        $query = "UPDATE confa_users set color_ribbon='" . $ribbonColor . "', color_ribbon_background='" . $ribbonBackground . "', color_icon_hover='" . $ribbonIconBg . "', color_group_border='" . $ribbonGroupBorder . "', color_topics_unread='" . $textUnread . "', color_topics_hover='". $textHover . "', color_topics_visited='" . $textRead . "', color_titles='" . $textTitles . "' where id=" . $user_id;
        //die($query);
        $result = mysql_query($query);
        if (!$result) {
          mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
          die('Query failed');
        }
      }
    }

    if (array_key_exists("ignored", $_POST)) {
      $ignored = $_POST['ignored'];
      mysql_log(__FILE__, "" . __LINE__);
      while (list($key, $val) = each($ignored)) {
        $query = "INSERT INTO confa_ignor(ignored_by, ignored) values(" . $user_id . ", " . intval(trim($val)) . ")";
        $result = mysql_query($query);
        if (!$result) {
          mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
          die('Query failed');
        }
      }
    }
  $text = "Successfully updated.";
  $status = 201;
} else {
  $text = "Failed";
}

header("HTTP/1.0 " . $status );
header("Content-type	text/plain; charset=UTF-8");

print($text);
?>


