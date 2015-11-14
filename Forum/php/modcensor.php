<?php
/*$Id: modcensor.php 416 2010-02-26 16:38:39Z dmitriy $*/

require_once('head_inc.php');
require_once('get_params_inc.php');
require_once('html_head_inc.php');

?>

<base target="bottom">
</head>
<body>

<?php

    if ( !is_null($moder) && $moder > 0 && !is_null( $msg_id ) && $msg_id > 0 ) {

        if ( !is_null($action) ) {
            $query = '';
            if (!strcmp( $action, "censor" ) ) {
                $query = ' UPDATE confa_posts set status=3 where id=' . $msg_id; 
            }
            if ( !strcmp( $action, "uncensor" ) ) {
                $query = ' UPDATE confa_posts set status=1 where id=' . $msg_id; 
            }
            if ( !strcmp( $action, "delete" ) ) {
                $query = ' UPDATE confa_posts set status=2 where id=' . $msg_id; 
            }
            if ( !strcmp( $action, "undelete" ) ) {
                $query = ' UPDATE confa_posts set status=1 where id=' . $msg_id; 
            }
            if ( !strcmp( $action, "closepost" ) ) {
                $query = ' UPDATE confa_posts set closed=1 where id=' . $msg_id; 
            }
            if ( !strcmp( $action, "openpost" ) ) {
                $query = ' UPDATE confa_posts set closed=0 where id=' . $msg_id; 
            }
            if ( !strcmp( $action, "closethread" ) ) {
                $query = ' SELECT thread_id from confa_posts where id=' . $msg_id;
                $result = mysql_query( $query );
                if (!$result) {
                    mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                    die('Query failed');
                }
                $row = mysql_fetch_assoc($result);
                $thread_id = $row['thread_id'];
                if (is_null($thread_id)) {
                    die('cannot find thread_id for message ' . $msg_id);
                }
                $query = ' UPDATE confa_threads set closed=1 where id=' . $thread_id; 
            }
            if ( !strcmp( $action, "openthread" ) ) {
                $query = ' SELECT thread_id from confa_posts where id=' . $msg_id;
                $result = mysql_query( $query );
                if (!$result) {
                    mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                    die('Query failed');
                }
                $row = mysql_fetch_assoc($result);
                $thread_id = $row['thread_id'];
                if (is_null($thread_id)) {
                    die('cannot find thread_id for message ' . $msg_id);
                }
                $query = ' UPDATE confa_threads set closed=0 where id=' . $thread_id; 
            }
            if ( strlen ( $query ) > 0 ) {
                $result = mysql_query( $query );
                if (!$result) {
                    mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                    die('Query failed');
                }
            }
        }

        $action = "";
        
require("msg.php");

   } else {
        print(" You have no rights to access this page." );
   }
?>

</body>
</html>

