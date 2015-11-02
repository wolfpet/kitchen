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

?><link rel="stylesheet" type="text/css" href="<?=autoversion('css/diff.css');?>">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.6/styles/default.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.6/highlight.min.js"></script>
<script language="javascript">
function report_on()
{
  var div = document.getElementById("report");  
  if (div != null) {
    if (div.style.display != 'inline')
      div.style.display = 'inline';
    else
      div.style.display = 'none';
  }
}
</script>
<base target="bottom">
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
      if (!strcmp($action, "like")) {
        if (like($user_id, $msg_id, 1) === false) {
          die('Query failed');
        }
      } else if (!strcmp($action, "dislike")) {
        if (like($user_id, $msg_id, -1) === false) {
          die('Query failed');
        }
      } else if (!strcmp($action, "bookmark")) {
        if (bookmark($user_id, $msg_id, true) == false) {
          die('Query failed');
        }
      } else if (!strcmp($action, "unbookmark")) {
        if (bookmark($user_id, $msg_id, false) == false) {
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
      } else if (!strcmp($action, "report")) {
        if (report($user_id, $msg_id, $mode) == false) {
          die('Query failed');
        }
      }
   }
   $msg_bookmark = NULL;
   $bookmarks = '';
   if (!is_null($user_id) && is_numeric($user_id)) { 
     $query = 'SELECT b.id, b.user, u.username from confa_bookmarks b, confa_users u where b.user=u.id and b.post=' . $msg_id; 
     $result = mysql_query($query);
     if (!$result) {
        mysql_log( __FILE__ . ":" . __LINE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed');
     }
     while ($row = mysql_fetch_assoc($result)) {
       if ($row['user'] == $user_id) { 
        $msg_bookmark = $row['id'];
       }
       if (strlen($bookmarks) > 0) $bookmarks .= ', ';
       $bookmarks .= $row['username'];
     }
   }

require("msg_inc.php");

if ( $reply_closed ) {
?>
Closed |
<?php
} else {
?>

<a href="<?php print($root_dir . $page_new); ?>?re=<?php print($msg_id); ?>">Reply</a> |
<span style="background-color: rgb(224, 224, 224);"><a href="<?php print( $root_dir . $page_pmail_send . '?to=' . $author . '&re=' .  $msg_id); ?>"); ?>Reply to sender (private)</a></span> |
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
       print('<a target="bottom" href="' . $root_dir . $page_msg . '?id=' . $msg_id . '&action=unbookmark"><font color="black">In bookmarks</font></a>');
   }
   if ($thread_owner && $managed) {
     print(" | ");
     if ($reply_closed) {
       print('<a target="bottom" href="' . $root_dir . $page_msg . '?id=' . $msg_id . '&action=openthread">Open&nbsp;Thread</a>');
     } else {
       print('<a target="bottom" href="' . $root_dir . $page_msg . '?id=' . $msg_id . '&action=closethread">Close&nbsp;Thread</a>');
     } 
   }
   if (intval($revisions) > 0) {
     print(" | ");
     print('<a href="javascript:revisions_on();">Revisions</a>');
   }
   if ($user_id != $auth_id) {
     print(" | ");
     print('<a target="bottom" href="javascript:report_on();">Report</a><span id="report" style="display:none;"> as <a target="bottom" href="' 
      . $root_dir . $page_msg . '?id=' . $msg_id . '&action=report&mode=nsfw">NSFW</a> or <a target="bottom" href="' 
      . $root_dir . $page_msg . '?id=' . $msg_id . '&action=report&mode=boyan">Repetitive</a></span>');
   }   
   if (!$reply_closed && can_edit_post($auth_id, $created_ts, $user_id, $msg_id)) {
     print(" | ");
     if (!is_null($parent) && $parent != 0) {
         print('<a target="bottom" href="' . $root_dir . $page_new . '?id=' . $msg_id . '?&re=' . $parent . '">Edit</a>');
     } else {
         print('<a target="bottom" href="' . $root_dir . $page_new . '?id=' . $msg_id . '">Edit</a>');
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
$footer = '';
if (strlen($bookmarks) > 0) {
  $footer .= ' <FONT color="darkblue">' . $bookmarks . '</FONT>';
}
if (strlen($likes) > 0) {
  $footer .= ' <FONT color="green">' . $likes . '</FONT>';
}
if (strlen($dislikes) > 0) {
  $footer .= ' <FONT color="red">' . $dislikes . '</FONT>';
}
if (strlen($reads) > 0) {
  $footer .= ' <FONT color="lightgray">' . $reads . '</FONT>';
}
if (isset($reports) && $reports['boyan'] != '') {
  $footer .= ' <img border=0 src="' . $root_dir . $boyan_img . '" valign="middle"/>&nbsp;<span style="color:gray">' . $reports['boyan'].'</span>';
}
if (isset($reports) && $reports['nsfw'] != '') {
  $footer .= ' <span class="nsfw">nsfw</span>&nbsp;<span style="color:gray">'.$reports['nsfw'].'</span>';
}

if (strlen($footer) > 0) {
  $footer .= '<BR/>';
}
print($footer);

require_once('msg_hist_inc.php');
require_once('tail_inc.php');
?>
<br/><center style="color:gray"><?php print(chuck(15));?></center>
&nbsp;
</body>
</html>


