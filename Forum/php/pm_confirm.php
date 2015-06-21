<?php
/*$Id: pm_confirm.php 823 2012-11-02 23:43:52Z dmitriy $*/
require_once('head_inc.php');
$css = 'disc2.css';
if (!is_null($user_id) && $user_id != null) {
  $query = "SELECT css from confa_users where id = " . $user_id;
  $result = mysql_query($query);
  if ($result) {
    $row = mysql_fetch_assoc($result);
    $css = $row['css'];
  } 
}

?>
<html>
<head>
<link REL="STYLESHEET" TYPE="text/css" HREF="<?=autoversion('css/'.$css)?>">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

<title>Thank you, </title></head>
<body style="background-color: #CCEEEE;">
<h3>Confirmation</h3>
Thank you, <b><?php print(htmlentities($user, HTML_ENTITIES,'UTF-8')); ?></b>!<br/><br/>
Your private message, named "<b><?php print(htmlentities($subj, HTML_ENTITIES,'UTF-8')); ?></b>", has been sent to <b><?php print(htmlentities($to, HTML_ENTITIES,'UTF-8')); ?></b>.<p>

</body></html>

</html>


