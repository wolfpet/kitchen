<?php
/*$Id: dosearch.php 841 2012-11-22 02:17:40Z dmitriy $*/

require_once('dump.php');
require_once('head_inc.php');

//$query = "select subject, body from confa_posts where CONVERT(CAST(CONVERT(tbody USING latin1) AS BINARY) USING utf8) like _utf8 '%bla bla%' collate utf8_general_ci";


    $cur_page = $page_dosearch;
    $title = 'Search results';

    $max_id = 1;

    $query = 'SELECT u.username, u.moder, p.auth, p.closed as post_closed, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as created, p.subject, p.status, p.id as id, p.chars  from confa_posts p, confa_users u where p.author=u.id and p.status != 2 ';

    $query .= ' AND (CONVERT(CAST(CONVERT(body USING latin1) AS BINARY) USING utf8) like _utf8 "%'. $text .'%" collate utf8_general_ci';
    $query .= ' OR CONVERT(CAST(CONVERT(subject USING latin1) AS BINARY) USING utf8) like _utf8 "%'. $text .'%" collate utf8_general_ci)';
    
    //p.body like \'%' . mysql_real_escape_string( $text ) . '%\') ';


    if (!is_null($author) && strlen($author) > 0 ) {
        $query .= ' and u.username like \'%' . mysql_real_escape_string($author) . '%\' ';
    }

    $fromdate = '';
    $todate = '';

    $query .= ' order by created desc limit 500';
    //die($query);

    mysql_log(__FILE__, 'search query: ' . $query);
    $result = mysql_query($query);
    if (!$result) {
        mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed ' );
    }
    $numrows = mysql_num_rows($result);
    $num = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $id = $row['id'];
        $auth_moder = $row['moder'];

        $subj = $row['subject'];
        $subj   = encode_subject( $subj );
        $enc_user = htmlentities($row['username'], HTML_ENTITIES,'UTF-8');
        if ($num == 1) {
            $max_id = $id;
        }
        $line = '<li><a target="bottom" name="' . $id . '" href="' . $root_dir . $page_msg . '?id=' . $id . '"> ' . $subj . ' </a> <b>' . $enc_user . '</b>' . ' ' . $row['created'] . ' <b>' . $row['chars'] . '</b> bytes</li>';
        $out .= $line;
        $num++;
    }

require_once('html_head_inc.php');
require_once('custom_colors_inc.php'); 
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

//require('menu_inc.php');
?>


<br>Your searched for <?=is_null($mode) ? '' : $mode.' with ' ?><i><?php if (!is_null($text) && strlen($text)>0) {print('"' . $text . '"');} else { print('<u>any text</u>'); } ?></i> in <?php 
    switch($searchin) {
    case 1:
        print('<i><u>Body and subject</u></i> ');
    break;
    case 2:
        print('<i><u>Body</u></i> ');
    break;
    case 3:
        print('<i><u>Subject</u></i> ');
    break;
    }
    if (!is_null($author) && strlen($author)>0) {
        print('posted by "<i>' . $author . '</i>" ');
    } else {
        print('posted by <i><u>any author</u></i> ');
    }
    if (strlen($fromdate)> 0 ) {
        print(' from <i>' . $fromdate . '</i> ');
    }
    if (strlen($todate)> 0 ) {
        print(' to <i>' . $todate . '</i> ');
    }

?> 
</b> <br/><br/>Found <?php print( $numrows); ?> message(s) (maximum 500)
<br/>
<ol>
<?php print($out); ?>
</ol>
</body>
</html>
<?php

require('tail_inc.php');

?>

