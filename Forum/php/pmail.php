<?php
/*$Id: pmail.php 803 2012-10-14 19:35:33Z dmitriy $*/

require_once('head_inc.php');

    if ($logout === true) {
        header( "Location: http://$host$root_dir" ) ;
        die();
    }

    $cur_page = $page_pmail;
    $how_many = 20;
    $max_id = 1;

    $last_id = 0;

    $query = 'select id from confa_users where username=\'' . $user . '\'';
    $result = mysql_query($query);
    if (!$result) {
        mysql_log(__FILE__, 'get_user_props failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed');
    }
    $row = mysql_fetch_row($result);
    $user_id = $row[0];

    $query = 'SELECT count(*) from confa_pm where receiver=' . $user_id . ' and status != 2';
    $result = mysql_query($query);
    if (!$result) {
        mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed ' );
    }
    $row = mysql_fetch_row($result);
    $count = $row[0]; 

    $last_id = get_page_last_index('confa_pm where receiver=' . $user_id, $how_many, $page);
    if (is_null($last_id)) {
        $last_id = 1;
    }

    $query = 'SELECT s.username as sender_name, p.id as id, p.sender, p.receiver, p.subject, p.body, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'EST\') as created,  p.status,  p.chars  from confa_pm p, confa_users s where p.sender=s.id and p.receiver=' . $user_id . ' and p.status != 2 and p.id <= ' . $last_id . ' order by id desc limit 20';
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
    $auth_text = '';
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $id = $row['id'];
        $sender = $row['sender_name'];
        $subj = $row['subject'];
        $body = $row['body'];
        $created = $row['created'];
        $chars = $row['chars'];
        $status = $row['status'];
        $st_in = '';
        $st_out = '';
        if ($status == 1) {
            $st_in = '<B>';
            $st_out = '</B>';
        }
   
        $subj = htmlentities($subj, HTML_ENTITIES,'UTF-8');
        $enc_user = htmlentities($sender, HTML_ENTITIES,'UTF-8');
        $line = '<li><INPUT TYPE=CHECKBOX NAME="pmdel[]" value="' . $id . '"/>' . $st_in . ' <a target="bottom" name="' . $id . '" href="' . $root_dir . $page_msg_pm . '?id=' . $id . '"> ' . $subj . ' </a>' . $st_out . ' <b>' . $enc_user . '</b>' . $auth_text . ' ' . $row['created'] . ' <b>' . $row['chars'] . '</b> bytes</li>';
        $out .= $line;
        $num++;
    }

require_once('html_head_inc.php');

?>
<base target="bottom">
</head>
<body style="background-color: #CCEEEE;">
<!--<table width="95%"><tr>
<td>-->
<!--<h3><?php print($title);?></h3>-->
<!--</td>

</tr></table>-->
<?php

require('menu_inc.php');

    $max_page = $count/20;
    $max_page++;
    print_pages($max_page, $page, 'contents', $cur_page);
    if (!is_null($err) && strlen($err) > 0) {
        print('<BR><font color="red"><b>' . $err . '</b></font>');
    } 
?>

<form method=POST target="contents" action="<?php print($root_dir . $page_pm_del); ?>">
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

<input type="submit" value="Delete selected">
</form>
</body>
</html>
<?php

require('tail_inc.php');

?>

