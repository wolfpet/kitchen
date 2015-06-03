<?php
/*$Id$*/

require_once('head_inc.php');

    $cur_page = $page_pay_ban;

    $max_id = 1;

    if (is_null($last_id)) {
        $last_id = 0;
    }
    $query = 'SELECT id, username, moder from confa_users where moder is null or moder=0 ';
    $result = mysql_query($query);
    if (!$result) {
        mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed ' );
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
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="9769777">
<input type="hidden" name="item_name" value="Ban user">

<TABLE><TR><TD>Username</TD><TD><SELECT name="custom" id="custom">
<?php
    $pref = $user_id * 10000;
    $out = '';
    while ($row = mysql_fetch_assoc($result)) {
        $val = $pref + $row['id'];

        print('<OPTION value="' . $val . 'a">' . htmlentities($row['username'], HTML_ENTITIES,'UTF-8') . '</OPTION>');
    }


?>
</SELECT>
</TD><TD>
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">

</TD></TR></TABLE>


</body>
</html>
<?php

require('tail_inc.php');

?>

