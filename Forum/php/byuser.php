<?php
/*$Id: byuser.php 814 2012-10-16 13:28:15Z dmitriy $*/

require_once('head_inc.php');

    $cur_page = $page_byuser;
    $how_many = 50;
    $max_id = 1;

    $last_id = 0;

    $query = 'SELECT count(*) from confa_posts where author=' . $author_id . ' and status != 2';
    $result = mysql_query($query);
    if (!$result) {
        mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed ' );
    }
    $row = mysql_fetch_row($result);
    $count = $row[0]; 

    $last_id = get_page_last_index('confa_posts where author=' . $author_id, $how_many, $page);
    $query = 'SELECT u.username, u.moder, p.auth, p.closed as post_closed, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as created, p.subject, p.status, p.id as msg_id, p.chars  from confa_posts p, confa_users u where p.author=' . $author_id . ' and p.author=u.id and  p.status != 2 and p.id <= ' . $last_id . ' order by msg_id desc limit 50'; 

    $result = mysql_query($query);
    if (!$result) {
        mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed ' );
    }

    $num = 1;  

    $out = '';
    if (mysql_num_rows($result) == 0) {
        $max_id = $last_id;
    }
    while ($row = mysql_fetch_assoc($result)) {
        $msg_id = $row['msg_id'];
        $u_moder = $row['moder'];

        $subj = $row['subject'];
        $subj = encode_subject( $subj );
        $enc_user = htmlentities($row['username'], HTML_ENTITIES,'UTF-8');
        $line = '<li><a target="bottom" name="' . $msg_id . '" href="' . $root_dir . $page_msg . '?id=' . $msg_id . '"> ' . $subj . ' </a> <b>' . $enc_user . '</b>' . ' ' . $row['created'] . ' <b>' . $row['chars'] . '</b> bytes</li>';
        $out .= $line;
        $num++;
    }

require_once('html_head_inc.php');

?>
<base target="bottom">
</head>
<body >
<!--<table width="95%"><tr>
<td>-->
<!--<h3><?php print($title);?></h3>-->
<!--</td>

</tr></table>-->
<?php

//require('menu_inc.php');

    $max_page = $count/20;
    $max_page++;
    print_pages($max_page, $page, 'contents', $cur_page, '&author_id=' . $author_id);
    if (!is_null($err) && strlen($err) > 0) {
        print('<BR><font color="red"><b>' . $err . '</b></font>');
    }
?>

<ol>
<?php print($out); ?>
</ol>
<!--
<?php 
    if (strlen($err) > 0) {
        print('<br><font color="red"><b>' . $err . '</b></font></br>');
    }
?>
-->

</body>
</html>
<?php

require('tail_inc.php');

?>

