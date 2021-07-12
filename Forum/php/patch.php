<?php
require_once('head_inc.php');
require_once('html_head_inc.php');
//require_once('dump.php'); // 199.34.127.57
// print('ID='. $msg_id);
// $msg_id='445289';
  if (is_null($msg_id)) die("Specify message ID");
  if (!isset($user_id)) die("Please login");
// 1 retrieve and print as is
  $query = 'SELECT * from confa_posts where id = ' . $msg_id;
  // $query = "alter table confa_users add last_pm_check_time timestamp default '0000-00-00 00:00:00'";
  // $query = "update confa_users set last_pm_check_time = CURRENT_TIMEstamp";
  $result = mysql_query($query);
  if (!$result) {
      mysql_log(__FILE__, 'Query page count failed: ' . mysql_error() . ' QUERY: ' . $query);
      die('Query failed.' . mysql_error() . ' QUERY: ' . $query);
  }
  print("Mode: " . $mode."<br/>");
  while ($row = mysql_fetch_assoc($result)) {
    $body = $row['body'];
    print("Original:<br/>".nl2br($body));
    
    $body = render_for_editing($body);
    print("<br/><br/><b>Restored:</b><br/>".nl2br($body));
    
    // new content
    $body = render_for_db($body);
    print("<br/><br/><b>To be saved:</b><br/>".nl2br($body));
    
    // save here
    if (isset($mode) && $mode == "save") {
      $ibody = '\'' . mysql_real_escape_string($body) . '\'';
      $query = 'UPDATE confa_posts SET body=' . $ibody . ' WHERE id=' . $msg_id;
      $result2 = mysql_query($query);
      if (!$result2) {
        mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        return 'Query failed';
      } else {
        print("<br/><br/>MESSAGE SAVED");        
      }      
    }
    
    $body = render_for_display($body, true, $user, $user_id, $msg_id);
    print("<br/><br/><b>Result:</b><br/>".$body);  
  }

  print("<br/><br/>The end");
require_once('tail_inc.php');
?>


