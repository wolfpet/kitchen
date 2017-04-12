<?php

require_once('dump.php');
require_once('head_inc.php');

if($user_id==null)die('unauthorized');

//post new pm?
$body  = $_POST['body'];

if($body !=null) 
{
    $receiver = $_POST['receiver'];
    postPM($receiver, $user_id, $body); 
    return;
}

//delete pm?
$action = $_GET['action'];
if($action == 'del')
{
    $id = $_GET['id'];
    deletePM($id, $user_id);
    return;
}

//return latest messages?
if($action == 'ping')
{
    $senderid = $_GET['senderid'];
    $lastMsgId = $_GET['lastMsgId'];
    getLatestPMs($senderid, $user_id, $lastMsgId);
    return;
}

//apparently none of the above. Return the full PMs list 
$sender = $_GET['senderid'];
$receiver = $user_id;
getConvo($sender, $receiver);
require_once('tail_inc.php');



// ---------- PM2 API Functions ---------------

function getLatestPMs($senderid, $user_id, $lastMsgId)
{
    if($senderid == 0)
    {
	//the user hasn't yet opened any convos. return the last message ID and exit
	$query = 'select id, receiver from confa_pm where receiver='.$user_id.' order by id desc';
	$result = mysql_query($query);
	//die($query);
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$id = array($row['id']);
	header('Content-type:application/json;charset=utf-8');
	echo json_encode($id);
	$status = 201;
	return;
    }
    $query = 'select sender, receiver, id, subject, body, created, status  from confa_pm where sender='.$senderid.' and receiver='.$user_id.' and id>'.$lastMsgId.' order by id desc';
    $result = mysql_query($query);
    $PMs = array();
    $me = $user_id;
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $id = $row['id'];
        $senderid = $row['sender'];
        $receiverid = $row['receiver'];
        $subj = $row['subject'];
        $body =  cleanupPM($row['body']);
        $pmTime = $row['created'];
        $status = $row['status'];
        //check the status before pushing to the viewer
        $add = true;
        if($status == 30)$add=false; //both sides deleted the msg
        if($senderid == $me && $status == 22)$add=false; //my message, I deleted. do not add.
        if($receiverid == $me && $status == 28)$add=false; //message for me, I deleted. do not show either.
        if($add==true)
        {
          $pm = array($id, $senderid, $receiverid, $subj, $body, $pmTime, $status);
          array_push($PMs, $pm);
        }
        //set new status as delivered
        if($status == 1)
        {
            //brand new message
            if($senderid == $me)
            {
              //my new message - set 17
              setStatus($id, 17);update_new_pm_count($receiver);
            }
            else
            {
               //I received it - set 20
               setStatus($id, 20);
            }
        }
        if($status == 17)
        {
            //sender already saw
            if($receiverid == $me)
            {
               //I received it - set 20
               setStatus($id, 20);
            }
        }

    }

    header('Content-type:application/json;charset=utf-8');
    //unset($PMs[0]);//removing the null
    echo json_encode($PMs);
    $status = 201;
}

