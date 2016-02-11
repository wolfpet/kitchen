<?php
//require_once('settings.php');
//require_once('get_params_inc.php');
require_once('head_inc.php');

$top = "$page_expanded?dummy=1";
$bottom = $page_welc;

if (!is_null($pm_id)) {
  // pmail
  $top = $page_pmail;
  $bottom = "$page_msg_pm?id=$pm_id";
} else if (isset($msg_id) && $msg_id > 0) {
  // regular post
  $top = "$page_expanded?page=$page#$msg_id";
  $bottom = "$page_msg?id=$msg_id";
} else if (!is_null($re)) {
  // response
  $top = "$page_expanded?page=$page#$re";
  $bottom = "$page_new?re=$re";
}
?>
<html>
<head>
<title><?=$title?></title>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-Frame-Options" content="DENY">
<meta http-equiv="X-Content-Type-Options" content="nosniff">
</head>
<frameset rows="45%,55%">
  <frame src="<?=$top?>" name="contents">
  <frame src="<?=$bottom?>" name="bottom">
</frameset>
<script src="//fast.wistia.net/labs/fresh-url/v1.js" async></script>
</html>

