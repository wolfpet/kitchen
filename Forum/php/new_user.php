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
Due to abuse of free, impersonated automatic registration system, a new way of registration was introduced.
Goal is to make sure a person, trying to be registered is a real person, is not trying to register multiple accounts
and planning to be a good member of our community. In a very rare, unlikely case, as a result of long and multiple 
conflicts with the community, account may be suspended.
<br>To register, simply send email with request and desired nick to <B>dmitriy@radier.ca</B><!--<img src="/images/myemail.png"/>-->,
you will get response with the password shortly.
However, in some cases, identification through some of the acceptable ways may be requested, such as request
to send email through linkedin or other methods.
<B>Please, send 2 times due to spam filter</B>

We are welcome everyone with a good spirit and peacefull intentions.
Thank you. 

<?php

//require_once("new_user_inc.php");
require_once('tail_inc.php');

?>

