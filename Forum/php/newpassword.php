<?php
/*$Id: newpassword.php 883 2013-03-11 16:35:42Z dmitriy $*/
require_once('head_inc.php');
require_once('html_head_inc.php');
?>
<base target="bottom">
</head>
<body>
<?php
    if ((is_null($user) || strlen($user) == 0) || (is_null($email) || strlen($email) == 0)) {
        $err = 'Both username and email are required';
    } else {
        $query = 'SELECT id, username, email from confa_users where username = \'' . mysql_real_escape_string($user) . '\' and email = \'' . mysql_real_escape_string($email) . '\'';
        $result = mysql_query($query);
        if (!$result) {
            mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed');
        }
        if (mysql_num_rows($result) == 0) {
            $err = 'No user with username ' . htmlentities($user, HTML_ENTITIES,'UTF-8') . ' and email ' . htmlentities($email, HTML_ENTITIES,'UTF-8') . ' found';
        } else {
            do {
                if (mysql_num_rows($result) > 1) {
                    $err = 'There is more then 1 username registered with this email';
                    break;
                }
                $row = mysql_fetch_assoc($result);
                if ( is_null($row['email']) || strlen(trim($row['email'])) == 0) {
                    $err = 'Sorry, no email set in profile. Cannot generate and send new password'; 
                    break;
                }
                $user = $row['username'];
                $email = $row['email'];
                $userid = $row['id'];
                $newpass = generatePassword(6, 7);

                $to = $email;
                $subject = "Forum's new password";
                $message = $user . ", your new generated password is\n\n" . $newpass . "\n\nFeel free to login and change it in your profile";
                $from = $from_email;
                $headers = "From: $from";
                if (xmail($to,$subject,$message,$headers)) {
                  $query = 'UPDATE confa_users set password=password(\'' . $newpass . '\'), modified=NULL  where username=\'' . mysql_real_escape_string($user) . '\'';
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
                } else {
                  die("Email could not be sent");
                }                  
            } while(false);
        } 
    }
    if (strlen($err) > 0 ) {
        print('<font color="red"><b>' . $err . '</b></font>');
        require_once("forgot_inc.php");
    } else {
?><h3>Confirmation</h3>
Thank you, <b><?php print(htmlentities($user, HTML_ENTITIES,'UTF-8')); ?></b>!<br/><p>
New generated password has been sent to your email</p><p>
<?php
    }

require_once('tail_inc.php');
?>

