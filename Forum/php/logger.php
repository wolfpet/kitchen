<?php
/*$Id: mysql_log.php 381 2009-11-02 20:25:46Z dmitriy $*/

function loglog($page, $line, $msg) {
    global $log_enabled;
    if ($log_enabled == true) {
        $time = strftime('%Y-%m-%d %H:%M:%S') . "\n";
  
        $log_name = "log/log-" .  date("Y-m-d") . ".log"; 
        $fp=fopen( $log_name, 'a' ); 
        if (flock($fp, LOCK_EX)) {
            fputs($fp, $time);
            fputs($fp, $page . ':' . $line .  ' ' .  $msg . "\n"); 
            flock($fp, LOCK_UN);
        }
        fclose($fp); 
    }
}

?>
