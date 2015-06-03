<?php
/*$Id: newpassword.php 883 2013-03-11 16:35:42Z dmitriy $*/
require_once('head_inc.php');
require_once('get_params_inc.php');
require_once('html_head_inc.php');
require_once('mail.php');

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

    if ((is_null($user) || strlen($user) == 0) && (is_null($email) || strlen($email) == 0)) {
        $err .= 'Either - username or email is required<BR>';
    } else {
        if ( !is_null($user) && strlen($user) > 0) {
            $query = 'SELECT id, username, email from confa_users where username = \'' . mysql_escape_string($user) . '\'';
        } else {
            $query = 'SELECT id, username, email from confa_users where email = \'' . mysql_escape_string($email) . '\'';
        }
        $result = mysql_query($query);
        if (!$result) {
            mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed');
        }
        if (mysql_num_rows($result) == 0) {
            $err = 'No user with';
            if (!is_null($user) && strlen($user) > 0 ) {
                $err .= ' username ' . htmlentities($user,HTML_ENTITIES,'UTF-8'); 
            }
            if (!is_null($email) && strlen($email) > 0 ) {
                $err .= ' email ' . $email; 
            }
            $err .= ' in the database';
        } else {
            do {
                if (mysql_num_rows($result) > 1) {
                    $err = 'There is more then 1 username registered with this email';
                    break;
                }
                $row = mysql_fetch_assoc($result);
                if ( is_null($row['email']) || strlen($row['email']) == 0) {
                    $err = 'Sorry, no email in profile. Cannot generate and send new password.'; 
                    break;
                }
                if (!is_null($email) && strlen($email) > 0 && strcasecmp($email, $row['email'])) {
                    $err = 'Email you have entered is not in the database.';
                    break; 
                }
                $user = $row['username'];
                $email = $row['email'];
                $userid = $row['id'];
                $newpass = generatePassword(6, 7);
                $query = 'UPDATE confa_users set password=password(\'' . $newpass . '\'), modified=NULL  where username=\'' . $user . '\'';
                $result = mysql_query($query);
                if (!$result) {
                    mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                    die('Query failed');
                }
                if (mysql_affected_rows($link) != 1) {
                    mysql_log( __FILE__, 'insert updated password failed ' . mysql_error() . ' QUERY: ' . $query);
                    die('Query failed');
                }
                $query = 'DELETE from confa_sessions where user_id=' . $userid;
                $result = mysql_query($query);
                $to = $email;
                $subject = "Forum's new password";
                $message = $user . ", your new generated password is\n" . $newpass . "\nFeel free to login and change it in your profile";
                $from = $from_email;
                $headers = "From: $from";
                send_mail($to,$subject,$message);
            } while(false);
        } 
    }
    if (strlen($err) > 0 ) {
        print('<font color="red"><b>' . $err . '</b></font>');
        require_once("forgot_inc.php");
    } else {
        print("<B>" . $user . "</B>, new generated password has been sent to your email\n");// . $email . "\n");
    }

require_once('tail_inc.php');

?>