function deletePM($id, $user_id)
{
    //if my messages then status 22, else status 28
    $query = 'select sender, status from confa_pm where id=' . $id;
    $result = mysql_query($query);
    $row = mysql_fetch_array($result, MYSQL_ASSOC);
    $senderid = $row['sender'];
    $status = $row['status'];
    if($senderid==$user_id)
    {
	if($status == 28)
	{
	    //receiver already deleted it set to 30
            $query = 'UPDATE confa_pm set status = 30 where id =' . $id;
            $result = mysql_query($query);
	}
	else
	{
	    //my msg - status 22
            $query = 'UPDATE confa_pm set status = 22 where id =' . $id;
            $result = mysql_query($query);
        }
    }
    else
    {
	if($status == 22)
	{
	    //sender already deleted it so set to 30
	    $query = 'UPDATE confa_pm set status = 30 where id =' . $id;
	    $result = mysql_query($query);

	}
	else
	{
	//not mine - status 28
	    $query = 'UPDATE confa_pm set status = 28 where id =' . $id;
	    $result = mysql_query($query);
	}
    }
    
    
}
function cleanupPM($str)
{
   //html tags
    $str = preg_replace_callback('#\<(.*)\>#i',
    function ($matches) {
          return "[ ".html_entity_decode($matches[1])."][/]";
              }, $str);

   // Deal with quotes
   $format_search =  array(
   '#\[quote(?!.*\[quote)\](.*?)\[/quote\]\s*#is', // Quote ([quote]text[/quote])
   '#\[quote(?!.*\[quote)=(.*?)\](.*?)\[/quote\]\s*#is', // Quote with author ([quote=author]text[/quote])
 );
   // The matching array of strings to replace matches with
   $format_replace = array(
   '<i>$1</i><br/>',
   '<i><b>$1:</b>$2</i><br/>',
 );
   // Perform the actual quotes conversion
   $count = 1;
    while ($count > 0) {
    $str = preg_replace($format_search, $format_replace, $str, -1, $count);
   }
   return $str;
}
function getConvo($sender, $receiver)
{
    global $prop_tz;
    global $server_tz;
    global $user_id;

    $query = 'SELECT p.id, p.sender, p.receiver, p.subject, p.body, 
    CONVERT_TZ(p.created, \'' . $server_tz . '\', \''.$prop_tz.':00\') as created,  p.status as status,  p.chars  
    from confa_pm p where ((p.sender='.$sender.' AND p.receiver='.$receiver.') OR (p.sender='.$receiver.' AND p.receiver='.$sender.')) '
    . 'order by id';
    //die($query);
    $result = mysql_query($query);
    if (!$result) {
        mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed ' );
    }

    $num = 1;
    $out = '';

    if (mysql_num_rows($result) == 0) {
        $max_id = $last_id;
    }
    $auth_text = '';
    $PMs = array();
    $me = $user_id;
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $id = $row['id'];
        $senderid = $row['sender'];
        $receiverid = $row['receiver'];
        $subj = $row['subject'];
        $body =  cleanupPM($row['body']);
        $pmTime = $row['created'];
        $status = $row['status'];
        //check the status before pushing to the viewer
        $add = true;
        if($status == 30)$add=false; //both sides deleted the msg
        if($senderid == $me && $status == 22)$add=false; //my message, I deleted. do not add.
        if($receiverid == $me && $status == 28)$add=false; //message for me, I deleted. do not show either.
        if($add==true)
        {
          $pm = array($id, $senderid, $receiverid, $subj, $body, $pmTime, $status);
          array_push($PMs, $pm);
        }
        //set new status as delivered
        if($status == 1)
        {
            //brand new message
            if($senderid == $me)
            {
              //my new message - set 17
              setStatus($id, 17);update_new_pm_count($receiver);
            }
            else
            {
               //I received it - set 20
               setStatus($id, 20);
            }
        }
        if($status == 17)
        {
            //sender already saw
            if($receiverid == $me)
            {
               //I received it - set 20
               setStatus($id, 20);
            }
        }

    }

    header('Content-type:application/json;charset=utf-8');
    //unset($PMs[0]);//removing the null
    echo json_encode($PMs);
    $status = 201;
}

function postPM($receiver, $sender, $body)
{
    if($body == ''){echo("Error: empty message"); return;}
    $body = mysql_escape_string($body);
    $chars = 0;
    if (strlen($body) != 0) {
        $chars = strlen(utf8_decode($body));
    }
    $subj='n/a';
    $query = 'INSERT INTO confa_pm(sender, receiver, subject, body, chars) values(' . $sender . ', ' . $receiver . ', \'' . mysql_escape_string($subj) . '\', \'' . $body . '\' , ' . $chars . ')';
    //die($query);
    $result = mysql_query($query);
    if (!$result) { die('Query failed'); }
    $msg_id = mysql_insert_id();
    update_new_pm_count($receiver);
     echo($msg_id);
 
}

function setStatus($id, $status)
{
    $query = 'UPDATE confa_pm set status = '. $status .' where id =' . $id;
    $result = mysql_query($query);
}

?>

