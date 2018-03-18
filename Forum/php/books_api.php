<?php

require_once('dump.php');
require_once('head_inc.php');

//if($user_id==null)die('__unauthorized__');

$message_id  = $_POST['msg'];
$user_id = $_POST['user'];

postBook($user_id, $message_id); 
require_once('tail_inc.php');


// ---------- API Functions ---------------
function postBook($user_id, $message_id)
{

    //check if already there
    $query ='select msg_id from confa_books where msg_id=' . $message_id;
    $result = mysql_query($query);
    if (!$result) { die('Query failed'); }
    $num_rows=0;

    while ($row = mysql_fetch_assoc($result))
    {
     $num_rows++;
    }

    if($num_rows < 1)
    {
    //add 
    $query = 'INSERT INTO confa_books(added_by_id, msg_id) values(' . $user_id . ', ' . $message_id . ')';
    $result = mysql_query($query);
    if (!$result) { die('Query failed'); }
    $msg_id = mysql_insert_id();
    echo("thank you. Book added. ID-".$msg_id);
    }
    else
    {
    echo("already exists");
    }
}

?>

