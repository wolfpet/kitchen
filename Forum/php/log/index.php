<?php
use Phalcon\Mvc\Micro, 
    Phalcon\Events\Manager as EventsManager;
use Phalcon\Http\Response;

require_once('head_inc.php');

// Create a events manager
$eventManager = new EventsManager();

// Listen all the application events
$eventManager->attach('micro', function($event, $app) {
  global $err_login;
  
  if ($event->getType() == 'beforeExecuteRoute') {
    // authenticate user
    $user = $app->request->getServer('PHP_AUTH_USER');
    $password = $app->request->getServer('PHP_AUTH_PW');  

    if (!is_null($user) || !is_null($password)) {
      // Credentials are sent: perform the login 
      if (!login($user, $password, false)) {
        // Invalid credentials: set correct status
        $app->response->setStatusCode(403, is_null($err_login) ? 'Authentication error' : $err_login)->sendHeaders();
        // and exit
        return false;
      } else {
        // success, do nothing
      }      
    } else {
      // if cookies are set, they would have already been handled by auth.php
    }

    // Return false to stop the operation
    return true;
  }
});

$app = new Micro();

// Bind the events manager to the app
$app->setEventsManager($eventManager);

$app->get('/', function() use ($app) {
  echo file_get_contents('index.html');
});

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
  
  $response = new Response();
      
  if (is_null($count))
    $data = api_get_threads($id);
  else {
    $count = intval($count);
    if ($count > 0 && $count <= 1000) {
      $data = api_get_threads($id, $count - 1);   // -1, because query is inclusive
    } else {
      $response->setStatusCode(400, 'Error')->sendHeaders();
      $data = array('status' => 'ERROR', 'messages' => array('Invalid parameter value ' . $count));
    }
  }
  
  $response->setContentType('application/json');
  $response->setJsonContent($data);
  
  return $response;  
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
    
    $response->setContentType('application/json');
    $response->setJsonContent( array(
      'id'       => intval($msgs[$id]['thread_id']),
      'closed'   => filter_var($msgs[$id]['t_closed'], FILTER_VALIDATE_BOOLEAN),
      'message'  => $array[0]
    ));
    
  } else {
    $response->setStatusCode(404, 'Not Found')->sendHeaders();
    $response->setContentType('application/json');
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

  return array(
    'count' => $count,
    'threads' => $threads
  );
}

