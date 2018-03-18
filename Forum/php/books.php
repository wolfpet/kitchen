<?php

require_once('head_inc.php');
require_once('html_head_inc.php');
require_once('custom_colors_inc.php');

?>

<h3>Books</h3>

<dl>
<?php

$query = 'SELECT confa_books.msg_id as msgid, confa_posts.subject, confa_users.username from confa_books, confa_posts, confa_users where confa_posts.id=confa_books.msg_id and confa_users.id=confa_posts.author;';
$result = mysql_query($query);
if (!$result) {die('Query failed ');}
while ($row = mysql_fetch_assoc($result))
{
 print('<img border="0" src="images/es.gif" width="16" height="16" align="top" style="padding:0px 0px 3px 0px;">&nbsp;<a href="msg.php?id='.$row['msgid'].'" target="bottom">'.$row['subject'].'</a>&nbsp;<b>'.$row['username'].'</b><br>');
}


?>
</dl>