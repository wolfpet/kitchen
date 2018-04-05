<?php

require_once('head_inc.php');
require_once('get_params_inc.php');

if ( !is_null( $moder ) && $moder > 0 ) {

        $moduserid = $_GET['userid'];
        $grant = $_GET['grant'];
        $revoke = $_GET['revoke'];
        if($grant == 'yes'){ $query = 'Update confa_users set moder=1 where id=' . $moduserid;}
        if($revoke == 'yes'){ $query = 'Update confa_users set moder=null where id=' . $moduserid;}
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
}
else
{
    print( "<HTML><BODY>Access denied</BODY></HTML>" );
}

?>

