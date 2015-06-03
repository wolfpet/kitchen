<?php

date_default_timezone_set('America/New_York');

$server = "SERVER: ";
$post = "POST: ";
$get = "GET: ";
$cookie = "COOKIE: ";

if ( isset($_SERVER) && is_array($_SERVER) ) { 
  $server .= serialize($_SERVER);
}
if ( isset($_POST) && is_array($_POST) ) { 
  $post .= serialize($_POST);
}
if ( isset($_GET) && is_array($_GET) ) { 
  $get .= serialize($_GET);
}
if ( isset($_COOKIE) && is_array($_COOKIE) ) { 
  $cookie .= serialize($_COOKIE);
}
  
$server .= "\n";
$post .= "\n";
$get .= "\n";
$cookie .= "\n";
$time = strftime('%Y-%m-%d %H:%M:%S') . "\n";

$log_name = "log/requests-" .  date("Y-m-d") . ".log"; 
$fp=fopen( $log_name, 'a' ); 
if (flock($fp, LOCK_EX)) {
  fputs($fp, $time);
  fputs($fp, $server); 
  fputs($fp, $cookie); 
  fputs($fp, $post); 
  fputs($fp, $get); 
  fputs($fp, "============\n"); 
  flock($fp, LOCK_UN);
}
fclose($fp); 

?>


