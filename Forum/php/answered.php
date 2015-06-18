<?php
/*$Id: answered.php 843 2012-11-23 23:54:18Z dmitriy $*/

require_once('head_inc.php');

    $cur_page = $page_answered;

    $max_id = 1;

    if (is_null($last_answered_id)) {
        $last_answered_id = 0;
    }

    if (/*!is_null($how_many) && ctype_digit($how_many*/ $how_many > 0) {  
    	$query = 'SELECT b.id as my_id, b.author as me_author, u.username, u.moder, p.closed as post_closed, p.auth, p.views, p.content_flags, p.likes, p.dislikes, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as created, p.subject, p.author, p.status, p.id as id, p.chars from confa_posts p, confa_posts b, confa_users u where p.parent=b.id and b.author=' . $user_id . ' and p.author=u.id and p.status != 2 order by id desc limit ' . $how_many;
    } else {
    	$query = 'SELECT b.id as my_id, b.author as me_author, u.username, u.moder, p.closed as post_closed, p.auth, p.views, p.content_flags, p.likes, p.dislikes, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as created, p.subject, p.author, p.status, p.id as id, p.chars, s.last_answered_time  from confa_posts p, confa_posts b, confa_users u, confa_sessions s where s.hash=\'' . $auth_cookie .'\' and s.last_answered_time < p.created and p.parent=b.id and b.author=' . $user_id . ' and p.author=u.id and p.id > ' . $last_answered_id . ' and p.status != 2 order by id desc limit 100';
    }
    $result = mysql_query($query);
    if (!$result) {
        mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed ' );
    }

    $query = 'UPDATE confa_sessions set last_answered_time=current_timestamp where user_id = ' . $user_id;
    $result2 = mysql_query($query);
    if (!$result2) {
        mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed ' );
    }

    $num = 1;  

    $out = '';
    if (mysql_num_rows($result) == 0) {
        $max_id = $last_answered_id;
    }
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $id = $row['id'];
        $auth_moder = $row['moder'];

        $subj = $row['subject'];
        $subj = encode_subject( $subj );

        $enc_user = htmlentities($row['username'], HTML_ENTITIES,'UTF-8');
        $enc_user = '<a class="user_link" href="' . $root_dir . $page_byuser . '?author_id=' . $row['author'] . '" target="contents">' . $enc_user . '</a>';
        if ($num == 1) {
            setcookie('last_answered_id2', $id, 1800000000, $root_dir, $host);
            $max_id = $id;
        }
        $icons = '';
        if ($row['content_flags'] & 0x02) {
          $icons = ' <img border=0 src="' . $root_dir . $image_img . '"/> ';
        }
        if ($row['content_flags'] & 0x04) {
          $icons .= ' <img border=0 src="' . $root_dir . $youtube_img . '"/> ';
        }

        $line = '<li> ' . $icons . '<a target="bottom" name="' . $id . '" href="' . $root_dir . $page_msg . '?id=' . $id . '">' . print_subject($subj) . '</a>  <b>' . $enc_user . '</b>' . ' ' . '[' . $row['views'] . ' views] '  . $row['created'] . ' <b>' . $row['chars'] . '</b> bytes';
        
        if (!is_null($row['likes'])) {
          $likes = $row['likes'];
          if ($likes > 0) {
            $line .= ' <font color="green"><b>+' . $likes . '</b></font>';
          }
        }
        if (!is_null($row['dislikes'])) {
          $dislikes = $row['dislikes'];
          if ($dislikes > 0) {
            $line .= ' <font color="red"><b>-' . $dislikes . '</b></font>';
          }
        }
        $line .= "</li>";
        $out .= $line;
        $num++;
    }

require_once('html_head_inc.php');

?>
<base target="bottom">
</head>
<body>
<!--<table width="95%"><tr>
<td>-->
<!--<h3><?php print($title);?></h3>-->
<!--</td>

</tr></table>-->
<?php

require('menu_inc.php');

?>

<!--<table width="100%">
<tr>
<td><h3>Messages by date</h3></td>
<td>Queried: <b><?php  print(date('Y F d H:i:s', time())); ?></b></td>
</tr>
</table>
-->

<br>Queried: <b><?php  print(date('Y F d H:i:s', time())); ?></b><br>
<ol>
<?php print($out); ?>
</ol>
<form target="contents" method=POST action="<?php print($root_dir . $page_answered); ?>">
<?php 
    if (strlen($err) > 0) {
        print('<br><font color="red"><b>' . $err . '</b></font></br>');
    }
    print("<b>Want to see more? Say how many:</b>");
    print('<input type="text" size="5" id="how_many" name="how_many" value="' . $how_many . '">');
?>
<!--
<b>Want to see more? Say how many:</b>
<input type="text" size="5" id="how_many" name="how_many" value="<? print($how_many); ?>">
-->
<input type="submit" value="Get them!">
</body>
</html>
<?php

require('tail_inc.php');

?>

