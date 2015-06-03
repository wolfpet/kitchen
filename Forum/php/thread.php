<?php
/*$Id: thread.php 988 2014-01-05 01:14:33Z dmitriy $*/

require_once('head_inc.php');
require_once('get_params_inc.php');
require_once('html_head_inc.php');

?>

<base target="bottom">
</head>
<body>
<?php

    $prefix = NULL;
    $proceeded = false;
    $thread_id = NULL;


    if (is_null($msg_id)) {
        die('No message id supplied');
    }



    function print_msgs2($ar, $msgs, $msg_id) {
        $keys = array_keys($ar);
        print("<dl><dd>\n");
        foreach ($keys as $key) {
            print($msgs[$key]);
            print("<BR>\n");
            if (sizeof($ar[$key]) > 0) {
                print_msgs2($ar[$key], $msgs, $msg_id);
            }
        }
        print("</dd></dl>\n");
    }

    // Connecting, selecting database
    // Performing SQL query
    $query = 'SELECT thread_id from confa_posts where id=' . $msg_id;
    $result = mysql_query($query) or die('Query count failed: ' . mysql_error());
    $row = mysql_fetch_assoc($result);
    $thread_id = $row['thread_id'];
    if (is_null($thread_id)) {
        die('No thread found for message id=' . $msg_id);
    }

  
    mysql_free_result($result);
    $query = 'SELECT u.username, u.moder, p.auth, p.parent, p.closed as post_closed, p.views, p.likes, p.dislikes, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'EST\')  as created, p.subject, p.body, p.status, p.content_flags, LENGTH(IFNULL(p.body,"")) as len, p.thread_id, p.level, p.id as id, p.chars, p.page, t.closed as t_closed from confa_posts p, confa_users u, confa_threads t ';
    $query .= ' where p.author=u.id and thread_id = ' . $thread_id . ' and thread_id=t.id order by thread_id desc, level, id desc';
    $result = mysql_query($query) or die('Query  failed ');
    $msgs = array();
    $content = array();
    $cur_content = &$content;
    $stack = array();
    $stack[0] = &$content;
    $level = 0;
    $armass = array();
    $glob = array();
    $l = 0;

    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $length = $row['chars'];
        if (is_null($length)) {
            $length =  $row['len'];
        }
        $armass[$l] = array();
        $u_moder = $row['moder'];
        $subj = $row['subject'];
        $subj = htmlentities(translit($subj, $proceeded),  HTML_ENTITIES,'UTF-8');
        $enc_user = htmlentities($row['username'], HTML_ENTITIES,'UTF-8');

        $img = '';
        $thread_closed = $row['t_closed'];
        if ($row['level'] == 0) {
            if ($thread_closed != 0) {
              $img = '<img border=0 src="images/cs.gif" width=16 height=16 alt="*">';
            } else {
              $img = '<img border=0 src="images/bs.gif" width=16 height=16 alt="*">';
            }
        } else {
            $img = '<img border=0 src="images/dc.gif" width=16 height=16 alt="*">';
        }

        if ( $row['status'] == 2 ) {
            $line = '&nbsp;' . $img . ' <I><font color="gray"><del>This message has been deleted</del></font></I> ';
        } else {
            if ($row['id'] == $msg_id) {
                $line = '&nbsp;' . $img . ' ' . $subj;
            } else {
        $icons = '';
        if ($row['content_flags'] & 0x02) {
          $icons = ' <img border=0 src="' . $root_dir . $image_img . '"/> ';
        }
        if ($row['content_flags'] & 0x04) {
          $icons .= ' <img border=0 src="' . $root_dir . $youtube_img . '"/> ';
        }

                $line = '&nbsp;<a name="' . $row['id'] . '" target="bottom" href="' . $root_dir . $page_thread . '?id=' . $row['id'] . '">' . $img . $icons . $subj . '  </a> ';
            }
        }
        $line .= ' <b>' . $enc_user . '</b>' .  ' ' . '[' . $row['views'] . ' views] ' . $row[    'created'] . ' <b>' . $length . '</b> bytes';
if (!is_null($row['likes'])) {
          $likes = $row['likes'];
          if ($likes > 0) {
            $line .= ' <font color="green"><b>+' . $likes . '</b></font>';
          }
        }
        if (!is_null($row['dislikes'])) {
          $dislikes = $row['dislikes'];
          if ($dislikes > 0) {
            $line .= ' <font color="red"><b>-' . $dislikes . '</b></font>';
          }
        }

        $msgs[$row['id']] = $line;
        if ($row['level'] == 0) {
            $content[$row['id']] = &$armass[$l];
            $glob[$row['id']] = &$armass[$l];
        } else {
            $cur_content = &$glob[$row['parent']];
            $cur_content[$row['id']] = &$armass[$l];
            $glob[$row['id']] = &$armass[$l];
        }

        $l++;
    } 
    $query = 'UPDATE confa_posts set views=views + 1 where id=' . $msg_id;
    $result = mysql_query($query);
    if (!$result) {
        mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed');
    }

require('msg_inc.php');

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



<a target="contents" name="<?php print($msg_id); ?>" href="<?php print($root_dir . $page_expanded); ?>?page=<?php print($msg_page . '#' .$msg_id);?>">Synchronize</a> 
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
    print_msgs2($content, $msgs, $msg_id);

require_once('tail_inc.php');

?>


</table>
</body>
</html>

