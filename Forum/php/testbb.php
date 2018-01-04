<?php
/*$Id: post.php 986 2014-01-01 20:42:58Z dmitriy $*/
/*
if(!extension_loaded('fastbbcode')) {
    dl('fastbbcode.' . PHP_SHLIB_SUFFIX);
}
*/
require_once('head_inc.php');
require_once('html_head_inc.php');
//require_once('dump.php'); // 199.34.127.57
// print('ID='. $msg_id);
// $msg_id='445289';
  if (is_null($msg_id)) die("Specify message ID");
// 1 retrieve and print as is
  $query = 'SELECT * from confa_posts where id = ' . $msg_id;
  // $query = "alter table confa_users add last_pm_check_time timestamp default '0000-00-00 00:00:00'";
  // $query = "update confa_users set last_pm_check_time = CURRENT_TIMEstamp";
  $result = mysql_query($query);
  if (!$result) {
      mysql_log(__FILE__, 'Query page count failed: ' . mysql_error() . ' QUERY: ' . $query);
      die('Query failed.' . mysql_error() . ' QUERY: ' . $query);
  }
  while ($row = mysql_fetch_assoc($result)) {
    $body = $row['body'];
    print("Original:<br/>".$body);
    
    $body = preg_replace("#\[render=([^\]]*?)\](.*?)\[\/render\]#is", "[div]$2[/div]", $body);
    print("<br/><br/><b>after_divs:</b><br/>".$body);
    $body = before_bbcode($body);
    print("<br/><br/><b>before_bbcode:</b><br/>".$body);
    $body = do_bbcode ( $body );
    print("<br/><br/><b>do_bbcode:</b><br/>".$body);
    $body = nl2br($body);
    print("<br/><br/><b>nl2br:</b><br/>".$body);
    $body = after_bbcode($body);
    print("<br/><br/><b>after_bbcode:</b><br/>".$body);
  
  // bbcode, print
  // after bbcode, print
  }

  print("<br/>The end");
require_once('tail_inc.php');
?>


