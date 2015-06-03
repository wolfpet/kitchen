<?php
/*$Id: mysql_log.php 381 2009-11-02 20:25:46Z dmitriy $*/

function mysql_log($page, $msg) {
    $time = strftime('%Y-%m-%d %H:%M:%S') . "\n";
  
    $log_name = "log/mysql-" .  date("Y-m-d") . ".log"; 
    $fp=fopen( $log_name, 'a' ); 
    if (flock($fp, LOCK_EX)) {
        fputs($fp, $time);
        fputs($fp, $page . ': ' .  $msg . "\n"); 
        fputs($fp, "============\n"); 
        flock($fp, LOCK_UN);
    }
    fclose($fp); 
}

?>


