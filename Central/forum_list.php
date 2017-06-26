<html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, minimum-scale=1, initial-scale=1, user-scalable=no, shrink-to-fit=no">
<link rel="stylesheet" type="text/css" href="../css/disc2.css">
<link rel="stylesheet" type="text/css" href="../css/common.css">
<link rel="stylesheet" type="text/css" href="../css/ribbon.css">
<title>K-Central</title>
</head>
<body id="html_body" style="overflow: hidden;">
<?php 
require_once('settings.php');
require_once('menu_inc.php'); 
require_once('func.php');

?>
<div id="content" style="padding: 10px; overflow-y: scroll; height: calc(100vh - 74px);">
<h3>List of forums on K-Central</h3>



<?php

if(!centralDBConnect()){die('CentralDBError');}

$query = 'select forum_title, reg_date from forums';
$result = mysql_query($query);
if (!$result) { die('Query failed ' ); }
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
{
    $forum_title = $row['forum_title'];
    $reg_date  = $row['reg_date'];
    print('&num;<a href="http://'. $forum_title . '.' . $baseName . '">'. $forum_title . '</a> created: '.$reg_date.'<br><br>');
}
                                        

?>

</div>
</body>
</html>

