<?php
/*$Id: modposts.php 814 2012-10-16 13:28:15Z dmitriy $*/

    if ( !is_null( $moder ) && $moder > 0 ) {

        $how_many = 50;
        $max_id = 1;
        $last_id = 0;
        $attr_in ='';
        $attr_out = '';
        $mode     = '';

        if ( !strcmp($cur_page, $page_m_delposts) ) {
            $subquery = 'status = 2 ';
            $attr_in = '<del>';
            $attr_out = '</del>';
            $mode     = '&mode=del';
        } else {
            if ( !strcmp($cur_page, $page_m_censposts) ) {
                $subquery = 'status = 3 ';
                $attr_in = '<font color="red">';
                $attr_out = '</font>';
                $mode    = '&mode=cens';
            }
        }
        $where = '';
        $where_author = '';
        if ( !is_null($author_id) ) {
            $where_author = 'p.author=' . $author_id . ' and ';
            $where = ' and author=' . $author_id;
        }
        $query = 'SELECT count(*) from confa_posts where ' . $subquery . $where;

        $result = mysql_query($query);
        if (!$result) {
            mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed ' );
        }
        $row = mysql_fetch_row($result);
        $count = $row[0]; 

        $last_id = get_page_last_index(' confa_posts where ' . $subquery . $where, $how_many, $page);
        if (is_null($last_id) || strlen($last_id) == 0)
          $last_id = 0;

        $query = 'SELECT u.username, u.moder, p.auth, p.closed as post_closed, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'EST\') as created, p.subject, p.status, p.id as msg_id, p.chars  from confa_posts p, confa_users u where ' . $where_author . ' p.author=u.id and  p.' . $subquery . ' and p.id <= ' . $last_id . ' order by msg_id desc limit 50'; 

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
            $msg_id = $row['msg_id'];
            $prop_moder = $row['moder'];

            $subj = $row['subject'];
            $subj = htmlentities($subj, HTML_ENTITIES,'UTF-8');
            $enc_user = htmlentities($row['username'], HTML_ENTITIES,'UTF-8');
            $line = '<li><a target="bottom" name="' . $msg_id . '" href="' . $root_dir . $page_msg . '?id=' . $msg_id . $mode . '"> ' . $attr_in . $subj . $attr_out . ' </a> <b>' . $enc_user . '</b>' . ' ' . $row['created'] . ' <b>' . $row['chars'] . '</b> bytes</li>';
            $out .= $line;
            $num++;
        }

require_once('html_head_inc.php');

?>
<base target="bottom">
</head>
<body id="html_body">
<?php

//require('menu_inc.php');

        $max_page = $count/20;
        $max_page++;
        print_pages($max_page, $page, 'contents', $cur_page);
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
    } else {
        print( "You have no access to this page.");
    }
?>

</body>
</html>
<?php

require('tail_inc.php');

?>