function api_get_body($body, $status=1) {
  if ($status == 3) {
    $msgbody = '<font color="red">censored</font>';
  } else if ($status == 2) {
    $msgbody = '';  // message deleted
  } else {
    $msgbody = render_for_display($body);
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
 *
 * Returns one message
 */

$app->get('/api/messages/{id:[0-9]+}', function($msg_id) {
  global $prop_tz, $server_tz;
  
  $response = new Response();

  // update views
  $query = 'UPDATE confa_posts set views=views + 1 where id=' . $msg_id;
  $result = mysql_query($query);
  if (!$result || $result == 0) {
    
    $response->setStatusCode(404, 'Not Found')->sendHeaders();
    $response->setContentType('application/json');
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
    $response->setContentType('application/json');
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
    $response->setContentType('application/json');    
    $response->setJsonContent(array('status' => 'ERROR', 'messages' => array(mysql_error())));

    return $response;
  }

  if (mysql_num_rows($result) != 0) {
    $row = mysql_fetch_assoc($result);

    $response->setContentType('application/json');    
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
    $response->setContentType('application/json');
    $response->setJsonContent(array('status' => 'ERROR', 'messages' => array('Message not found')));
  }

  return $response;
});

/**
 * GET /messages/$id/answers
 */
$app->get('/api/messages/{id:[0-9]+}/answers', function($msg_id) {
  global $prop_tz, $server_tz;
  
  $response = new Response();

  $query = 'SELECT u.username, u.moder, p.auth, p.parent, p.closed as post_closed, p.views, p.likes, p.dislikes, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\')  as created, p.subject, p.status, p.content_flags, LENGTH(IFNULL(p.body,"")) as len, p.thread_id, p.level, p.id as id, p.level, p.chars, p.page, (select count(*) from confa_posts where parent = p.id) as counter '
    .' from confa_posts p, confa_users u where p.author=u.id and p.parent = ' . $msg_id . ' order by id desc';
    
  $result = mysql_query($query);

  if (!$result) {
    
    $response->setStatusCode(400, 'Error');
    
    $response->setContentType('application/json');
    $response->setJsonContent(array('status' => 'ERROR', 'messages' => array(mysql_error())));
    
    return $response;
  }

  $messages = array();
  $count = 0;
  
  while ($row = mysql_fetch_assoc($result)) {
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
      'answers' => intval($row['counter']),
      'level' => intval($row['level'])
    );
    $count++;
  }

  $response->setContentType('application/json');
  $response->setJsonContent(array('count' => $count,'messages' => $messages));
  
  return $response;
});

/**
 * GET /api/messages?mode=<bydate|mymessages|answered>&count=50&id=<max_msg_id>
 *
 * Returns "Collapsed threads" view data, also a default view for mobile client. optional arguments - $max_thread_id, $count
 */
$app->get('/api/messages', function() use ($app) {
  global $prop_tz, $server_tz, $root_dir, $host, $user_id;
  
  $response = new Response();
  
  $mode = $app->request->getQuery('mode');  

  if (is_null($mode)) {
    $mode = 'bydate';
  }
  
  $count = $app->request->getQuery('count');
  
  if (!is_null($count)) {
    $count = intval($count);
  }

  $max_id = $app->request->getQuery('id');

  if (is_null($max_id)) {
    $max_id = -1;
  } else {
    $max_id = intval($max_id);
  }
  
  switch ($mode) {
    
    case 'bydate':
      if (is_null($count)) {
        $count = 30;
      }
      $query = 'SELECT u.username, u.moder, u.ban_ends, p.auth, p.closed as post_closed, p.views, p.likes, p.dislikes, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz 
        . ':00\') as created, p.subject, p.author as author, p.status, p.id as id, p.chars, p.content_flags, p.parent, p.level, p.page, (select count(*) from confa_posts where parent = p.id) as counter from confa_posts p, confa_users u' 
        . ' where p.author=u.id' . ($max_id > 0 ? (' and p.id <= ' . $max_id) : '') . ' and p.status != 2 order by id desc limit ' . $count;
      $result = mysql_query($query);
      break;
        
    case 'answered':
      $result = get_answered(is_null($count) ? 0 : $count);  
      break;
      
    case 'mymessages':
      if (is_null($count)) {
        $count = 50;
      }
      $query = 'SELECT u.username, u.moder, p.auth, p.closed as post_closed, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz 
        . ':00\') as created, p.subject, p.content_flags, p.views, p.likes, p.dislikes, p.status, p.id as id, p.page, p.parent, p.level, p.chars, (select count(*) from confa_posts where parent = p.id) as counter from confa_posts p, confa_users u where p.author=' 
        . $user_id . ' and p.author=u.id and  p.status != 2 ' . ($max_id > 0 ? (' and p.id <= ' . $max_id) : '') . ' order by id desc limit ' . $count; 

      $result = mysql_query($query);
      break;
    
    default:
      $response = new Response();
      
      $response->setStatusCode(400, 'Error')->sendHeaders();
      $response->setContentType('application/json');
      $response->setJsonContent(array('status' => 'ERROR', 'messages' => array('Invalid parameter value: ' . $mode)));
      
      return $response;
  }

  if (!$result) {
      mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query . 'max_id="' . $max_id . '"');
      die('Query failed ' . mysql_error() . ' QUERY: ' . $query );
  }

  $messages = array();
  $count = 0;
  
  while ($row = mysql_fetch_assoc($result)) {
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
      'answers' => intval($row['counter']),
      'level' => intval($row['level'])
    );
    $count++;
    if ($mode == 'answered' && $count == 1) {
      setcookie('last_answered_id2', $row['id'], 1800000000, $root_dir, $host);
    }      
  }

  $response->setContentType('application/json');
  $response->setJsonContent(array('count' => $count,'messages' => $messages));
  
  return $response;  
});

