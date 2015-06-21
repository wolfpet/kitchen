<?php
/*$Id: msg.php 988 2014-01-05 01:14:33Z dmitriy $*/

require_once('head_inc.php');
require_once('html_head_inc.php');

$likes = '';
$dislikes = '';
$reads = '';
$thread_owner = false;

/* Set to false to disallow thread owner 
close/open thread */
$managed = true;
?>
<!--<base target="bottom">-->
</head>
<body>
<?php
    if (is_null($action)) {
    	$query = 'UPDATE confa_posts set views=views + 1 where id=' . $msg_id;
    	$result = mysql_query($query);
    	if (!$result) {
        	mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        	die('Query failed');
    	}
    } 
    if (!is_null($action)) {
      	if (!strcmp($action, "like") || !strcmp($action, "dislike")) {
            $val = 1;
            if (!strcmp($action, "dislike")) {
               $val = -1;
            }
            $query = 'INSERT INTO confa_likes(user, post, value) values(' .
                $user_id . ', ' . $msg_id . ', ' . $val . ') ON DUPLICATE KEY UPDATE value=value+ ' . $val ;

            $result = mysql_query($query);
            if (!$result) {
                mysql_log( __FILE__ . ":" . __LINE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                die('Query failed');
            }
            $query = 'select value from confa_likes where user=' . $user_id . ' and post = ' . $msg_id;

            $result = mysql_query($query);
            if (!$result) {
                mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                die('Query failed');
            }
            $row = mysql_fetch_assoc($result);
            mysql_free_result($result);
            if ($row != null) {
                $val = $row['value'];
                $query = '';
                switch ($val) {
                   case 1:
                       if (!strcmp($action, "like")) {
                           $query = 'UPDATE confa_posts set likes=likes+1 where id=' . $msg_id;
                       }
                       break;
                   case -1:
                       if (!strcmp($action, "dislike")) {
                           $query = 'UPDATE confa_posts set dislikes=dislikes+1 where id=' . $msg_id;
                       }
                       break;
                   case 0:
                       if (!strcmp($action, "like")) {
                           $query = 'UPDATE confa_posts set dislikes=dislikes-1 where id=' . $msg_id;
                       } else /* dislike */ {
                           $query = 'UPDATE confa_posts set likes=likes-1 where id=' . $msg_id;
                       }
                       break;
                 } 
                 if (strlen($query) > 0 ) {
                     $result = mysql_query($query);
                     if (!$result) {
                         mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                         die('Query failed');
                     }

                 }
            }

      } else if (!strcmp($action, "bookmark")) {
	    $query = 'insert into confa_bookmarks(user, post) values(' . $user_id. ', ' . $msg_id . ');';
            $result = mysql_query($query);
            if (!$result) {
                 mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                 die('Query failed');
            }
      } else if (!strcmp($action, "closethread") || !strcmp($action, "openthread")) {
        $query = "SELECT t.author as t_author, t.properties as t_properties, t.id as thread_id  from confa_threads t, confa_posts p where p.thread_id = t.id and p.id=" . $msg_id;
            $result = mysql_query($query);
            if (!$result) {
                 mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                 die('Query failed');
            }
        $row = mysql_fetch_assoc($result);
        if ($user_id == $row['t_author']) {
          $thread_id = $row['thread_id'];
          $value = 0;
          if (!strcmp($action, "closethread")) {
            $value = 1;
          }
          $query = "UPDATE confa_threads set closed=" . $value . " where id=" . $thread_id; 
            $result = mysql_query($query);
            if (!$result) {
                 mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                 die('Query failed');
            }
        }
      }

   }
   $msg_bookmark = NULL;
   if (!is_null($user_id) && is_numeric($user_id)) { 
     $query = 'SELECT id from confa_bookmarks where user=' . $user_id . ' and post=' . $msg_id;
     $result = mysql_query($query);
     if (!$result) {
        mysql_log( __FILE__ . ":" . __LINE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed');
     }
     $row = mysql_fetch_assoc($result);
     $msg_bookmark = $row['id'];
   }

require("msg_inc.php");

if ( $reply_closed ) {
?>
Closed |
<?php
} else {
?>
<a href="<?php print($root_dir . $page_new); ?>?re=<?php print($msg_id); ?>">Reply</a> |
<span style="background-color: rgb(224, 224, 224);"><a href="<?php print( $root_dir . $page_pmail_send . '?to=' . $author . '&re=' .  $msg_id); ?>"); ?>Reply to sender (private)</a> </span>|
<?php
}
?>



<a target="contents" name="<?php print($msg_id); ?>" href="<?php print($root_dir . $page_expanded); ?>?page=<?php print($msg_page . '#' .$msg_id);?>">Synchronize</a> |
<a target="bottom" href="<?php print($root_dir . $page_thread); ?>?id=<?php print($msg_id); ?>">Thread</a>
<?php
  if (!is_null($user_id)) {
?>

|
<a target="bottom" href="<?php print($root_dir . $page_msg); ?>?id=<?php print($msg_id); ?>&action=like"><font color="green"><!--&#8679;-->+</FONT></a>
 <font color="blue">Reputation</font>
<a target="bottom" href="<?php print($root_dir . $page_msg); ?>?id=<?php print($msg_id); ?>&action=dislike"><font color="red"><!--&#8681;-->-</font></a>
|
<?php
   if (is_null($msg_bookmark)) {
       print('<a target="bottom" href="' . $root_dir . $page_msg . '?id=' . $msg_id . '&action=bookmark">Bookmark</a>');
   } else {
       print('In bookmarks');
   }
   if ($thread_owner && $managed) {
     print(" | ");
     if ($reply_closed) {
       print('<a target="bottom" href="' . $root_dir . $page_msg . '?id=' . $msg_id . '&action=openthread">Open Thread</a>');
     } else {
       print('<a target="bottom" href="' . $root_dir . $page_msg . '?id=' . $msg_id . '&action=closethread">Close Thread</a>');
     } 
   }
} // !is_null($user)
    if ( !is_null( $moder ) && $moder > 0 ) {
        print( '&nbsp;&nbsp;&nbsp;<SPAN STYLE="background-color: #FFE0E0">[ ' );
        if ( $msg_status == 3 ) {
            print( '<a href="' . $root_dir . 'modcensor.php' . '?action=uncensor&id=' . $msg_id . '"><font color="green">Uncensor message</font></A> |' );
        } else {
            print( '<a href="' . $root_dir . 'modcensor.php' . '?action=censor&id=' . $msg_id . '"><font color="green">Censor message</font></A> |' );
        }
        if ( $msg_status == 2 ) {
            print( ' <a href="' . $root_dir . 'modcensor.php' . '?action=undelete&id=' . $msg_id . '"><font color="green">Undelete message</font></A> |' );
        } else {
            print( ' <a href="' . $root_dir . 'modcensor.php' . '?action=delete&id=' . $msg_id . '"><font color="green">Delete message</font></A> |' );
        }
            if ( $thread_closed ) {
                print( ' <a href="' . $root_dir . 'modcensor.php' . '?action=openthread&id=' . $msg_id . '"><font color="green">Open thread</font></A> |' );
            } else {
                print( ' <a href="' . $root_dir . 'modcensor.php' . '?action=closethread&id=' . $msg_id . '"><font color="green">Close thread</font></A> |' );
            }
            if ( $post_closed ) {
                print( ' <a href="' . $root_dir . 'modcensor.php' . '?action=openpost&id=' . $msg_id . '"><font color="green">Open post</font></A> ' );
            } else {
                print( ' <a href="' . $root_dir . 'modcensor.php' . '?action=closepost&id=' . $msg_id . '"><font color="green">Close post</font></A> ' );
            }

        print( ']</SPAN>' );
    }
?>
<BR>
<?php

if (strlen($likes) > 0) {
  print(' <FONT color="green">' . $likes . '</FONT>');
}
if (strlen($dislikes) > 0) {
  print(' <FONT color="red">' . $dislikes . '</FONT>');
}
if (strlen($reads) > 0) {
  print(' <FONT color="lightgray">' . $reads . '</FONT>');
}
require_once('tail_inc.php');

?>
<br/>
<center style="color:gray"><?php print(chuck(15));?></center>
&nbsp;
</body>
</html>


