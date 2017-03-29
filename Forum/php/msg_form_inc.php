<?php
  /*$Id: msg_form_inc.php 816 2012-10-21 01:19:09Z dmitriy $*/
  include_facebook_api_if_required($msgbody);
  initialize_highlightjs_if_required($msgbody);
?>
<h3 onclick="toggleExpand();" style="cursor: pointer" id="subject"><?php print($subject); ?></h3>
Author: <b><?php print($author . '</b>' . ' '); 
if (!is_null($views)) {
  print(" [$views views] " );
}
print($created); 

if (isset($modified) && !is_null($modified)) {
  print(', last modified ' . $modified);
}
if ($content_flags & $content_boyan) {
  print('&nbsp;<img border=0 src="' . $root_dir . $boyan_img . '" valign="middle"/>');
}
if (isset($nsfw)) {
  print('&nbsp;<span class="nsfw">NSFW</span>');
}
?><br>
<?php print($in_response); ?><hr><div id="msgbody"><?php 
if (is_null($msgbody) || strlen($msgbody) == 0) {
  print(''); 
} else if (isset($nsfw) && $nsfw && isset($safe_mode) && $safe_mode != 0) {
  // print('<span style="color:gray; display: table-cell; vertical-align: middle; text-align:center;"><i>Content is blocked for your safety</i></span>');
  print('<H3 style="color:lightgray; display: table-cell; vertical-align: middle; text-align:center;">NSFW</H3>');
} else {
  print($trans_body); 
}
?></div><hr>



