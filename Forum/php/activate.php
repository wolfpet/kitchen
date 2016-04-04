<?php
/*$Id: activate.php 803 2012-10-14 19:35:33Z dmitriy $*/

require_once('head_inc.php');
require_once('html_head_inc.php');

?>
<base target="bottom">
</head>
<?php

if (isset($reg_type) && $reg_type == REG_TYPE_CLOSED) {
  die('Registration is closed');
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
    $action = strtolower($action);
    $action_deny = 'decline';
    if ($row['td'] > 86400 || $action == $action_deny) {
        $query = 'DELETE from confa_regs where username=\'' . $row['username'] . '\'';
        $result = mysql_query( $query );
        if (!$result) {
            mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed');
        }
        if ($action == $action_deny) {
          // notify the user
          $email_subject = "Your registration on $host forum website";
          $email_message = "We regret to inform that your request of registration the user '$username' has been declined.\n\nThe administration wishes you luck in your future endeavors.";
          $email_headers = "From: $from_email";
          mail($email,$email_subject,$email_message,$email_headers);
          die('The registration of user <b>' . $username. '</b> has been declined.');           
        } else
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
    if (isset($reg_type) && $reg_type == REG_TYPE_CONFIRM) {
      print('<p>Account <B>' . $username . '</B> has been activated. The user may now login to the <a href="http://' . $host . $root_dir . '" target="_top">forum');      
      // post a welcome to forum
      // post('Welcome, ' . $username.'!', 'Your account has been activated, you may now login to the forum.');
      // send an email to the user
      $email_subject = "Your registration on $host forum website";
      $email_message = "Welcome, ". $username."!\n\nYour account has been activated, you may now login to the forum.";
      $email_headers = "From: $from_email";
      mail($email,$email_subject,$email_message,$email_headers);
    } else {
      print('<p><B>' . $username . '</B>, your account has been activated. Now you may login to the <a href="http://' . $host . $root_dir . '" target="_top">forum</a>');
    }
    require_once('tail_inc.php');
?>

