<?php

require_once('head_inc.php');
require_once('html_head_inc.php');
require_once('custom_colors_inc.php');

?>

<h3>Polls</h3>

<dl>
<?php

$query = 'select * from confa_polls where type=0 order by id desc;';
$result = mysql_query($query);

if (!$result) {die('Query failed ');}
while ($row = mysql_fetch_assoc($result))
{

print('<img border="0" src="images/es.gif" width="16" height="16" align="top" style="padding:0px 0px 3px 0px;">&nbsp;<a href="polls_display.php?poll='.$row['id'].'" target="bottom">'.  $row['content'] . '</a><br>');
}


?>
</dl>