<?php
/*$Id: pmail.php 803 2012-10-14 19:35:33Z dmitriy $*/

require_once('head_inc.php');

    if ($logout === true) {
        header( "Location: $protocol://$host$root_dir$page_expanded" ) ;
        die();
    }

    $cur_page = $page_pmail;
    $how_many = 20;
    $max_id = 1;

    $last_id = 0;
    $search_condition = 'receiver=' . $user_id . ' and !(p.status &'.$pm_deleted_by_receiver.')';
    
    if (!is_null($author_id)) $search_condition .= ' and sender='.$author_id;
    
    $query = 'SELECT count(*) from confa_pm p where '.$search_condition;
    $result = mysql_query($query);
    if (!$result) {
        mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed ' );
    }
    $row = mysql_fetch_row($result);
    $count = $row[0]; 

    $last_id = get_page_last_index('confa_pm p where ' . $search_condition, $how_many, $page);
    if (is_null($last_id)) {
        $last_id = 1;
    }

    $query = 'SELECT s.username as sender_name, p.id as id, p.sender, p.receiver, p.subject, p.body, CONVERT_TZ(p.created, \'' . $server_tz . '\', \''.$prop_tz.':00\') as created,  p.status,  p.chars  from confa_pm p, confa_users s where p.sender=s.id and '
      . $search_condition . ' and p.id <= ' . $last_id . ' order by id desc limit 20';
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
        $sender_id = $row['sender'];
        $st_in = '';
        $st_out = '';
        if ($status & 1) {
            $st_in = '<B>';
            $st_out = '</B>';
        }
   
        $subj = htmlentities($subj, HTML_ENTITIES,'UTF-8');
        $enc_user = htmlentities($sender, HTML_ENTITIES,'UTF-8');
        $line = '<li><INPUT TYPE=CHECKBOX NAME="pmdel[]" value="' . $id . '"/>' . $st_in . ' <a target="bottom" name="' . $id . '" href="' 
          . $root_dir . $page_msg_pm . '?id=' . $id . '"> ' . $subj . ' </a>' . $st_out . ' <a class="user_link" href="' 
          . $root_dir . $page_pmail . '?author_id=' . $sender_id . '" target="contents"><b>' . $enc_user . '</b></a>' . $auth_text . ' ' . $row['created'] . ' <b>' . $row['chars'] . '</b> bytes</li>';
        $out .= $line;
        $num++;
    }

require_once('html_head_inc.php');

?>
<base target="bottom">
</head>
<body id="html_body" style="background-color: #CCEEEE;">
<!--<table width="95%"><tr>
<td>-->
<!--<h3><?php print($title);?></h3>-->
<!--</td>

</tr></table>-->
<?php

require('menu_inc.php');

    $max_page = floor($count/20);
    $max_page++;
    print_pages($max_page, $page, 'contents', $cur_page);
    if (!is_null($err) && strlen($err) > 0) {
        print('<BR/><br/><font color="red"><b>' . $err . '</b></font>');
    } 
?>

<form method=POST target="contents" action="<?php print($root_dir . $page_pm_del); ?>">
<ol>
<?php print($out); ?>
</ol>
<!--
<?php 
    if (strlen($err) > 0) {
        print('<br/><br/><font color="red"><b>' . $err . '</b></font></br>');
    }
?>
-->
<input type="hidden" name="lastpage" value="<?php print($cur_page);?>">
<input type="submit" value="Delete selected">
</form>
</body>
</html>
<?php

require('tail_inc.php');

?>

