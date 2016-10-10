<?php
/*$Id: answered.php 843 2012-11-23 23:54:18Z dmitriy $*/

require_once('head_inc.php');

    $cur_page = $page_answered;

    $max_id = 1;

    get_show_hidden_and_ignored();

    $result = get_answered($how_many);
    if (!$result) {
        die('Query failed');
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
        $line = '<li>' . print_line($row, false, false, false, false);
        $line .= "</li>";
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
?>

<!--<table width="100%">
<tr>
<td><h3>Messages by date</h3></td>
<td>Queried: <b><?php  print(date('Y F d H:i:s', time())); ?></b></td>
</tr>
</table>
-->

<br>Queried: <b><?php  print(local_time(time(), 'Y F d H:i:s')); ?></b><br>
<ol>
<?php print($out); ?>
</ol>
<form target="contents" method=POST action="<?php print($root_dir . $page_answered); ?>">
<?php 
    if (is_null($how_many) || $how_many == 0) {
        $how_many = 20;
    }
    if (strlen($err) > 0) {
        print('<br><font color="red"><b>' . $err . '</b></font></br>');
    }
    print("<b>Want to see more? Say how many: </b>");
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

