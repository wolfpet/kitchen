<?php
/*$Id: new_inc.php 818 2012-10-22 20:02:52Z ranger $*/

require_once('head_inc.php');

$thread_owner = false;
$title = 'New message';
$ticket = '' . ip2long(substr($ip, 1, strlen($ip) - 2)) . '-' . time();
  
if (!is_null($re) && strlen($re) > 0) {
  $msg_id = $re;
  // Retrieve the original message's subject
  $query = 'SELECT u.username, u.moder, p.subject, p.closed as post_closed, p.views, p.id as msg_id, p.status, p.auth, p.parent, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as created, p.body, p.author, u.id as id, t.closed as thread_closed, ( select max(page) from confa_threads) - t.page + 1 as page, p.thread_id, t.id, p.status, t.author as t_author, t.properties as t_properties from confa_users u, confa_posts p, confa_threads t where p.thread_id=t.id and u.id=p.author and p.id=' . $msg_id;
  $result = mysql_query($query);
  if (!$result) {
      mysql_log( __FILE__, 'query 2 failed ' . mysql_error() . ' QUERY: ' . $query);
      die('Query failed');
  }

  if (mysql_num_rows($result) != 0) {
    $row = mysql_fetch_assoc($result);
    $subject = htmlentities(translit($row['subject'], $proceeded), HTML_ENTITIES,'UTF-8');
    $subj = $subject;
    
    if (strpos($subj, 'Re:') !== 0) $subj = "Re: " . $subj;
    
    $author = htmlentities($row['username'], HTML_ENTITIES,'UTF-8');
    $created = $row['created'];
    $msg_status = $row['status'];
    if ( !is_null($row['post_closed']) && $row['post_closed'] > 0 ) {
        $post_closed = true;
    }
    if ( !is_null($row['thread_closed']) && $row['thread_closed'] > 0 ) {
        $thread_closed = true;
    }
    if ( $thread_closed || $post_closed ) {
        $reply_closed = true;
        $err = "Post or thread is closed";
    }
  } else {
    $err = "No such message";
  }
  $field_id = $re;
} else {
  $field_id = intval(time());
}

if (strlen($err) != 0) {
    print('<div class="errmsg">' . $err . '</div>');
} else {
?>
<script language="JavaScript" src="<?=autoversion('js/translit.js')?>"></script>
<script language="JavaScript" src="<?=autoversion('js/func.js')?>"></script>

<form action="<?php print($root_dir . $page_post); ?>" method="post" id="msgform_<?=$re?>" name="msgform">
<input type="hidden" name="re" id="re" value="<?php print($re); ?>"/>
<input type="hidden" name="ticket" id="ticket" value="<?php print($ticket); ?>"/>

<table width="100%">
<tr><td width="80%" valign="top">
<table width="100%">
<?php
 if (!$logged_in) {
?>
<tr>
<td nowrap colspan="3">From: <input onfocus="javascript:RegisterField(this, true, false);" onkeypress="javascript:translate2(event);" onkeydown="javascript:text_OnKeydown(event);" type="text" id="user" name="user" value="<?php print($user); ?>" size="32" maxlength="64"/></td>
</tr>
<tr>
<td nowrap colspan="3">Password: <input type="password" id="password" name="password" size="16" maxlength="16"/></td>
</tr>
<?php
 } else {
?>
<tr>
</tr>
<?php
}
if (!isset($_SERVER['HTTP_USER_AGENT']) || FALSE === strpos( $_SERVER['HTTP_USER_AGENT'], 'Opera Mini' ) ) {
  $keyboard = true;
} else {
  $keyboard = false;
}
?>
<tr>
<td size=20% colspan="3">Subject <input type="text" style="display:block;width:100%;" <?php if ($keyboard) { ?> onfocus="javascript:RegisterField(this, true, false);" onkeypress="javascript:translate2(event);" onkeydown="javascript:text_OnKeydown(event);" <?php } ?> name="subj" id="subj" tabindex="1" value='<?=str_replace("'", "&#39", $subj)?>' maxlength="128"/></td>
</tr>
<tr>
<td colspan="1" nowrap>
<?php
if ($keyboard) {
?>
<div id="ruschars" style="display:none;"><a href="javascript:toggleCharset();">Russian keyboard</a></div>
<div id="latchars" style="display:block;"><a href="javascript:toggleCharset();">Latin keyboard</a></div>
<?php
}
?>
</td>
<td nowrap align="right">&nbsp;
  <a href="#" style="text-decoration: none" onclick="javascript:insertBBCode('body_<?=$field_id?>', 'b');return false;">[<b>b</b>]</a>
  <a href="#" style="text-decoration: none" onclick="javascript:insertBBCode('body_<?=$field_id?>', 'i');return false;">[<i>i</i>]</a>
  <a href="#" style="text-decoration: none" onclick="javascript:insertBBCode('body_<?=$field_id?>', 'u');return false;">[<u>u</u>]</a>
  <a href="#" style="text-decoration: none" onclick="javascript:insertBBCode('body_<?=$field_id?>', 's');return false;">[<del>s</del>]</a>
</td>
<td nowrap align="right">
<a href="#" onclick="javascript:insertTag('body_<?=$field_id?>', 1);return false;">[url=]</a>&nbsp;
<a href="#" onclick="javascript:insertTag('body_<?=$field_id?>', 2);return false;">[img=]</a>
</td>
</tr>
<tr>
<td colspan="3" width="100%">
<textarea id="body_<?=$field_id?>" name="body" style="width: 100%;" <?php if ($keyboard) { ?> onfocus="javascript:RegisterField(this, true, false);" onkeypress="javascript:translate2(event);" onkeydown="javascript:text_OnKeydown(event);" onpaste="javascript:insertURL(this);"<?php } ?> 
  tabindex="2" rows="8"><?=$body?></textarea>
</td>
</tr>
<tr>
<td colspan="3"><!--<input name="preview" id="preview" type="checkbox" value="off"/> Preview first</input> --><input name="nsfw" id="nsfw" type="checkbox" value="true" <?=isset($nsfw)?"checked":""?>/> NSFW</td></td>
</td></tr>
<tr>
<!-- <td colspan="3"><input tabindex="3" value="Send!" type="submit"></td> -->
</tr>
</tbody></table>
</td>
<!--<td valign="top">
</td>-->
</tr>
</table>
<a id="send_<?=$re?>" class="mbutton" href="#" onclick="send_message(this, '<?=$re?>'); return false;"
  >Send</a><a id="discard" class="mbutton" href="#" onclick="discard_message('<?=$re?>'); return false;">Discard</a>

</form>
<?php 
} 
?>