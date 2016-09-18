<?php
/*$Id: modips.php 378 2009-11-02 19:36:24Z dmitriy $*/

require_once('head_inc.php');

    if ( !is_null( $moder ) && $moder > 0 ) {
        $cur_page = $page_byuser;
        $how_many = 50;

        $last_ip = '0.0.0.0';

        $query = 'SELECT count(distinct(IP)) from confa_posts';
        $result = mysql_query($query);
        if (!$result) {
            mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed ' );
        }
        $row = mysql_fetch_row($result);
        $count = $row[0]; 
        $page_size = 50;

        if ($page > 1) {
            $query = 'SELECT max(IP) from (select distinct(IP) from confa_posts order by IP limit ' . $page_size*$page . ') p';
            $result = mysql_query($query);
            if (!$result) {
                mysql_log(__FILE__, 'queryfailed ' . mysql_error() . ' QUERY: ' . $query);
                die('Query failed');
            }
            $row = mysql_fetch_row($result);
            $last_ip = $row[0];

        }
        $query = 'SELECT distinct(IP) from confa_posts where IP > \'' . $last_ip . '\' limit 50'; 
        $result = mysql_query($query);
        if (!$result) {
            mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed ' );
        }

        $num = 1;  

        $out = '';
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $ip = $row['IP'];
            $line = '<li><a target="contents" href="' . $root_dir . $page_m_users . '?byip=' . $ip . '"> ' . $ip . ' </a> </li>';
            $out .= $line;
            $num++;
        }
    }

require_once('html_head_inc.php');

?>
<base target="bottom">
</head>
<body id="html_body">
<?php
    if ( !is_null( $moder ) && $moder > 0 ) {

require('menu_inc.php');

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
        print(" You have no access to this page." );
    }

?>

</body>
</html>
<?php

require('tail_inc.php');

?>

