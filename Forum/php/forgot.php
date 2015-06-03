<?php
/*$Id: forgot.php 378 2009-11-02 19:36:24Z dmitriy $*/

require_once('head_inc.php');
require_once('html_head_inc.php');

    $title= 'Forgot password';

?>

<base target="bottom">
</head>
<body>
<table width="95%"><tr>
<td>
<h3><?php print($title);?></h3>
</td>

</tr></table>

<?php

require_once("forgot_inc.php");
require_once('tail_inc.php');

?>

