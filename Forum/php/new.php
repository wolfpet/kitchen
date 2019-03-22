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
  var a = navigator.userAgent||navigator.vendor||window.opera; // agent
  
  if (parent.expanded && parent.expanded()) 
  {
    document.getElementById("expandMsg").style.display='none';
    document.getElementById("restoreMsg").style.display='block';    
  }
  else if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) {
      // if mobile, expand
      toggleExpand();
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