/**
 * GET /api/profile
 *
 * Returns user profile and session ID (in headers)
 */
$app->get('/api/profile', function() use ($app) {
  global $logged_in;
  global $err_login;

  global $user_id;
  global $ban;
  global $ban_ends;
  global $new_pm;
  global $prop_bold;
  global $prop_tz;
  global $ban_time;
  global $user;
  
  $response = new Response();
  
  if ($logged_in) {
    $profile = array(
      'id' => intval($user_id),
      'name' => $user,
      'new_pm' => intval($new_pm),
      'banned' => filter_var($ban, FILTER_VALIDATE_BOOLEAN),
      'ban_ends' => $ban_ends,
      'ban_time' => $ban_time,
      'prop_bold' => filter_var($prop_bold, FILTER_VALIDATE_BOOLEAN),
      'prop_tz' => intval($prop_tz)
    );
    $response->setContentType('application/json');
    $response->setJsonContent($profile);
  } else {
    $response->setStatusCode(403, 'Authentication error');
    $response->setContentType('application/json');
    $response->setJsonContent(array('status' => 'ERROR', 'messages' => array( is_null($err_login) ? "User not logged in" : $err_login)));
  }

  return $response;
});

/**********************************************************
 * Updates
 **********************************************************/
 
/**
 * PUT /messages/$id/like
 */
$app->put('/api/messages/{id:[0-9]+}/like', function($msg_id) {
  global $logged_in, $user_id, $err_login;
  
  $response = new Response();

  if (!$logged_in) {
    $response->setStatusCode(403, 'Authentication error');
    $response->setContentType('application/json');
    $response->setJsonContent(array('status' => 'ERROR', 'messages' => array( is_null($err_login) ? "User not logged in" : $err_login)));
    
    return $response;
  }
  
  $new_value = like($user_id, $msg_id, 1);
  
  if ($new_value === false) {
    // error
    $response->setStatusCode(400, 'Error');
    $response->setJsonContent(array('status' => 'ERROR', 'messages' => array(mysql_error())));
  } else {
    $response->setJsonContent(array('value' => intval($new_value)));
  }

  $response->setContentType('application/json');
  
  return $response;
});

/**
 * DELETE /messages/$id/like
 */
$app->delete('/api/messages/{id:[0-9]+}/like', function($msg_id) {
  global $logged_in, $user_id, $err_login;
  
  $response = new Response();

  if (!$logged_in) {
    
    $response->setStatusCode(403, 'Authentication error');
    $response->setContentType('application/json');
    $response->setJsonContent(array('status' => 'ERROR', 'messages' => array( is_null($err_login) ? "User not logged in" : $err_login)));
    
    return $response;
  }
  
  $new_value = like($user_id, $msg_id, -1);
  
  if ($new_value === false) {
    // error
    $response->setStatusCode(400, 'Error');
    $response->setJsonContent(array('status' => 'ERROR', 'messages' => array(mysql_error())));
  } else {
    $response->setJsonContent(array('value' => intval($new_value)));
  }

  $response->setContentType('application/json');
  
  return $response;
});

/**
 * PUT /messages/$id/bookmark
 */
$app->put('/api/messages/{id:[0-9]+}/bookmark', function($msg_id) {
  global $logged_in, $user_id, $err_login;
  
  $response = new Response();

  if (!$logged_in) {
    $response->setStatusCode(403, 'Authentication error');
    $response->setContentType('application/json');
    $response->setJsonContent(array('status' => 'ERROR', 'messages' => array( is_null($err_login) ? "User not logged in" : $err_login)));
    
    return $response;
  }
  
  $new_value = bookmark($user_id, $msg_id);
  
  if ($new_value === false) {
    $response->setStatusCode(400, 'Error');
    $response->setContentType('application/json');
    $response->setJsonContent(array('status' => 'ERROR', 'messages' => array(mysql_error())));
  }
  
  return $response;
});

