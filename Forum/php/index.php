<?php
use Phalcon\Mvc\Micro;
use Phalcon\Http\Response;

$app = new Micro();

require_once('head_inc.php');

// "Collapsed threads" view, also a default view for mobile client. optional arguments - $min_thread_id, $max_thread_id

$app->get('/api/threads', function() {
  api_get_threads(-1);
});

$app->get('/api/threads/{id:-?[0-9]+}', function($id) {
  api_get_threads(intval($id));
});

$app->get('/api/threads/{id:-?[0-9]+}/{count:[0-9]+}', function($id, $count) {
  api_get_threads(intval($id), intval($count));
});

function api_get_threads($max_thread_id, $count=50) {
  
  if ($max_thread_id == -1) {
    get_max_pages_collapsed($max_thread_id);
  }
  
  $min_thread_id = $max_thread_id - $count;  
  $threads = array();
  $count = 0;
  
  $result = get_thread_starts($min_thread_id, $max_thread_id);

  while ($row = mysql_fetch_assoc($result)) {
    $threads[] = array(
        'id'       => intval($row['thread_id']),
        'counter'  => intval($row['counter']),
        'closed'   => filter_var($row['thread_closed'], FILTER_VALIDATE_BOOLEAN),
        'status'   => intval($row['thread_status']),
        'message'  => array(
          'id' => intval($row['msg_id']),
          'status' => intval($row['status']),
          'subject' => api_get_subject($row['subject'], $row['status']),
          'author' => array('id'  => intval($row['user_id']), 'name' => $row['username']),
          'chars' => intval($row['chars']),
          'created' => $row['created'],
          'views' => intval($row['views']),
          'likes' => intval($row['likes']),
          'dislikes' => intval($row['dislikes'])
        )
    );
    $count++;
  } 

  $data = array(
    'count' => $count,
    'threads' => $threads
  );

  echo json_encode($data);
}

function api_get_body($body, $status=1) {
  if ($status == 3) {
    $msgbody = '<font color="red">censored</font>';
  } else if ($status == 2) {
    $msgbody = '';  // message deleted
  } else {
    $translit_done = false;
    $msgbody = translit($body, $translit_done);
    // $msgbody = htmlentities( $msgbody, HTML_ENTITIES,'UTF-8');
    $msgbody = before_bbcode($msgbody);
    $msgbody = bbcode ( $msgbody );
    $msgbody = nl2br($msgbody);
    $msgbody = after_bbcode($msgbody);
  }
  return $msgbody;
}

function api_get_subject($subject, $status=1) {
  if ($status == 2) {
    return 'This message has been deleted'; 
  } else {
    return $subject;
  }
}

/**
 * GET /messages/$id
 */

$app->get('/api/messages/{id:[0-9]+}', function($msg_id) use ($prop_tz, $server_tz) {
  $response = new Response();

  // update views
  $query = 'UPDATE confa_posts set views=views + 1 where id=' . $msg_id;
  $result = mysql_query($query);
  if (!$result || $result == 0) {
    $response->setStatusCode(404, 'Not Found')->sendHeaders();
    $response->setJsonContent(array('status' => 'ERROR', 'messages' => array('Message not found')));
    return $response;
  }

  // get likes/dislikes/readss
  $likes = 0;
  $dislikes = 0;
  $ratings = array();
  
  $query = 'SELECT u.username as userlike, l.value as valuelike from confa_users u, confa_likes l where l.user=u.id and l.post=' . $msg_id;
  $result = mysql_query($query);
  if (!$result) {
    $response->setStatusCode(400, 'Error')->sendHeaders();
    $response->setJsonContent(array('status' => 'ERROR', 'messages' => array(mysql_error())));
    return $response;
  }
  
  while($row = mysql_fetch_assoc($result)) {
      if ($row['valuelike'] > 0) {
        $likes++;
      } else if ($row['valuelike'] < 0){
        $dislikes++;
      }
      $ratings[] = array( 'name' => $row['userlike'], 'count' => intval($row['valuelike']));
  }
  mysql_free_result($result);

  // retrieve the message
  $query = 'SELECT u.username, u.moder, p.subject, p.closed as post_closed, p.views, p.id as msg_id, p.status, p.auth, p.parent, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as created, p.body, p.author, u.id as id, t.closed as thread_closed, ( select max(page) from confa_threads) - t.page + 1 as page, p.thread_id, t.id, p.status, t.author as t_author, t.properties as t_properties from confa_users u, confa_posts p, confa_threads t where p.thread_id=t.id and u.id=p.author and p.id=' . $msg_id;
  $result = mysql_query($query);
  if (!$result) {
    $response->setStatusCode(400, 'Error');
    $response->setJsonContent(array('status' => 'ERROR', 'messages' => array(mysql_error())));
    return $response;
  }

  if (mysql_num_rows($result) != 0) {
    $row = mysql_fetch_assoc($result);
    
    $response->setJsonContent(array(
      'id' => intval($row['msg_id']),
      'status' => intval($row['status']),
      'subject' => api_get_subject($row['subject'], $row['status']),
      'author' => array('id'  => intval($row['author']), 'name' => $row['username']),
      'created' => $row['created'],
      'views' => intval($row['views']),
      'likes' => $likes,
      'dislikes' => $dislikes,
      'ratings' => $ratings,
      'page' => intval($row['page']),
      'parent' => intval($row['parent']),
      'closed' => filter_var($row['post_closed'], FILTER_VALIDATE_BOOLEAN),
      'body' => array('html' => api_get_body($row['body'], $row['status']))
    ));
      
  } else {
    $response->setStatusCode(404, 'Not Found')->sendHeaders();
    $response->setJsonContent(array('status' => 'ERROR', 'messages' => array('Message not found')));
  }

  return $response;
});

/**
 * GET /messages/$id/answers
 */
$app->get('/api/messages/{id:[0-9]+}/answers', function($msg_id) use ($prop_tz, $server_tz) {
  $response = new Response();
  $response->setStatusCode(400, 'Error')->sendHeaders();
  $response->setJsonContent(array('status' => 'ERROR', 'messages' => array('Not implemented')));

  return $response;
});

/*
TODO:

GET /users/$name or $id

User profile, can be used for authentication

Data: {id:-1, token:"", ... }

GET /threads {?max_thread_id=-1&limit=-1}

"Collapsed threads" view, also a default view for mobile client. optional arguments - $min_thread_id, $max_thread_id

Data:

{count: -1, threads: [
{msg_id:-1, subject:"", created:"ts",author : {id:-1, name:""}, answers: {count : -1}, likes:-1, dislikes:-1, flags:[], permissions:[], thread_id:-1}
.... 
]}

GET /messages/$id

Return message data by ID, including content

POST /messages

Create new topic

POST /messages/$id/answers

Respond to a message

PUT /messages/$id/like

Like a message

DELETE /messages/$id/like

Dislike a message
*/

$app->notFound(
    function () use ($app) {
        $app->response->setStatusCode(404, "Not Found")->sendHeaders();
        echo 'This page was not found!';
    }
);

$app->handle();

require_once('tail_inc.php');
?>