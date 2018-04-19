<?php

require_once('dump.php');
require_once('head_inc.php');



$thread_id  = $_POST['thread'];
$user_id = $_POST['user'];
$action = $_POST['action'];

if($user_id==null)die('__unauthorized__');

if($action=='pin')
{
   pinThread($thread_id, $user_id);
   
   
   die();
}

//postBook($user_id, $message_id); 
require_once('tail_inc.php');


// ---------- API Functions ---------------

function pinThread($thread_id, $user_id)
{
    //check if already there
    $query ='select thread_id, owner_id from confa_pins where thread_id=' . $thread_id . ' AND owner_id=' . $user_id;
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
     $query = 'INSERT INTO confa_pins(owner_id, thread_id) values(' . $user_id . ', ' . $thread_id . ')';
     $result = mysql_query($query);
     if (!$result) { die('Query failed'); }
     $msg_id = mysql_insert_id();
     echo("thread pinned");
    }
    else
    {
     $query = 'DELETE FROM confa_pins WHERE thread_id=' . $thread_id . ' AND owner_id=' . $user_id;
     $result = mysql_query($query);
     if (!$result) { die('Query failed'); }
     echo("thread unpinned");
    }
}


?>

