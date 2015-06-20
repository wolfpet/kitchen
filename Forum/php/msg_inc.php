<?php
/*$Id: msg_inc.php 988 2014-01-05 01:14:33Z dmitriy $*/

require_once('head_inc.php');

    $proceeded = false;
    $msg_status = 1;
    $views = 1;
    $in_response ='';

    // Performing SQL query
    $query = 'SELECT u.username as userlike, l.value as valuelike from confa_users u, confa_likes l where l.user=u.id and l.post=' . $msg_id;
    $result = mysql_query($query);
    if (!$result) {
        mysql_log( __FILE__, 'query 1 failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed');
    }
    
    $reads = '';
    $likes = '';
    $dislikes = '';
    
    while($row = mysql_fetch_assoc($result)) {
        if ($row['valuelike'] > 0) {
          if (strlen($likes) > 0) {
            $likes .= ', ';
          }
          $likes .= $row['userlike'];
          if ($row['valuelike'] > 1) {
            $likes .= '(' . $row['valuelike'] . ')';
          }
        } else if ($row['valuelike'] < 0){
          if (strlen($dislikes) > 0) {
            $dislikes .= ', ';
          }
          $dislikes .= $row['userlike'];
          if ($row['valuelike'] < -1) {
            $dislikes .= '(' . ( 0 - $row['valuelike']) . ')';
          }
        } else {
          if (strlen($reads) > 0) {
            $reads .= ', ';
          }
          $reads .= $row['userlike'];
        }
    }
    mysql_free_result($result);

    // Performing SQL query
    $query = 'SELECT u.username, u.moder, p.subject, p.closed as post_closed, p.views, p.id as msg_id, p.status, p.auth, p.parent, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as created, p.body, p.author, u.id as id, t.closed as thread_closed, ( select max(page) from confa_threads) - t.page + 1 as page, p.thread_id, t.id, p.status, t.author as t_author, t.properties as t_properties from confa_users u, confa_posts p, confa_threads t where p.thread_id=t.id and u.id=p.author and p.id=' . $msg_id;
    $result = mysql_query($query);
    if (!$result) {
        mysql_log( __FILE__, 'query 2 failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed');
    }

    if (mysql_num_rows($result) != 0) {
        $row = mysql_fetch_assoc($result);
        $subject = htmlentities(translit($row['subject'], $proceeded), HTML_ENTITIES,'UTF-8');
        $subj = $subject;
        $author = htmlentities($row['username'], HTML_ENTITIES,'UTF-8');
        $msg_page = $row['page'];
        $created = $row['created'];
        $msg_status = $row['status'];
        if ( !is_null($row['post_closed']) && $row['post_closed'] > 0 ) {
            $post_closed = true;
        }
        if ( !is_null($row['thread_closed']) && $row['thread_closed'] > 0 ) {
            $thread_closed = true;
        }
        if ( $thread_closed || $post_closed ) {
            $reply_closed = true;
        }
        $views = $row['views'];
        if ($row['t_author'] == $user_id) {
          $thread_owner = true;
        }
        if ($row['status'] == 3) {
            if ( !is_null( $moder ) && $moder > 0 && !strcmp( $mode, "cens" ) ) {
                $translit_done = false;
                $msgbody = translit($row['body'], $translit_done);
                if (!is_null($msgbody) && strlen($msgbody) > 0 && !is_null($prefix) && strlen($prefix) > 0){
                    $msgbody = $prefix . ' ' . str_replace("\n", "\n" . $prefix . ' ', $msgbody);
                }
                $msgbody = htmlentities( $msgbody, HTML_ENTITIES,'UTF-8');
                $msgbody = before_bbcode($msgbody);
                $msgbody = do_bbcode ( $msgbody );
                $msgbody = nl2br($msgbody);
                $msgbody = after_bbcode($msgbody);
            } else { 
                $msgbody = '<font color="red">censored</font>';
            }
        } else if ($row['status'] == 2) {
            if ( !is_null( $moder ) && $moder > 0 && !strcmp( $mode, "del" ) ) {
                $translit_done = false;
                $msgbody = translit($row['body'], $translit_done);
                if (!is_null($msgbody) && strlen($msgbody) > 0 && !is_null($prefix) && strlen($prefix) > 0){
                    $msgbody = $prefix . ' ' . str_replace("\n", "\n" . $prefix . ' ', $msgbody);
                }
                $msgbody = htmlentities( $msgbody, HTML_ENTITIES,'UTF-8');
                $msgbody = before_bbcode($msgbody);
                $msgbody = do_bbcode ( $msgbody );
                $msgbody = nl2br($msgbody);
                $msgbody = after_bbcode($msgbody);
            } else {
                $msgbody = '';
                $subject = '<h3><I><font color="gray" size="14 pt"><del>This message has been deleted</del></font></I></h3>'; 
                $subj = 'This message has been deleted'; 
            }

        } else {
            $translit_done = false;
            $msgbody = translit($row['body'], $translit_done);
            if (!is_null($msgbody) && strlen($msgbody) > 0 && !is_null($prefix) && strlen($prefix) > 0){
                $msgbody = $prefix . ' ' . str_replace("\n", "\n" . $prefix . ' ', $msgbody);
            }
            $msgbody = htmlentities( $msgbody, HTML_ENTITIES,'UTF-8');
            $msgbody = before_bbcode($msgbody);
            $msgbody = do_bbcode ( $msgbody );
            $msgbody = nl2br($msgbody);
            $msgbody = after_bbcode($msgbody);
        }

        #Translit - start
        $trans_body = $msgbody; //translit($msgbody, $translit_done);
        if ($translit_done === true) {
            $trans_body .= '<BR><BR>[Message was transliterated]';
        }

        #Translit - end

        $start ='';
        $author = $author;
        $id = $row['id'];
        $msg_id = $row['msg_id'];
        $parent = $row['parent'];
        if (!is_null($parent)) {
            mysql_free_result($result);
            $query = 'SELECT u.username, u.moder, p.subject, p.closed as post_closed, p.auth, p.status, p.parent, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as created, p.body, p.author, u.id from confa_users u, confa_posts p where u.id=p.author and p.id=' . $parent;
            $result = mysql_query($query) or die('error to request parent message');
            if (mysql_num_rows($result) != 0) {
                $row = mysql_fetch_assoc($result) or die('error to fetch parent row');;
                $proceeded = false;
                $parent_author = htmlentities($row['username'], HTML_ENTITIES,'UTF-8');
                $parent_date = $row['created'];

                if ( $row['status'] == 2 ) {
                    $in_response = 'In response to: <I><font color="gray"><del>This message has been deleted</del></font></I>  by <b>' . $parent_author . '</b>' . ', ' . $parent_date . '<br> ';    
                } else { 
                    $parent_subject = htmlentities(translit($row['subject'], $proceeded), HTML_ENTITIES,'UTF-8');
                    $in_response ='In response to: <a href="' . $root_dir . $page_msg . '?id=' . $parent . '">' . $parent_subject . '</a> by <b>' . $parent_author . '</b>' . ', ' . $parent_date . '<br> ';
                }
            }
        }
    } else {
        die('No such message');
    }

    mysql_free_result($result);

  notify_about_new_pm($user_id, $last_login, "bottom");

require_once('msg_form_inc.php');

?>



