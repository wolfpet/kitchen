<?php
/*$Id: activate.php 803 2012-10-14 19:35:33Z dmitriy $*/

require_once('head_inc.php');
require_once('html_head_inc.php');

?>
<base target="bottom">
</head>
<?php

if (!isset($reg_type) ||
    (isset($reg_type) && $reg_type == REG_TYPE_CLOSED)) {
  die('Изза абьюза форума регистрация прекращена на неопределенный срок.');
}
if ( strlen($act_link) > 0 ) {
    $query = 'SELECT username, password, timediff(current_timestamp, created) as td, email, actkey from confa_regs where actkey=\'' . mysql_real_escape_string($act_link) . '\'';
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
<?php
    if (isset($reg_type) && $reg_type == REG_TYPE_CONFIRM) 
      print('<p>Account <B>' . $username . '</B> has been activated. The user may now login to the <a href="http://' . $host . $root_dir . '" target="_top">forum');
    else
      print('<p><B>' . $username . '</B>, your account has been activated. Now you may login to the <a href="http://' . $host . $root_dir . '" target="_top">forum</a>');
    require_once('tail_inc.php');
?>

