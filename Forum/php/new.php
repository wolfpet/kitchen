<?php
/*$Id: new.php 988 2014-01-05 01:14:33Z dmitriy $*/

require_once('head_inc.php');
require_once('html_head_inc.php');

$thread_owner = false;
    $title = 'New message';
    $ticket = '' . ip2long(substr($ip, 1, strlen($ip) - 2)) . '-' . time();

    if (isset($msg_id) && $msg_id > 0) {	// editing of the existing message
		
      $query = 'SELECT u.username, u.moder, p.subject, p.closed as post_closed, p.views, p.id as msg_id, p.status, p.auth, p.parent, p.created, p.body, p.author, u.id as id, t.closed as thread_closed, ( select max(page) from confa_threads) - t.page + 1 as page, p.thread_id, t.id, p.content_flags, t.author as t_author from confa_users u, confa_posts p, confa_threads t where p.thread_id=t.id and u.id=p.author and p.id=' . $msg_id;
      $result = mysql_query($query);
      if (!$result) {
        mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed');
      }

      if (mysql_num_rows($result) != 0) {
        $row = mysql_fetch_assoc($result);
        $auth_id = $row['author'];
        $created = $row['created'];
        $msg_status = $row['status'];		
        $content_flags = $row['content_flags'];		
        if ( !is_null($row['post_closed']) && $row['post_closed'] > 0 ) {
          $post_closed = true;
        }
        if ( !is_null($row['thread_closed']) && $row['thread_closed'] > 0 ) {
          $thread_closed = true;
        }
        if ( $thread_closed || $post_closed ) {
          $reply_closed = true;
        }
        if ($content_flags & $content_nsfw) {
          $nsfw = true;
        }
        
        $title = 'Edit message';

        $body = $row['body'];
        $subj = $row['subject'];
        
        mysql_free_result($result);

        if ( $msg_status != 1 /* || $reply_closed */ || !can_edit_post($auth_id, $created, $user_id, $msg_id)) { 
          header('Location: ' . $root_dir . $page_msg . '?id=' . $msg_id, TRUE, 302); 
          die('Failed to edit the message. Message is not yours, has been answered or deleted/censored. Better luck next time!');
        }
      } else {
        die('No such message');
      }		
    }
?>
<base target="bottom">
</head>
<body onload="javascript:var subj = document.getElementById('subj'); addEvent(subj,'focus',function(){ this.selectionStart = this.selectionEnd = this.value.length;}); subj.focus();">
<?php 
    if (is_null($re) || strlen($re)== 0) {
?><table width="95%"><tr>
<td>
<h3><?php print($title);?></h3>
</td></tr></table><?php
    }

    if (!is_null($re) && strlen($re) > 0) {
        // save msg_id and subj, as they are changed by msg_inc
        if (isset($msg_id)) { 
          $edit_subj = $subj;
          $edit_id = $msg_id;
        }
        
        $msg_id = $re;
require("msg_inc.php");

        // restore msg_id & subj
        if (isset($edit_id)) {
          $subj = $edit_subj;
          $msg_id = $edit_id;
        } else {
          if (strncasecmp($subj, 're:', 3)) {
              $subj = 'Re: ' . $subj;
          }
          $msg_id = null;
        }
    }

require('new_inc.php'); 
require_once('tail_inc.php');
?>