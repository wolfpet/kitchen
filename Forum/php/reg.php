<?php
/*$Id: reg.php 803 2012-10-14 19:35:33Z dmitriy $*/

require_once('head_inc.php');
require_once('get_params_inc.php');
require_once('html_head_inc.php');

?>
<base target="bottom">
<?php
if (isset($recaptcha_site_key) && isset($recaptcha_secret_key)) {
?>
<script src='https://www.google.com/recaptcha/api.js'></script>
<?php 
}
?>
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
		if (isset($recaptcha_site_key) && isset($recaptcha_secret_key)) {
			$captcha = $_POST['g-recaptcha-response'];
			if ($captcha) {
				// Verify captcha
				$options = array(
					'http' => array(
						'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
						'method'  => 'POST',
						'content' => http_build_query(array('secret' => $recaptcha_secret_key, 'response' => $captcha, 'remoteip' => $ip)),
					),
				);
				$context  = stream_context_create($options);
				$result = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
				
				if ($result === FALSE) { /* Handle error */ 
					$err .= 'reCAPTCHA check failed<BR>';
				} else {
					// var_dump($result)
		      $ar2 = json_decode($result);
					if (!$ar2->success) {
						$err .= "Sorry, your reCAPTCHA response is invalid<BR>";
					}
				}
			} else {
				$err .= 'Please prove that you are not a bot by completing reCAPTCHA<BR>';
			}
		}
        if (strlen($user) > 63) {
            $err .= 'Username is too long<BR>';
        } else if ( strlen( $err ) == 0) {
            $query = 'SELECT username from confa_users where username = \'' . mysql_real_escape_string($user) . '\'';
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
                $query = 'SELECT username from confa_regs  where username = \'' . mysql_real_escape_string($user) . '\'';
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
    if ( strlen( $err ) == 0) {
        if (is_null($subj) || strlen($subj) < 10) {
            $err .= 'Please tell us about yourself and why you want join this forum<BR>';
        } else if (strlen($subj) > 200) {
          $subj = substr($subj, 200);
        }
    }
    if (strlen($err) == 0) {
        $tm = date('Y-m-d H:i:s');
        $md5 = md5($tm . $user);
        $query = 'INSERT into confa_regs(username, password, email, actkey, description) values(\'' .  mysql_real_escape_string($user) . '\', password(\'' . mysql_real_escape_string($password) 
        . '\'), \'' . mysql_real_escape_string($email) . '\', \'' . $md5 . '\', \'' . mysql_real_escape_string($subj) . '\')';
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
        $message = "To activate your account, please click the following link or copy and paste it in your browser:<p><a href=\"".$protocol."://" . $host . $root_dir . $page_activate . '?act_link=' . $md5 . '">'.$protocol.'://' . $host . $root_dir . $page_activate . '?act_link=' . $md5 . "</a><p>This link will be valid for 24 hours.\n";
        $from = $from_email;
        $headers = "From: $from";
        if ( !isset($reg_type) || $reg_type == REG_TYPE_OPEN )
          print($message);
        else if ( isset($reg_type) && $reg_type == REG_TYPE_EMAIL ) {
          mail($to,$subject,$message,$headers);
          print("<B>" . $user . "</B>, activation link has been sent to " . htmlentities($email, HTML_ENTITIES,'UTF-8') . ". The link will be valid for 86400 seconds ( 24 hours )");
        } else if ( isset($reg_type) && $reg_type == REG_TYPE_CONFIRM ) {
          print("In order to activate your account, your request needs to be approved by a moderator. Until that time, feel free to read the forum.");
        } else
          // Somebody is hacking because with closed registration user would never get here
          print "Go away. If you don't close this page in 30 seconds your hard drive will be formatted.";
    } 
    if ($err != '') {
        print('<font color="red"><b>' . $err . '</b></font>');
        require_once("new_user_inc.php");
    }

require_once('tail_inc.php');

?>

