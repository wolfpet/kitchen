<?php
/*$Id: modusers.php 814 2012-10-16 13:28:15Z dmitriy $*/

require_once('head_inc.php');

    if ( !is_null( $moder ) && $moder > 0 ) {
        $cur_page = $page_registrations;
        
        $how_many = 50;
        $max_id = 1;
        $last_id = 0;
        $limit = '';

        $query = 'SELECT username, actkey, CONVERT_TZ(created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as created from confa_regs order by username'; 
        $result = mysql_query($query);
        if (!$result) {
            mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed ' );
        }

        $num = 1;  
        $out = '';
        while ($row = mysql_fetch_assoc($result)) {
            $created = $row['created'];
            $enc_user = htmlentities($row['username'], HTML_ENTITIES,'UTF-8');
            $md5 = $row['actkey'];
            $line = '<tr><td align="center">'. $enc_user . '</td><td align="center">' . $created . '</td><td width="25%" align="center" nowrap>'
              .'<form method="get" action="http://'. $host . $root_dir . $page_activate .'" target="bottom">'
              .'<input type="hidden" name="act_link" value="'. $md5.'"/><input name="action" type="submit" value="Confirm"/><input name="action" type="submit" value="Decline"/></form>'
              .'</td></tr>';
            $out .= $line;
            $num++;
        }
    }

require_once('html_head_inc.php');

?>
<base target="bottom">
</head>
<body >
<!--<table width="95%"><tr>
<td>-->
<!--<h3><?php print($title);?></h3>-->
<!--</td>

</tr></table>-->
<?php
    if ( !is_null( $moder ) && $moder > 0 ) {

require('menu_inc.php');
        if (!is_null($err) && strlen($err) > 0) {
            print('<BR><font color="red"><b>' . $err . '</b></font>');
        }
        if (!is_null($byip) && strlen($byip) > 0) {
            print('<P>Posting from IP: <B>' . $byip . '</B>');
        }
?>

<!--<ol>-->
<table width="95%">
<tr><th>Username</th><th>Created</th><th>Status</th></tr>
<?php print($out); ?>
</table>
<!--</ol>-->
<?php
    } else {
        print( "You have no access to this page." );
    }
?>
</body>
</html>
<?php

require('tail_inc.php');

?>

