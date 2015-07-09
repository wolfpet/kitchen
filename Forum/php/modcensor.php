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

require("msg_inc.php");



if ( $reply_closed ) {

?>
Closed |
<?php
} else {
?>



<a href="<?php print($root_dir . $page_new); ?>?re=<?php print($msg_id); ?>">Reply</a> |
<span style="background-color: rgb(224, 224, 224);"><a href="<?php print( $root_dir . $page_pmail_send . '?to=' . $author); ?>">Reply to sender (private)</a> </span>|
<?php
}
?>

<a target="contents" name="<?php print($msg_id); ?>" href="<?php print($root_dir . $page_expanded); ?>?page=<?php print($msg_page . '#' .$msg_id);?>">Synchronize</a> |
<a target="bottom" href="<?php print($root_dir . $page_thread); ?>?id=<?php print($msg_id); ?>">Thread</a>
<?php

        if ( !is_null( $moder ) && $moder > 0 ) {
            print( '&nbsp;&nbsp;&nbsp;<SPAN STYLE="background-color: #FFE0E0">[ ' );
            if ( $msg_status == 3 ) {
                print( '<a href="' . $root_dir . 'modcensor.php' . '?action=uncensor&id=' . $msg_id . '"><font color="green">Uncensor message</font></A> |' );
            } else {
                print( '<a href="' . $root_dir . 'modcensor.php' . '?action=censor&id=' . $msg_id . '"><font color="green">Censor message</font></A> |' );
            }
            if ( $msg_status == 2 ) {
                print( ' <a href="' . $root_dir . 'modcensor.php' . '?action=undelete&id=' . $msg_id . '"><font color="green">Undelete message</font></A> |' );
            } else {
                print( ' <a href="' . $root_dir . 'modcensor.php' . '?action=delete&id=' . $msg_id . '"><font color="green">Delete message</font></A> |' );
            }
            if ( $thread_closed ) {
                print( ' <a href="' . $root_dir . 'modcensor.php' . '?action=openthread&id=' . $msg_id . '"><font color="green">Open thread</font></A> |' );
            } else {
                print( ' <a href="' . $root_dir . 'modcensor.php' . '?action=closethread&id=' . $msg_id . '"><font color="green">Close thread</font></A> |' );
            }
            if ( $post_closed ) {
                print( ' <a href="' . $root_dir . 'modcensor.php' . '?action=openpost&id=' . $msg_id . '"><font color="green">Open post</font></A> ' );
            } else {
                print( ' <a href="' . $root_dir . 'modcensor.php' . '?action=closepost&id=' . $msg_id . '"><font color="green">Close post</font></A> ' );
            }

            print( ']</SPAN>' );
        }

    } else {
        print(" You have no rights to access this page." );
    }
?>

</body>
</html>