/**
 * DELETE /messages/$id/bookmark
 */
$app->delete('/api/messages/{id:[0-9]+}/bookmark', function($msg_id) {
  global $logged_in, $user_id, $err_login;
  
  $response = new Response();

  if (!$logged_in) {
    
    $response->setStatusCode(403, 'Authentication error');
    $response->setContentType('application/json');
    $response->setJsonContent(array('status' => 'ERROR', 'messages' => array( is_null($err_login) ? "User not logged in" : $err_login)));
    
    return $response;
  }
  
  $new_value = bookmark($user_id, $msg_id, false);
  
  if ($new_value === false) {
    $response->setStatusCode(400, 'Error');
    $response->setContentType('application/json');
    $response->setJsonContent(array('status' => 'ERROR', 'messages' => array(mysql_error())));
  }
  
  return $response;
});

/**
 * POST /threads
 */
$app->post('/api/threads', function() use ($app) {
  return api_post($app, 0, 0);
});

/**
 * POST /messages/$id/answers
 */
$app->post('/api/messages/{id:[0-9]+}/answers', function($re) use ($app) {
  return api_post($app, intval($re), 0);
});

/**
 * PUT /messages/$id
 */
$app->put('/api/messages/{id:[0-9]+}', function($msg_id) use ($app) {
  return api_post($app, 0, intval($msg_id));
});

function api_post($app, $re, $msg_id) {
  global $logged_in, $user_id, $err_login;
  
  $response = new Response();

  if (!$logged_in) {
    
    $response->setStatusCode(403, 'Authentication error');
    $response->setContentType('application/json');
    $response->setJsonContent(array('status' => 'ERROR', 'messages' => array( is_null($err_login) ? "User not logged in" : $err_login)));
    
    return $response;
  }

  // retrieve the parameters: 
  $msg = $app->request->getJsonRawBody();
  
  // mandatory
  $subj = $msg->subject;
  $body = $msg->body;
  
  // optional
  $nsfw = $msg->nsfw;
  $ticket = $msg->ticket;
  
  if ($ticket == null) $ticket = "";
  if ($nsfw == null) $nsfw = false;
  
  $validation_error = validate($subj, $body);
  if (strlen($validation_error) > 0) {
    
    $response->setStatusCode(404, $validation_error);
    $response->setContentType('application/json');
    $response->setJsonContent(array('status' => 'ERROR', 'messages' => array($validation_error)));
    
    return $response;
  }
  
  $result = post($subj, $body, $re, $msg_id, $ticket, $nsfw);
  
  if (is_string($result)) {
    $response->setStatusCode(400, $result);
    $response->setContentType('application/json');
    $response->setJsonContent(array('status' => 'ERROR', 'messages' => array($result)));
  } else {
    $response->setContentType('application/json');
    $response->setJsonContent($result);
  }
    
  return $response;
}

/*******************  
    Private Mail
 *******************/

$app->get('/api/inbox', function() use ($app) {
  return api_pmail_list($app);
});

$app->get('/api/inbox/{id:[0-9]+}', function($msg_id) use ($app) {
  return api_pmail($app, $msg_id);
});

$app->get('/api/sent', function() use ($app) {
  return api_pmail_list($app, false);
});

$app->get('/api/sent/{id:[0-9]+}', function($msg_id) use ($app) {
  return api_pmail($app, $msg_id);
});

$app->post('/api/sent', function() use ($app) {
});

