<?php
/*$Id: dosearch.php 841 2012-11-22 02:17:40Z dmitriy $*/

require_once('dump.php');
require_once('head_inc.php');

    $cur_page = $page_dosearch;
    $title = 'Search results';

    $max_id = 1;

    if (is_null($prop_tz)) {
        $prop_tz = -5;
    }
    $query = 'SELECT u.username, u.moder, p.auth, p.closed as post_closed, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as created, p.subject, p.status, p.id as id, p.chars  from confa_posts p, confa_users u where p.author=u.id and p.status != 2 ';

    if (!is_null($howmanylikes) && strlen($howmanylikes)) {
        #$query .= ' and likes >= ' . $howmanylikes;
        if ($likedby == "2") {
            $query .= ' and likes >= ' . $howmanylikes;
        } else {
            $query .= ' and exists ( select * from confa_likes li where li.post = p.id and li.user = ' . $user_id . ' and li.value >= ' . $howmanylikes . ')';
        }

    }
    if (!is_null($text) && strlen($text) > 0) {
        switch ($searchin) {
        case 1:
            $query .= ' and ( p.subject like \'%' . mysql_escape_string( $text ) . '%\' or p.body like \'%' . mysql_escape_string( $text ) . '%\') ';
        break;
        case 2:
            $query .= ' and p.body like \'%' . mysql_escape_string( $text ) . '%\' ';
        break;
        case 3:
            $query .= ' and p.subject like \'%' . mysql_escape_string( $text ) . '%\' ';
        break;
        }
    }

    if (!is_null($author) && strlen($author) > 0 ) {
        $query .= ' and u.username like \'%' . mysql_escape_string($author) . '%\' ';
    }

    $fromdate = '';
    $todate = '';

    if (!is_null($fromyear) && $fromyear > 0 ) {
        if (is_null($fromday) || $fromday == 0 ) {
            $fromday = 1;
        }
        if (is_null($frommonth) || $frommonth == 0 ) {
            $frommonth = 1;
        }
        $fromdate = $fromyear . '-' . $frommonth . '-' . $fromday;
    }

    if (!is_null($toyear) && $toyear > 0 ) {
        if (is_null($today) || $today == 0 ) {
            $today = 1;
        }
        if (is_null($tomonth) || $tomonth == 0 ) {
            $tomonth = 1;
        }
        $todate =  $toyear . '-' . $tomonth . '-' . $today ;
    }

    if ( strlen( $fromdate) > 0 && strlen($todate) > 0) {
        $query .= ' and p.created between \'' . $fromdate . '\' and \'' . $todate . '\' ';
    } else {
        if ( strlen($fromdate) > 0) {
            $query .= ' and p.created > \'' . $fromdate . '\' ';
        }
        if ( strlen($todate) > 0) {
            $query .= ' and p.created < \'' . $todate . '\' ';
        }
    }
    
    if (strcmp("bookmarks", $mode) == 0) {
      $query .= ' and exists ( select * from confa_bookmarks b where b.post = p.id and b.user = ' . $user_id . ') ';
    }
    
    $query .= ' order by created desc limit 500';
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

