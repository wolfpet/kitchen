<?php
/*$Id: msg_inc.php 988 2014-01-05 01:14:33Z dmitriy $*/

require_once('head_inc.php');

    $status = 1;
    $views = 1;
    $in_response ='';

    // Performing SQL query
/*    
    $query = 'SELECT p.id, p.subject, p.views, p.status, p.parent, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' 
      . $prop_tz . ':00\') as created, p.body, p.content_flags, p.chars from confa_versions p where p.parent=' . $msg_id . ' and id <= '. $ver . 'order by p.created desc limit 2';
*/ 
    $query = 'SELECT u.username, u.moder, v.subject, p.closed as post_closed, v.views, p.id as msg_id, v.status, p.auth, p.parent, CONVERT_TZ(v.created, \'' 
      . $server_tz . '\', \'' . $prop_tz . ':00\') as created, v.body, p.author, u.id as id, t.closed as thread_closed, p.thread_id, t.id, t.author as t_author,'
      . 'p.subject as msg_subject, p.body as msg_body '
      . 'from confa_users u, confa_posts p, confa_threads t, confa_versions v where p.thread_id=t.id and u.id=p.author and p.id=v.parent and p.id=' . $msg_id . ' and v.id=' . $version;
    $result = mysql_query($query);
    if (!$result) {
        mysql_log( __FILE__, 'query 2 failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed');
    }

    if (mysql_num_rows($result) != 0) {
        $row = mysql_fetch_assoc($result);
        $subject = htmlentities(translit($row['subject'], $proceeded), HTML_ENTITIES,'UTF-8');
        $author = htmlentities($row['username'], HTML_ENTITIES,'UTF-8');
        $auth_id = $row['author'];
        $created = $row['created'];
        $status = $row['status'];
        $views = $row['views'];
        if ($row['status'] == 3) {
          if ( !is_null( $moder ) && $moder > 0 && !strcmp( $mode, "cens" ) ) {
            $translit_done = false;
            $msgbody = translit($row['body'], $translit_done);
            if (!is_null($msgbody) && strlen($msgbody) > 0 && !is_null($prefix) && strlen($prefix) > 0){
                $msgbody = $prefix . ' ' . str_replace("\n", "\n" . $prefix . ' ', $msgbody);
            }
            $msgbody = render_for_display($msgbody);
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
            $msgbody = render_for_display($msgbody);
          } else {
            $msgbody = '';
            $subject = '<h3><I><font color="gray" size="14 pt"><del>This message has been deleted</del></font></I></h3>'; 
          }
        } else {
          $translit_done = false;
          $msgbody = translit($row['body'], $translit_done);
          if (!is_null($msgbody) && strlen($msgbody) > 0 && !is_null($prefix) && strlen($prefix) > 0){
              $msgbody = $prefix . ' ' . str_replace("\n", "\n" . $prefix . ' ', $msgbody);
          }
          $msgbody = render_for_display($msgbody);
        }

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

    notify_about_new_pm($user_id, $last_pm_check_time, "bottom");
    
    $modified = null;
    $trans_body = $msgbody;
    
require_once('msg_form_inc.php');

?>



