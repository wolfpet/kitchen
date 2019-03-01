<?php
/*$Id: new.php 988 2014-01-05 01:14:33Z dmitriy $*/

require_once('head_inc.php');
require_once('html_head_inc.php');

$thread_owner = false;
    $title = 'New thread';
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

        $body = render_for_editing($row['body']);
        $subj = $row['subject'];
        
        mysql_free_result($result);

        if ( $msg_status != 1 /* || $reply_closed */ || !can_edit_post($auth_id, $created, $user_id, $msg_id)) { 
          header('Location: ' . $root_dir . $page_msg . '?id=' . $msg_id, TRUE, 302); 
          die('Failed to edit the message. Message is not yours, has been answered or deleted/censored. Better luck next time!');
        }
      } else {
        die('No such message');
      }
    } else if (isset($quote) && strlen($quote) > 0) {
      $body = "[quote]".$quote."[/quote]";
    }
?>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.6/styles/default.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.6/highlight.min.js"></script>
<script src="js/tenorgif.js"></script>
<?php

//only add postimage script if specified in settings
if($imageGallery == 'postimage')
{
    print(add_postimage());
}

?>
<?php  require_once('custom_colors_inc.php'); ?>

<base target="bottom">
<script>
function loadimage(img)
{
 setTimeout(function()
 {
  img.style.opacity= 1;
  var downloadingImage = new Image();
  downloadingImage.onload = function(){
  img.src = this.src;
    };
     downloadingImage.src = img.alt;
 }
   , 500);
}
function toggleExpand()
{
    if(document.getElementById("expandMsg").style.display=='none')
    {
      //enable expanding
      document.getElementById("expandMsg").style.display='block';
      document.getElementById("restoreMsg").style.display='none';
      parent.restore();
    }
    else
    {
      document.getElementById("expandMsg").style.display='none';
      document.getElementById("restoreMsg").style.display='block';
      parent.expand();	
    }
}
function initExpand() 
{
  if (parent.expanded && parent.expanded()) 
  {
    document.getElementById("expandMsg").style.display='none';
    document.getElementById("restoreMsg").style.display='block';    
  }
  else
  {
    document.getElementById("expandMsg").style.display='block';
    document.getElementById("restoreMsg").style.display='none';
  }
}
</script>
</head>
<body onload="javascript: initExpand(); var subj = document.getElementById('subj'); addEvent(subj,'focus',function(){ this.selectionStart = this.selectionEnd = this.value.length;}); subj.focus();">
  <div id="expandMsg" onclick="toggleExpand();parent.expand();" style="float: right;position: relative;width: 0px;top: -20px;right: -5px;cursor: pointer;">
    <svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="grey" d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"></path></g></svg>
  </div>
  <div id="restoreMsg" onclick="toggleExpand();parent.restore();" style="display: none; float: right;position: relative;width: 0px;top: -20px;right: -5px;cursor: pointer;">
    <svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="red" d="M5 16h3v3h2v-5H5v2zm3-8H5v2h5V5H8v3zm6 11h2v-3h3v-2h-5v5zm2-11V5h-2v5h5V8h-3z"></path></g></svg>
  </div>
<?php 
    if (is_null($re) || strlen($re)== 0) {
?>
<!--
<table width="95%"><tr>
<td>
<h3><?php print($title);?></h3>
</td></tr></table>
-->
<?php
    }

    if (!is_null($re) && strlen($re) > 0) {
        // save msg_id and subj, as they are changed by msg_inc
        if (isset($msg_id)) { 
          $edit_subj = $subj;
          $edit_id = $msg_id;
        }

        if (isset($nsfw)) {
          $old_nsfw = $nsfw;
        }
        
        $msg_id = $re;        
require("msg_inc.php");

        // restore msg_id, subj and nsfw flag
        if (isset($edit_id)) {
          $subj = $edit_subj;
          $msg_id = $edit_id;
        } else {
          if (strncasecmp($subj, 're:', 3)) {
              $subj = 'Re: ' . $subj;
          }
          $msg_id = null;
        }
        if (isset($old_nsfw)) {
          $nsfw = $old_nsfw;
        } else if (isset($nsfw)) {
          unset($nsfw);
        }
    } else {
      // add clickable "New message"
      ?>
      <h3 onclick="toggleExpand();" style="cursor: pointer" id="subject"><?php print($title); ?>
      </h3>
      <?php 
    }

require('new_inc.php'); 
require_once('tail_inc.php');
?>