<?php
/*$Id: mysql_log.php 381 2009-11-02 20:25:46Z dmitriy $*/

function mysql_log($page, $msg) {
  global $user, $user_id;
  
  $time = strftime('%Y-%m-%d %H:%M:%S') . " ";
  
	if (!file_exists("log")) {
		if (!mkdir("log", 0744)) return;
	}
  
  $log_name = "log/mysql-" .  date("Y-m-d") . ".log"; 
  error_log((isset($user) ? ($user . ' (' . $user_id . ')' ) : '') . ' ' . basename($page) . ': ' .  $msg . PHP_EOL); //, 3, $log_name);
}
?>