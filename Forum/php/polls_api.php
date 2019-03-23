<?php
require_once('dump.php');
require_once('mysql_log.php');
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

function can_vote($user_id) {
    $query = 'select count(*) as cnt from confa_posts where author=' . $user_id;
    $result = mysql_query($query);
    
    if ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
      mysql_log('polls_api', $user_id . ' created ' . $row['cnt'] . ' messages');
      return intval($row['cnt']) > 10; // wrote more than 10 messages
    }
    
    $query = 'select created from confa_users where id=' . $user_id;
    $result = mysql_query($query);
    
    if ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
      $d1 = new DateTime();
      $d2 = new DateTime($row['created']);
      $diff = $d2->diff($d1);
      mysql_log('polls_api', $user_id . ' was created ' . $diff->y . ' years ago, ' . $row['created']);
      return $diff->y > 3;      // or registered > 3 years ago
    }
    
    mysql_log('polls_api', $user_id . ' is not allowed to vote in anonymous polls');    
    return false;  
}

$action = $_GET['action'];
if ($action == 'vote')
{
    $questionId = intval($_GET['questionId']);
    $answerId = intval($_GET['answerId']);
    
    if (is_poll_anonymous($answerId) && !can_vote($user_id)) {
      http_response_code(403);
      return;      
    }
    
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