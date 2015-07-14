<?php
/*$Id: activate.php 803 2012-10-14 19:35:33Z dmitriy $*/

require_once('head_inc.php');
require_once('get_params_inc.php');
require_once('html_head_inc.php');

?>
<base target="bottom">
</head>
<?php

if (!isset($reg_type) ||
    (isset($reg_type) && $reg_type == 2)){
  // Registration is closed
  print "Go away.";
  exit;
}

if ( strlen($act_link) > 0 ) {
    $query = 'SELECT username, password, timediff(current_timestamp, created) as td, email, actkey from confa_regs where actkey=\'' . $act_link . '\'';
    $result = mysql_query($query);
    if (!$result) {
        mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed');
    } 
    if ( mysql_num_rows($result) != 1) {
        die('Invalid request. The link probably has expired');
    }
    $row = mysql_fetch_assoc( $result );
    $username = $row['username'];
    $password = $row['password'];
    $email = $row['email'];
    if ($row['td'] > 86400) {
        $query = 'DELETE from confa_regs where username=\'' . $row['username'] . '\'';
        $result = mysql_query( $query );
        if (!$result) {
            mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed');
        }
        die('The link has expired.'); 
    } 
    #die('Изза абьюза форума регистрация прекращена на неопределенный срок.');
    $query = 'INSERT into confa_users(created, modified, username, password, email) values(NULL, NULL, \'' . $username . '\', \'' . $password . '\', \'' . $email . '\')';
    $result = mysql_query( $query );
    if (!$result) {
        mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed');
    }
    $query = 'DELETE from confa_regs where username=\'' . $row['username'] . '\'';
    $result = mysql_query( $query );
    if (!$result) {
        mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed');
    }

    } else {
        die('Invalid request');
    }

?>

<body>
<table width="95%"><tr>
<td>
<h3><?php print($title);?></h3>
</td>

</tr></table>

<?php
    print('<B>' . $username . '</B>, your account has been activated. Now you may login to the forum<BR><a href="http://' . $host . $root_dir . '">Forum</a>');
    require_once('tail_inc.php');
?>

