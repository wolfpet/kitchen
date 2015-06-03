<?php
require_once('dump.php');
require_once('head_inc.php');

header("Location: http://$host$root_dir$page_pay_ban?action=cancelled",TRUE,302); 
die("Redirected: http://$host$root_dir$page_pay_ban");

require_once('tail_inc.php');

?>

