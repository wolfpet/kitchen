<?php
/*$Id: confirm.php 987 2014-01-01 21:03:31Z dmitriy $*/
require_once('head_inc.php');
require_once('settings.php');
require_once('get_params_inc.php');

    if (isset($_GET) && is_array($_GET)) {
        $msg_id = $_GET['id'];
        $author_name = $_GET['author_name'];
        $page = $_GET['page'];
        $subj = $_GET['subj'];
    }
?>

<html>
<head>
<link REL="STYLESHEET" TYPE="text/css" HREF="css/disc2.css">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

<title>Thank you, </title></head>
<body ><h3>Confirmation</h3>
Thank you, <b><?php print(htmlentities($author_name, HTML_ENTITIES,'UTF-8')); ?></b>!<br>
Your article, named "<b><?php print($subj); ?></b>", has been sent to forum.<p>

If you <a href="<?php print($root_dir . 'top.php');?>?page=<?php print($page . '&id=' . $msg_id . '#' . $msg_id); ?>" target="contents">refresh the contents</a>, you should see its name in forum's contents.<p><hr><p>
</body></html>

</html>


