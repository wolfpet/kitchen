<?php
require_once('dump.php');
require_once('head_inc.php');

if($user_id==null)die('unauthorized');

$action = $_GET['action'];
if($action == 'vote')
{
    $questionID = $_GET['questionId'];
    $answerId = $_GET['answerId'];
    $query = 'INSERT INTO confa_polls(type, owner_id,  question_id, answer_id) values(2, '.$user_id.', '.$questionID.','.$answerId.')';
    $result = mysql_query($query);
    if (!$result) { die('Query failed'); }
    echo('voted');
    return;
}

if($action == 'unvote')
{
    $questionID = $_GET['questionId'];
    $answerId = $_GET['answerId'];
    $query = 'delete from confa_polls where owner_id='.$user_id.' and question_id='.$questionID.' and answer_id='.$answerId;
    $result = mysql_query($query);
    echo('unvoted');
    return;
}

if($action == 'whoVoted')
{
    $voters = array();
    $answerId = $_GET['answerId'];
    $query = 'select s.username as user, p.owner_id from confa_polls p, confa_users s where p.owner_id = s.id and answer_id='. $answerId;
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
           $voter = $row['user'];
           array_push($voters, $voter);
    }
    header('Content-type:application/json;charset=utf-8');
    echo json_encode($voters);
    $status = 201;
}
?>