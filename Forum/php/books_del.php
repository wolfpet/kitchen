<?php

require_once('dump.php');
require_once('head_inc.php');

//if($user_id==null)die('__unauthorized__');

$message_id  = $_POST['msg'];
$user_id = $_POST['user'];

delBook($user_id, $message_id); 
require_once('tail_inc.php');


function delBook($user_id, $message_id)
{

    $query = 'delete from confa_books where msg_id = ' . $message_id .';';
    //die($query);
    $result = mysql_query($query);
    if (!$result) { die('Query failed'); }
    echo("thank you. Book deleted.");
}

?>

