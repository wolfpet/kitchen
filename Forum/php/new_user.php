<?php
/*$Id: new_user.php 381 2009-11-02 20:25:46Z dmitriy $*/

require_once('head_inc.php');
require_once('html_head_inc.php');

    $title= 'New user registration';

?>

<base target="bottom">
</head>
<body>
<table width="95%"><tr>
<td>
<h3><?php print($title);?></h3>
</td>

</tr></table>

<BR>
<?php

require_once("new_user_inc.php");
require_once('tail_inc.php');

?>

