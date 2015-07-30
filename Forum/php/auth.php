<?php
/*$Id: auth.php 803 2012-10-14 19:35:33Z dmitriy $*/

    $logged_in = false;
    $ban = false;
    $new_pm = 0;

require_once('get_params_inc.php');

    if (!is_null($ip)) {
        $ip = '\'' . $ip . '\'';
    } else {
        $ip = 'NULL';
    }
    if (!is_null($agent)) {
        $agent = '\'' . mysql_real_escape_string($agent) . '\'';
    } else {
        $agent = 'NULL';
    }
    date_default_timezone_set('America/New_York');


    if (!is_null($auth_cookie) && !is_null($user)) {
        $query = 'SELECT u.id, u.status, u.ban, u.prop_tz, u.moder, s.safe_mode, u.prop_bold, u.ban_ends, u.new_pm, u.username, s.user_id, s.hash, s.updated, u.last_pm_check_time from confa_users u, confa_sessions s where u.id = s.user_id and s.hash =\'' . $auth_cookie . '\' and u.username = \'' . mysql_real_escape_string($user) . '\'';
        $result = mysql_query($query);
        if (!$result) {
            mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed');
        }

        if (mysql_num_rows($result) > 0) {
            $row = mysql_fetch_assoc($result);
            $ban_ends = $row['ban_ends'];
            $user_id = $row['id'];
            $moder = $row['moder'];
            $new_pm = $row['new_pm'];
            $prop_bold = $row['prop_bold'];
            $prop_tz = $row['prop_tz'];
            $status = $row['status'];
            $safe_mode = $row['safe_mode'];
            $last_login = $row['updated'];
            $last_pm_check_time = $row['last_pm_check_time'];
            if ( $status == 2 ) {
                $logout = true;
            }
            if (!is_null($ban_ends)) {
                $ban_time = strtotime($ban_ends);
                if ($ban_time > time()) {
                    $ban = true;
                    $query = 'SELECT CONVERT_TZ(ban_ends, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as ban_ends from confa_users where id=' . $user_id ;
                    $result = mysql_query($query);
                    if (!$result) {
                        mysql_log( __FILE__, 'insert failed ' . mysql_error() . ' QUERY: ' . $query);
                        die('Query failed');
                    }
                    $row = mysql_fetch_row($result);
                    $ban_ends = $row[0];
                } else {
                    #$logged_in = true;
                    $query = 'UPDATE confa_users set ban_ends = \'0000-00-00 00:00:00\' where id=' . $user_id;
                    $result = mysql_query($query);
                    if (!$result) {
                        mysql_log( __FILE__, 'insert failed ' . mysql_error() . ' QUERY: ' . $query);
                        die('Query failed');
                    }
                    $ban_ends = NULL;
                } 
                $logged_in = true;
            } else {
                $logged_in = true;
            }
            if (is_null($logout)) {
                $query = 'UPDATE confa_sessions set updated = current_timestamp where hash=\'' . $auth_cookie . '\'';
            } else {
                $logged_in = false;
                $query = 'DELETE from confa_sessions where hash=\'' . $auth_cookie . '\' and id=' . $user_id;
                setcookie('auth_cookie2', '', time() - 100000, $root_dir, $host, false, true);
            }
            $result = mysql_query($query);
            if (!$result) {
                mysql_log( __FILE__, 'insert failed ' . mysql_error() . ' QUERY: ' . $query);
                die('Query failed');
            }
            if ($logged_in /*&& !$ban*/) {
                $query = 'SELECT count(*) from confa_pm where receiver=' . $user_id . ' and status=1';
                $result = mysql_query($query);
                if (!$result) {
                    mysql_log( __FILE__, 'select failed ' . mysql_error() . ' QUERY: ' . $query);
                    die('Query failed');
                }
 
            }
 
        }
    }
?>