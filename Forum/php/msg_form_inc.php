<?php
/*$Id: msg_form_inc.php 816 2012-10-21 01:19:09Z dmitriy $*/
  include_facebook_api_if_required($msgbody);
?>
<h3 id="subject"><?php print($subject); ?></h3>
Author: <b><?php print($author . '</b>' . ' '); 
if (!is_null($views)) {
  print(" [$views views] " );
}
print($created); 

if (!is_null($modified)) {
  print(', last modified ' . $modified);
}
if (isset($nsfw)) {
  print('&nbsp;<span class="nsfw">NSFW</span>');
}
?><br>
<?php print($in_response); ?><hr><div id="body"><?php 
if (is_null($msgbody) || strlen($msgbody) == 0) {
  print(''); 
} else {
  print($trans_body); 
}
?></div><hr>



