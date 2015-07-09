<?php
/*$Id: reg.php 803 2012-10-14 19:35:33Z dmitriy $*/

require_once('head_inc.php');
require_once('get_params_inc.php');
require_once('html_head_inc.php');

?>

<base target="bottom">
</head>
<body>
<table width="95%"><tr>
<td>
<h3><?php print($user);?></h3>
</td>

</tr></table>

<?php

    if (is_null($user) || strlen($user) == 0) {
        $err .= 'No username<BR>';
    } else {
        if (strlen($user) > 63) {
            $err .= 'Username is too long<BR>';
        } else {
            $query = 'SELECT username from confa_users where username = \'' . mysql_escape_string($user) . '\'';
            $result = mysql_query($query);
            if (!$result) {
                mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                die('Query failed');
            }
            if (mysql_num_rows($result) == 0) {
                # OK, may register if name was not parked.
                # first - delete outdated records
                $query = 'DELETE from confa_regs where timediff(current_timestamp, created) > 86400';
                $result = mysql_query($query);
                if (!$result) {
                    mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                    die('Query failed');
                }
                $query = 'SELECT username from confa_regs  where username = \'' . mysql_escape_string($user) . '\'';
                $result = mysql_query($query);
                if (!$result) {
                    mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                    die('Query failed');
                }
                if (mysql_num_rows($result) != 0) {
                    $err .= 'The name ' . htmlentities($user,HTML_ENTITIES,'UTF-8') . ' has been parked and awaiting activation.';
                }
            } else {
                $err .= 'User with the name ' . htmlentities($user,HTML_ENTITIES,'UTF-8') . ' already exists.';
            }
        }
    }
    if ( strlen( $err ) == 0) {
        if (is_null($password) || strlen($password) == 0) {
            $err .= 'No password<BR>';
        } else {
            if (strlen($password) > 16) {
                $err .= 'Password is too long<BR>';
            } else {
                if (strlen($password) < 4) {
                    $err .= 'Password is too short';
                } else {
                    if (is_null($password2) || strlen($password2) == 0 ) {
                        $err .= 'Please, retype password<BR>';
                    } else {
                        if (strcmp($password, $password2) != 0) {
                            $err .= 'Password doesnot match. Please, reenter.<BR>'; 
                        }
                    }
                }
            }
        }
        if (is_null($email) || strlen($email) == 0) {
            $err .= 'Email required<BR>';
        } else {
            if (strlen($email) > 80) {
                $err .= 'Email is too long<BR>';
            } else {
                if (is_null($email2) || strlen($email2) == 0 ) {
                    $err .= 'Please, retype email<BR>';
                } else {
                    if (strcmp($email, $email2) != 0) {
                        $err .= 'Email doesnot match. Please, reenter.<BR>'; 
                    } else {
                        $email_err = validateEmail($email);
                        if ( strlen($email_err) > 0 ){
                            $email_err .= '<BR>';
                            $err .= $email_err;
                        }
                    }
                }
            }
        }
    } 
    if (strlen($err) == 0) {
        $tm = date('Y-m-d H:i:s');
        $md5 = md5($tm . $user);
        $query = 'INSERT into confa_regs(username, password, email, actkey) values(\'' .  mysql_escape_string($user) . '\', password(\'' . $password . '\'), \'' . $email . '\', \'' . $md5 . '\')';
        $result = mysql_query( $query );
        if (!$result) {
            mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query); 
            die('Query failed');
        }
        if (mysql_affected_rows($link) != 1) {
            mysql_log( __FILE__, 'insert failed ' . mysql_error() . ' QUERY: ' . $query); 
            die('Query failed');
        }
        $to = $email;
        $subject = "Forum registration";
        $message = $user . ", to activate account, please click on the following link or copy an paste it in your browser.\n <a href=\"http://" . $host . $root_dir . $page_activate . '?act_link=' . $md5 . '">http://' . $host . $root_dir . $page_activate . '?act_link=' . $md5 . "</a> The link will be valid 86400 seconds (24 hours in human language).\n";
        $from = $from_email;
        $headers = "From: $from";
        mail($to,$subject,$message,$headers);
    } 
    if ($err != '') {
        print('<font color="red"><b>' . $err . '</b></font>');
        require_once("new_user_inc.php");
    } else {
        print("<B>" . $user . "</B>, activation link has been sent to " . $email . ". The link will be valid for 86400 seconds ( 24 hours )");
    }

require_once('tail_inc.php');

?>