function api_pmail_list($app, $inbox=true) {
  global $prop_tz, $server_tz, $user_id, $pm_deleted_by_receiver, $pm_deleted_by_sender;
  
  $response = new Response();
    
  $count = $app->request->getQuery('count');
  
  if (!is_null($count)) {
    $count = intval($count);
  } else {
    $count = 20;
  }

  $max_id = $app->request->getQuery('id');

  if (is_null($max_id)) {
    $max_id = -1;
  } else {
    $max_id = intval($max_id);
  }
  
  if ($inbox) {
    $search_condition = 'receiver=' . $user_id . ' and !(p.status &'.$pm_deleted_by_receiver.')'; 
  } else {
    $search_condition = 'sender=' . $user_id . ' and !(p.status & '.$pm_deleted_by_sender.')'; 
  }

  $query = 'SELECT s.username, p.id as id, p.sender, p.receiver, p.subject, p.body, '
  . 'CONVERT_TZ(p.created, \'' . $server_tz . '\', \''.$prop_tz.':00\') as created, p.status, p.chars from confa_pm p, confa_users s where p.sender=s.id and '
  . $search_condition . ($max_id > 0 ? (' and p.id <= ' . $max_id) : '') . ' order by id desc limit ' . $count;

  $result = mysql_query($query);

  if (!$result) {
      mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query . ' max_id="' . $max_id . '"');
      die('Query failed ' . mysql_error() . ' QUERY: ' . $query );
  }

  $messages = array();
  $count = 0;
  
  while ($row = mysql_fetch_assoc($result)) {
    $messages[] = array(
      'id' => intval($row['id']),
      'status' => intval($row['status']),
      'subject' => api_get_subject($row['subject'], $row['status']),
      ($inbox ? 'author' : 'recipient') => array('id'  => intval($row[ $inbox ? 'sender' : 'receiver']), 'name' => $row['username']),
      'created' => $row['created'],
      'chars' => intval($row['chars'])
    );
    $count++;
  }

  $response->setContentType('application/json');
  $response->setJsonContent(array('count' => $count,'messages' => $messages));
  
  return $response;  
}

function api_pmail($app, $msg_id) {
  global $prop_tz, $server_tz, $user_id, $pm_deleted_by_receiver, $pm_deleted_by_sender;
  
  $response = new Response();
    
  $query = 'SELECT s.username as author, ss.username as recipient, p.subject, p.id as msg_id, p.sender, p.receiver,  CONVERT_TZ(p.created, \'' . $server_tz . 
    '\', \''.$prop_tz.':00\') as created, p.body, s.id as sid, ss.id as rid, p.status, p.chars from confa_users s, confa_users ss, confa_pm p where s.id=p.sender and ss.id=p.receiver and p.id=' . $msg_id . ' and '. 
    '(p.sender='.$user_id.' and !(p.status & '.$pm_deleted_by_sender.') or p.receiver='.$user_id.' and !(p.status & '.$pm_deleted_by_receiver.'))';

  $result = mysql_query($query);

  if (!$result) {
    
    $response->setStatusCode(400, 'Error');
    $response->setContentType('application/json');    
    $response->setJsonContent(array('status' => 'ERROR', 'messages' => array(mysql_error())));

    return $response;
  }

  if (mysql_num_rows($result) != 0) {
    $row = mysql_fetch_assoc($result);

    $response->setContentType('application/json');    
    $response->setJsonContent(array(
      'id' => intval($row['msg_id']),
      'status' => intval($row['status']),
      'subject' => api_get_subject($row['subject']),
      'author' => array('id'  => intval($row['sid']), 'name' => $row['author']),
      'recipient' => array('id'  => intval($row['rid']), 'name' => $row['recipient']),
      'created' => $row['created'],
      'body' => array('html' => api_get_body($row['body']))
    ));
      
  } else {
    $response->setStatusCode(404, 'Not Found')->sendHeaders();
    $response->setContentType('application/json');
    $response->setJsonContent(array('status' => 'ERROR', 'messages' => array('Message not found')));
  }
  
  return $response;  
}

$app->notFound(
    function () use ($app) {
        // echo 'Not found!';
        $app->response->redirect("index.html")->sendHeaders();
        //$app->response->setStatusCode(404, "Not Found")->sendHeaders();
        //echo 'This page was not found!. Please, use for now http://kirdyk.radier.ca/bydate.php?page=1';        
    }
);

$app->handle();

require_once('tail_inc.php');
?>