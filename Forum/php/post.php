<?php
/*$Id: post.php 986 2014-01-01 20:42:58Z dmitriy $*/

    $ibody       = 'NULL';

    $msg_page = 1;

require_once('head_inc.php');

    $title = 'New message';
    if ($logged_in == false ) {
require_once('login_inc.php');
    }

  $err = @validate($subj, $body);

  if ( strlen($err) == 0) {   
    if (!$preview) {      
      $log = @post($subj, $body, $re, isset($msg_id) ? $msg_id : 0, $ticket, isset($nsfw) ? $nsfw : false);
      if (is_string($log)) {
        die($log);
      } 
      
      $msg_id = $log['id'];
      $result = mysql_query('SELECT username from confa_users where id=' . $user_id) or die('Cannot get username');
      $row = mysql_fetch_row($result);
      $username = $row[0];      
      $success = true;
      $confirm = $root_dir . $page_confirm . '?id=' . $msg_id . '&subj=' . urlencode(htmlentities($subj, HTML_ENTITIES,'UTF-8')) . '&page=' . $msg_page . '&author_name=' . urlencode(htmlentities( $username, HTML_ENTITIES,'UTF-8'));
      header("Location: $confirm",TRUE,302);
      exit();
    }
  }    
require_once('html_head_inc.php');
require_once('dump.php');
?>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.6/styles/default.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.6/highlight.min.js"></script>
<script>
function resizeMe(iframe)
{
    iframe.width  = iframe.contentWindow.document.body.scrollWidth + 5;
    iframe.height = iframe.contentWindow.document.body.scrollHeight + 25;
}
</script>
<?php

//only add postimage script if specified in settings
if($imageGallery == 'postimage')
{
    print(add_postimage());
}
    
?>
</head>
<body>
<?php
  if ( strlen($err) == 0) {
    
    if (!is_null($preview)) {
      
      $author = $user;
      $subject = substr($subj,0,255);
      $created = $time = local_time(time(), 'Y-m-d H:i:s'); 
      $translit_done = false;
      $new_body = render_for_db($body);
      $msgbody = translit($new_body, $translit_done);
      if (!is_null($msgbody) && strlen($msgbody) > 0 && !is_null($prefix) && strlen($prefix) > 0){
        $msgbody = $prefix . ' ' . str_replace("\n", "\n" . $prefix . ' ', $msgbody);
      }
      
      $msgbody = render_for_display($msgbody);
      
      $trans_body = $msgbody; 
      if ($translit_done === true) {
        $trans_body .= '<BR><BR>[Message was transliterated]';
      }
    require_once('msg_form_inc.php');
    }
  }

//require('new_inc.php');
require_once('tail_inc.php');
?>