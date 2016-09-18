<?php
/*$Id: mymessages.php 829 2012-11-04 20:36:54Z dmitriy $*/

require_once('head_inc.php');

    $cur_page = $page_my_messages;
    $how_many = 50;
    $max_id = 1;
    $author_id = $user_id;

    $last_id = 0;

    $query = 'SELECT count(*) from confa_posts where author=' . $author_id . ' and status != 2';
    $result = mysql_query($query);
    if (!$result) {
        mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die(' Query failed ' );
    }
    $row = mysql_fetch_row($result);
    $count = $row[0]; 

    $last_id = get_page_last_index('confa_posts where author=' . $author_id , $how_many, $page );
    $query = 'SELECT u.username, u.id as user_id, u.moder, p.auth, p.closed as post_closed, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz 
      . ':00\') as created, u.ban_ends, p.level, CONVERT_TZ(p.modified, \'' . $server_tz . '\', \'' . $prop_tz 
      . ':00\') as modified, t.closed as thread_closed, t.counter, p.subject, p.content_flags, p.views, p.likes, p.dislikes, p.status, p.id as msg_id, p.chars, (SELECT count(*) from confa_bookmarks b where b.post=p.id) as bookmarks, (SELECT count(*) from confa_likes l where l.post=p.id and reaction is not null) as reactions from confa_posts p, confa_users u, confa_threads t where p.author=' 
      . $author_id . ' and p.author=u.id and p.status != 2 and t.id = p.thread_id';

    if (intval($last_id) > 0)
        $query .= ' and p.id <= ' . $last_id;

    $query .= ' order by msg_id desc limit 50'; 
    
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
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $line = '<li>'. print_line($row, false, false, false, false) . '</li>';  // $collapsed=false, $add_arrow=false, $add_icon=true, $indent=true
        $out .= $line;
        $num++;
    }

require_once('html_head_inc.php');

?>
<base target="bottom">
</head>
<body id="html_body">
<!--<table width="95%"><tr>
<td>-->
<!--<h3><?php print($title);?></h3>-->
<!--</td>

</tr></table>-->
<?php

require('menu_inc.php');

    $max_page = $count/$how_many;
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
<?php 
    print_pages($max_page, $page, 'contents', $cur_page, '&author_id=' . $author_id, false);
?>
</body>
</html>
<?php

require('tail_inc.php');

?>

