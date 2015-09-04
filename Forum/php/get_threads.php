<?php
/*$Id: top.php 944 2013-09-02 00:46:16Z dmitriy $*/

require_once('head_inc.php');
require_once('mysql_log.php');

if ( isset($msg_id) ) {
  
  get_show_hidden_and_ignored();
  
  $result = null;
  $last_thread = $msg_id;
  $content = array();
  $collapsed = isset($custom) && $custom == 'yes';
  
  if ($collapsed) {
    $limit = 50; // 50
    $min_thread = $last_thread - $limit;
    $result = get_thread_starts($min_thread, $last_thread-1);
  } else {
    $limit = isset($custom) && is_numeric($custom) ? intval($custom) : 100; // 100
    $result = get_threads_ex($limit, $last_thread);
  }

  $msgs = print_threads_ex($result, $content, $last_thread, $limit, $collapsed);
  
  print("id=${last_thread}");
  print_msgs($content, $msgs);
}

require_once('tail_inc.php');
?>