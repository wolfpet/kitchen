<?php
/*$Id: banned.php 811 2012-10-15 23:14:06Z dmitriy $*/

require_once('head_inc.php');
require_once('func.php');

$query = 'SELECT u.username, m.username as banned_by, u.ban, CONVERT_TZ(u.ban_ends, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as ban_end, h.ban_reason from confa_users u, confa_ban_history h, confa_users m where u.ban_ends>current_timestamp() and u.ban = h.id and m.id=h.moder';
$result = mysql_query($query);
if (!$result) {
    mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
    die('Query failed ' );
}

require_once('html_head_inc.php');

?>
</HEAD>
<BODY>
<h3>Banned users</h3>
<table border="1">
<tr>
<th>Username</th>
<th>Reason</th>
<th>Moderator</th>
<th>Ban ends</th>
</tr>
<?php
    while ($row = mysql_fetch_assoc($result)) {
        $username = htmlentities($row['username'], HTML_ENTITIES,'UTF-8');
        $translit_done = false;
        $reason = translit($row['ban_reason'], $translit_done);
        $reason = htmlentities( $reason, HTML_ENTITIES,'UTF-8');
        $reason = before_bbcode($reason);
        $reason = do_bbcode ( $reason );
        $reason = nl2br($reason);
        $reason = after_bbcode($reason);
        $banned_by = htmlentities($row['banned_by'], HTML_ENTITIES,'UTF-8');
        $ban_ends = $row['ban_end'];
        print('<tr><td>' . $username . '</td><td>' . $reason . '</td><td>' . $banned_by . '</td><td>' . $ban_ends . '</td></tr>');

    }
?>
</table>
</BODY>
</HTML>
<?php

require('tail_inc.php');

?>

