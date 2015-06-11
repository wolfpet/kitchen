<?php
use Phalcon\Mvc\Micro;
use Phalcon\Http\Response;

$app = new Micro();

require_once('head_inc.php');

/**
 * GET /api/threads?id=-1&count=50
 *
 * Returns "Collapsed threads" view data, also a default view for mobile client. optional arguments - $max_thread_id, $count
 */
$app->get('/api/threads', function() use ($app) {
  
  $id = $app->request->getQuery('id');  
  if (is_null($id)) 
    $id = -1;
  else 
    $id = intval($id);
  
  $count = $app->request->getQuery('count');
  
  if (is_null($count))
    api_get_threads($id);
  else {
    $count = intval($count);
    if ($count > 0 && $count <= 1000) {
      api_get_threads($id, $count - 1);   // -1, because query is inclusive
    } else {
      $response = new Response();
      $response->setStatusCode(400, 'Error')->sendHeaders();
      $response->setJsonContent(array('status' => 'ERROR', 'messages' => array('Invalid parameter value ' . $count)));
      return $response;
    }
  }
});

/**
 * GET /api/threads/[id]
 *
 * Returns one complete thread by ID (subjects only, no bodies), the way topthread.php does them
 */
$app->get('/api/threads/{id:[0-9]+}', function($id) {
  $result = get_thread($id);

  $content = array();
  $msgs = print_thread($result, $content, function($row) {
    return $row;
  });
  
  $response = new Response();

  function print_msgs2($ar, $msgs) {
    $messages = array();    
    $keys = array_keys($ar);
    
    foreach ($keys as $key) {
      $row = $msgs[$key];
      $messages[] = array(
        'id' => intval($row['id']),
        'status' => intval($row['status']),
        'subject' => api_get_subject($row['subject'], $row['status']),
        'author' => array('id'  => intval($row['auth']), 'name' => $row['username']),
        'created' => $row['created'],
        'views' => intval($row['views']),
        'likes' => intval($row['likes']),
        'dislikes' => intval($row['dislikes']),
        'page' => intval($row['page']),
        'parent' => intval($row['parent']),
        'closed' => filter_var($row['post_closed'], FILTER_VALIDATE_BOOLEAN),
        'flags' => intval($row['content_flags']),
        'answers' => sizeof($ar[$key]) > 0 ? print_msgs2($ar[$key], $msgs) : array()
      );
    }
    
    return $messages;
  }
  
  $array = print_msgs2($content, $msgs);
  
  if (count($array) > 0) {
    $id = array_keys($msgs)[0];
    $response->setJsonContent( array(
      'id'       => intval($msgs[$id]['thread_id']),
      'closed'   => filter_var($msgs[$id]['t_closed'], FILTER_VALIDATE_BOOLEAN),
      'message'  => $array[0]
    ));
  } else {
    $response->setStatusCode(404, 'Not Found')->sendHeaders();
    $response->setJsonContent(array('status' => 'ERROR', 'messages' => array('Message not found')));
  }

  return $response;
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

$app->notFound(
    function () use ($app) {
        $app->response->redirect("index.html")->sendHeaders();
        //$app->response->setStatusCode(404, "Not Found")->sendHeaders();
        //echo 'This page was not found!. Please, use for now http://kirdyk.radier.ca/bydate.php?page=1';
    }
);

$app->handle();

require_once('tail_inc.php');
?>