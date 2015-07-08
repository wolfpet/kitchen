<?php
/*$Id: login_inc.php 840 2012-11-22 01:34:47Z dmitriy $*/

    $new_pm = 0;

require_once('head_inc.php');

    $logged_in = false;
    do {
        if (is_null($user) || strlen($user) == 0) {
            $err_login = 'Username is required';
            break;
        }  
        if (is_null($password) || strlen($password) == 0) {
            $err_login = 'Password is required';
            break;
        }     
        $query = 'SELECT id, username, password, prop_bold, prop_tz, status, moder, ban, ban_ends, new_pm  FROM confa_users where username = \'' . mysql_escape_string($user) . '\' and password=password(\'' . mysql_escape_string($password) . '\')';
        $result = mysql_query($query);
        if (!$result) {
            mysql_log( __FILE__ . ':' . __LINE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed');
        }
        mysql_log( __FILE__ . ':' . __LINE__, 'query succeded numrows= ' . mysql_num_rows($result) . ' for username= ' . $user . ' QUERY: ' . $query);
        if (mysql_num_rows($result)  < 1) {
        mysql_log( __FILE__ . ':' . __LINE__, 'num_rows < 1' . $user . ' QUERY: ' . $query);
            $query = 'SELECT id, username, password, prop_bold, prop_tz, status, moder, ban, ban_ends, new_pm  FROM confa_users where username = \'' . mysql_escape_string($user) . '\' and password=old_password(\'' . mysql_escape_string($password) . '\')';
            $result = mysql_query($query);
        mysql_log( __FILE__ . ':' . __LINE__, 'result ' . $user . ' QUERY: ' . $query);
            if (!$result) {
                mysql_log( __FILE__ . ':' . __LINE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                die('Query failed');
            } 
        mysql_log( __FILE__ . ':' . __LINE__, '2 ' . $user . ' QUERY: ' . $query);
            if (mysql_num_rows($result) == 0) {
                $err_login = 'Wrong password or username ' . $user . ' ' . $password ;
                break;
            } else {
                mysql_log( __FILE__ . ':' . __LINE__, 'successfull login with old password');

            }
        }
        $ban = false;
        $row = mysql_fetch_assoc($result);
        $user_id = $row["id"];
        $db_pass = $row["password"];
        $ban_ends = $row["ban_ends"];
        $new_pm = $row["new_pm"];
        $prop_bold = $row["prop_bold"];
        $prop_tz  = $row['prop_tz'];
        if (is_null($prop_tz)) {
          $prop_tz = explode(":", $server_tz)[0];
        }
        if ( $row['status'] == 2 ) {
            $err_login = ' This user has been disabled.';
            break;
        }
        mysql_log( __FILE__, 'before ban ends ' . $user . ' QUERY: ' . $query);
        if (!is_null($ban_ends)) {
            $ban_time = strtotime($ban_ends);
            if ($ban_time > time()) {
                $ban = true;
                #$err_login = 'Sorry, you have been banned form this forum till ' . $ban_ends;
                $query = 'SELECT CONVERT_TZ(ban_ends, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as ban_ends from confa_users where id=' . $user_id ;
                $result = mysql_query($query);
                if (!$result) {
                    mysql_log( __FILE__, 'insert failed ' . mysql_error() . ' QUERY: ' . $query);
                    die('Query failed');
                }
                $row = mysql_fetch_row($result);
                $ban_ends = $row[0];

            } else {
                $query = 'UPDATE confa_users set ban_ends = \'0000-00-00 00:00:00\' where id=' . $user_id;
                $result = mysql_query($query);
                if (!$result) {
                    mysql_log( __FILE__, 'update failed ' . mysql_error() . ' QUERY: ' . $query);
                    die('Query failed');
                }
                $ban_ends = NULL;
            }
        }
        $tm = date('Y-m-d H:i:s');
        $md5 = md5($tm . $ip . $user);
        $auth = '1';
        $logged_in = true;
        
        $query = 'INSERT into confa_sessions(created, user_id, hash) values(\'' . $tm . '\', ' . $user_id . ', \'' . $md5 . '\')';
        
        $result = mysql_query($query); 
        if (!$result) {
            mysql_log( __FILE__, 'insert failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed');
        }
        
        mysql_log( __FILE__, 'insert query succeded for username= ' . $user . ' QUERY: ' . $query);
        
        setcookie('auth_cookie2', $md5, 1800000000, $root_dir, $host, false, true);
        setcookie('user2', $user, 1800000000, $root_dir, $host, false, true);

    } while(false);

?>

