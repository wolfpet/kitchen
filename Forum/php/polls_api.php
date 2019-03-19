<?php
require_once('dump.php');
require_once('head_inc.php');

if ($user_id == null) die('unauthorized');

function is_poll_anonymous($answerId) {
    $query = 'select anon from confa_polls where type=0 and id in (select question_id from confa_polls where type=1 and id='. $answerId.')';
    $result = mysql_query($query);
    
    if ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
      return intval($row['anon']) != 0;
    }
    
    return false;
}

$action = $_GET['action'];
if ($action == 'vote')
{
    $questionId = intval($_GET['questionId']);
    $answerId = intval($_GET['answerId']);
    $query = 'INSERT INTO confa_polls(type, owner_id,  question_id, answer_id) values(2, '.$user_id.', '.$questionId.','.$answerId.')';
    
    $result = mysql_query($query);
    if (!$result) die('Query failed'); 
    
    echo('voted');
    return;
}

if($action == 'unvote')
{
    $questionId = intval($_GET['questionId']);
    $answerId = intval($_GET['answerId']);
    
    $query = 'delete from confa_polls where owner_id='.$user_id.' and type=2 and question_id='.$questionId.' and answer_id='.$answerId;
    $result = mysql_query($query);
    
    echo('unvoted');
    return;
}

if($action == 'whoVoted')
{
    $answerId = intval($_GET['answerId']);
    
    if (is_poll_anonymous($answerId)) {
      http_response_code(405);
      return;
    }
    
    $voters = array();
    
    $query = 'select s.username as user, p.owner_id from confa_polls p, confa_users s where p.owner_id = s.id and answer_id='. $answerId;
    $result = mysql_query($query);
    
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
      array_push($voters, $row['user']);
    }
    
    header('Content-type:application/json;charset=utf-8');
    echo json_encode($voters);
    $status = 201;
    return;
}
?>