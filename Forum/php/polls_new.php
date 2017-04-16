<?php
require_once('dump.php');
require_once('head_inc.php');
require_once('html_head_inc.php');

if($user_id==null)die('unauthorized');

//register question first
$question= $_POST["pollQuestion"];
$query = 'INSERT INTO confa_polls(type, owner_id, content) values(0, ' . $user_id . ',  \'' . mysql_escape_string($question) . '\')';
$result = mysql_query($query);
if (!$result) { die('Query failed'); }
$question_id = mysql_insert_id();

//register answers
$numberOfAnswers = $_POST["numberOfAnswers"];
for($i=1; $i<=$numberOfAnswers; $i++)
{
    $query = 'INSERT INTO confa_polls(type, owner_id, content, question_id) values(1, ' . $user_id . ',  \'' . mysql_escape_string($_POST["pollAnswer" . $i]) . '\', '.$question_id.')';
    $result = mysql_query($query);
    if (!$result) { die('Query failed'); }
}

?>


<html>
<script>
    parent.document.getElementById('bottom').contentWindow.insertBodyText('body','[POLL]<?php print($question_id); ?>[/POLL]'); 
    parent.closeOverlay();
</script>
<body>
</body>

</html>

