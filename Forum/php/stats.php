<?php
require_once('head_inc.php');

$query = 'select u.username, u.status, count(p.id) as counter from confa_users u, confa_posts p where u.status != 2 and p.author=u.id and p.test=0 group by p.author order by counter desc';
    $result = mysql_query($query);
    if (!$result) {
        mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed ' );
    }

?>
<HTML>
<HEAD>
</HEAD>
<BODY>
<P>
Posts by users as by <?php print( date("Y-m-d H:i:s") ); ?> CST
<P>

<TABLE border="1">
<TR>
<TH>Place</TH><TH>Username</TH><TH>Posts</TH>
</TR>
<?php
    $i = 1;
    while ($row = mysql_fetch_assoc($result)) {
        print('<TR><TD>' . $i . '</TD><TD>' . htmlentities($row['username'], HTML_ENTITIES,'UTF-8') . '</TD><TD>' . $row['counter'] . '</TD></TR>');
        $i = $i +1;
    }
?>

</TABLE>
</BODY>
</HTML>

<?php
require_once('tail_inc.php');

?>

