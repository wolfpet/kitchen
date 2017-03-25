<?php

require_once('dump.php');
require_once('head_inc.php');

$user = $_GET['userid'];
$numberOfEvents = $_GET['number'];

$events =  recentEvents($user, $numberOfEvents);
header('Content-type:application/json;charset=utf-8');
unset($events[0]);//removing the null
echo json_encode($events);
$status = 201;

?>