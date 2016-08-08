<?php
/*$Id: modban.php 389 2009-11-19 18:58:08Z dmitriy $*/

require_once('head_inc.php');
require_once('get_params_inc.php');

?>

<?php
    if ( !is_null( $moder ) && $moder > 0 ) {
        #For ban history:
        if ( $bantime > 0 && ( is_null( $ban_reason ) || strlen(trim($ban_reason)) == 0 ) ) {
            $err_ban_reason='Reason is required';
require_once('moduser.php');
            exit;
        }
        $ban_id = 0;
        $query = null;
        if ( $bantime == 'max') {
            $query = 'INSERT into confa_ban_history( moder, expires, victim, ban_reason) values( ' . $user_id . ', \'2038-01-18\', ' . $moduserid . ', \'' . mysql_real_escape_string( trim( $ban_reason ) ) . '\' )';
        } else if ( strrpos($bantime, 'y') > 0) {
            $query = 'INSERT into confa_ban_history( moder, expires, victim, ban_reason) values( ' . $user_id . ', addtime(adddate( curdate(), INTERVAL ' . substr($bantime, 0, strlen($bantime) - 1) . ' YEAR), time(current_timestamp())), ' . $moduserid . ', \'' . mysql_real_escape_string( trim( $ban_reason ) ) . '\' )';
        } else if ( strrpos($bantime, 'm') > 0) {
            $query = 'INSERT into confa_ban_history( moder, expires, victim, ban_reason) values( ' . $user_id . ', addtime(adddate( curdate(), INTERVAL ' . substr($bantime, 0, strlen($bantime) - 1) . ' MONTH), time(current_timestamp())), ' . $moduserid . ', \'' . mysql_real_escape_string( trim( $ban_reason ) ) . '\' )';
        } else if ( $bantime > 0 ) {
            $query = 'INSERT into confa_ban_history( moder, expires, victim, ban_reason) values( ' . $user_id . ', addtime( current_timestamp(), \'' . $bantime . ':00\'), ' . $moduserid . ', \'' . mysql_real_escape_string( trim( $ban_reason ) ) . '\' )';
        } else if ($bantime == -1) {
            $query = 'INSERT into confa_ban_history( moder, expires, victim) values( ' . $user_id . ', \'0000-00-00 00:00:00\', ' . $moduserid . ' )';
        }
        if (!is_null($query)) {
          $result = mysql_query($query);
          if (!$result) {
              mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
              die('Query failed');
          }
          $ban_id = mysql_insert_id();
        }
        if ($bantime == 'p') {          
            $query = 'Update confa_users set status=2 where id=' . $moduserid;
        } else if ($bantime == 'max') {          
            $query = 'Update confa_users set ban_ends=\'2038-01-18\', ban=' . $ban_id . ' where id=' . $moduserid;
        } else if ( strrpos($bantime, 'y') > 0) {
            $query = 'Update confa_users set ban_ends=addtime(adddate( curdate(), INTERVAL ' . substr($bantime, 0, strlen($bantime) - 1) . ' YEAR), time(current_timestamp())), ban=' . $ban_id . ' where id=' . $moduserid;
        } else if ( strrpos($bantime, 'm') > 0) {
            $query = 'Update confa_users set ban_ends=addtime(adddate( curdate(), INTERVAL ' . substr($bantime, 0, strlen($bantime) - 1) . ' MONTH), time(current_timestamp())), ban=' . $ban_id . ' where id=' . $moduserid;
        } else if ( $bantime > 0 ) {
            $query = 'Update confa_users set ban_ends=addtime( current_timestamp(), \'' . $bantime . ':00\'), ban=' . $ban_id . ' where id=' . $moduserid;
        } else {
            $query = 'Update confa_users set pban=0, ban_ends=\'0000-00-00 00:00:00\',  ban=' . $ban_id . '  where id=' . $moduserid;
        }
        
        $result = mysql_query($query);
        if (!$result) {
            mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed');
        }

        if ( mysql_affected_rows( $link ) == 0 ) {
            mysql_log( __FILE__, '0 affected rows ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed');
        }
        
require('moduser.php');

    } else {
        print( "<HTML><BODY>You have no access to this page.</BODY></HTML>" );
    }

?>

