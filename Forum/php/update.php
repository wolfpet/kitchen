<?php
/*$Id: update.php 378 2009-11-02 19:36:24Z dmitriy $*/

require_once('head_inc.php');
require_once('html_head_inc.php');

    $title = 'Profile Settings';

    $info = '';
    if (is_null($user) || strlen($user) == 0) {
        $err .= 'No username<BR>';
    } else if ($logged_in) {
        do {
            $update = '';
            if (!is_null($password) && strlen($password) > 0 ) {
                if (is_null($password2) || strlen($password2) == 0) {
                    $err = 'Please, retype password';
                    break;
                } else {
                    if (strcmp($password, $password2)) {
                        $err = 'Password does not match';
                        break;
                    }
                    if (strlen($password) < 4) {
                        $err = 'Password is too short';
                        break;
                    }
                    if (strlen($password) > 16) {
                        $err = 'Password is too long';
                    }
                    $update = ' password=password(\'' . $password . '\')';
                } 
            }
            if (is_null($email) || strlen($email) == 0) {
                if (strlen($update) == 0) {
                    $err = 'At least one - email or password should be populated for the update';
                    break;
                }
            } else {
                if (is_null($email2) || strlen($email2) == 0) {
                    $err = 'Please, retype email';
                    break;
                }
                if (strcmp($email, $email2)) {
                    $err = 'Email does not match';
                    break; 
                }
                $err = validateEmail($email);
                if (strlen($err) > 0 ){
                    break;
                }
                if (strlen($update) > 0) {
                    $update .= ', ';
                }
                $update .= 'email=\'' . mysql_real_escape_string( $email ). '\' ';
            }
            if (strlen($update) > 0) {
                $update .= ', ';
            }
            if ( is_null($profile_bold) ) {
                $profile_bold = 0;
            }
            $update .= ' prop_bold=' . $profile_bold; 

            if (strlen($update) > 0) {
                $update .= ', ';
            }
            $update .= ' show_smileys=' . (isset($show_smileys) ? "1" : "0"); 

            if (strlen($update) > 0) {
                $update .= ', ';
            }
 
            $update .= ' reply_to_email=' . (isset($send_reply_to_email) ? "1" : "0") . ', '; 

           $update .= ' menu_style=' . (isset($send_menu_style) ? "1" : "0") . ', '; 
            
            if (is_null($tz)) {
                $tz = explode(":", $server_tz)[0];
            }
            
            $update .= ' prop_tz=\'' . mysql_real_escape_string($tz) . '\''; 
            
            $query = 'UPDATE confa_users set ' . $update . ' where id=' . $user_id; 
            $result = mysql_query($query);
            if (!$result) {
                mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                die('Query failed');
            }
            $info = 'Successfully updated.';
            $prop_bold = $profile_bold;
            $prop_tz_name = $tz;
            $prop_tz = get_tz_offset($tz);
            $smileys = isset($show_smileys);
            $reply_to_email = isset($send_reply_to_email);
            $menu_style = isset($send_menu_style) ? 1 : 0;
        } while (false);
    }


    if ($err != '') {
        //print('<font color="red"><b>' . $err . '</b></font>');
                 $title = $title . ': ' . $err;
    } else {
        if (strlen($info) > 0) {
            //print('<font color="green"><b>' . $info . '</b></font>');
		$title = $title . ': '. $info;
        }
    }
?>

<base target="bottom">
<link rel="stylesheet" type="text/css" href="css/spectrum.css">
<script type="text/javascript" src="js/profinit.js"></script>
<script type="text/javascript" src="js/spectrum.js"></script>
<script type="text/javascript" src="js/profcolors.js"></script>

</head>
<body>
<table width="95%"><tr>
<td>
<h3><?php print($title);?></h3>
</td>

</tr></table>

<?php

require_once("profile_inc.php");
require_once('tail_inc.php');

?>

