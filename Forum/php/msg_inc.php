<?php
/*$Id: msg_inc.php 988 2014-01-05 01:14:33Z dmitriy $*/

require_once('head_inc.php');

    $proceeded = false;
    $msg_status = 1;
    $views = 1;
    $in_response ='';

    // Performing SQL query to retrieve likes/dislikes
    $query = 'SELECT u.username as userlike, l.value as valuelike, l.reaction from confa_users u, confa_likes l where l.user=u.id and l.post=' . $msg_id;

    if ($logged_in) {
      $query .= ' and u.id not in (select i.ignored from confa_ignor i where i.ignored_by='.$user_id.')';
    }

    $result = mysql_query($query);
    if (!$result) {
        mysql_log( __FILE__, 'query 1 failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed');
    }
    
    $reads = '';
    $likes = '';
    $dislikes = '';
    $reaction = array(); // reaction to names
    
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
        } else if (!is_null($row['valuelike'])){
          if (strlen($reads) > 0) {
            $reads .= ', ';
          }
          $reads .= $row['userlike'];
        }
        if (!is_null($row['reaction']) && isset($reactions) && array_key_exists($row['reaction'], $reactions)) {
          if (array_key_exists($row['reaction'], $reaction)) {
            $reaction[$row['reaction']] .= ", ".$row['userlike'];
          } else {
            $reaction[$row['reaction']] = $row['userlike'];
          }
        }
    }
    mysql_free_result($result);

    // Performing SQL query to retrieve reports
    $query = 'SELECT u.username as user, r.content_flags as flags from confa_users u, confa_reports r where r.user=u.id and r.post=' . $msg_id;
    $result = mysql_query($query);
    if (!$result) {
        mysql_log( __FILE__, 'query 1 failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed');
    }
    
    $reports = array('nsfw' => '', 'boyan' => '');
    
    while($row = mysql_fetch_assoc($result)) {
        if ($row['flags'] & $content_nsfw) {
          if ($reports['nsfw'] != '') $reports['nsfw'] .= ', ';
          $reports['nsfw'] .= $row['user'];
        } 
        if ($row['flags'] & $content_boyan) {
          if ($reports['boyan'] != '') $reports['boyan'] .= ', ';
          $reports['boyan'] .= $row['user'];
        }
    }
    mysql_free_result($result);
    
    // Performing SQL query
    $query = 'SELECT u.username, u.moder, p.subject, p.closed as post_closed, p.views, p.id as msg_id, p.status, p.auth, p.parent, '
      . 'CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as created, CONVERT_TZ(p.modified, \'' . $server_tz . '\', \'' 
      . $prop_tz . ':00\') as modified, p.created as created_ts, p.body, p.author, u.id as id, t.closed as thread_closed, '
      .'(select max(page) from confa_threads) - t.page + 1 as page, p.thread_id, t.id, p.content_flags, t.author as t_author,'
      .'(select count(*) from confa_versions v where v.parent=p.id) as revisions,';
      
    if ($logged_in) {
      $query .= '(select count(*) from confa_ignor i where i.ignored=p.author and i.ignored_by='.$user_id.') as ignored,';
      $query .= '(select count(*) from confa_ignor i where i.ignored_by=p.author and i.ignored='.$user_id.') as ignoring,';
    } else {
      $query .= '0 as ignored, 0 as ignoring, ';
    }
    
    $query .= 't.properties as t_properties from confa_users u, confa_posts p, confa_threads t where p.thread_id=t.id and u.id=p.author and p.id=' . $msg_id;
      
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
        $auth_id = $row['author'];
        $msg_page = $row['page'];
        $created = $row['created'];
        $created_ts = $row['created_ts'];
        $modified = $row['modified'];
        $revisions = $row['revisions'];
        $msg_status = $row['status'];
        $content_flags = $row['content_flags'];
        $auth_ignored = $row['ignored'];
        $auth_ignoring = $row['ignoring'];
        if ( !is_null($row['post_closed']) && $row['post_closed'] > 0 ) {
            $post_closed = true;
        }
        if ( !is_null($row['thread_closed']) && $row['thread_closed'] > 0 ) {
            $thread_closed = true;
        }
        if ( $thread_closed || $post_closed ) {
            $reply_closed = true;
        }
        if ($content_flags & $content_nsfw) {
          $nsfw = true;
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
                $subj = 'This message has been deleted'; 
            }
        } else if (isset($attributes) && $attributes & $attr_hide_content_from_non_users && !$logged_in) {
                $msgbody = '<font color="gray">Please login for full experience.</font>';
                $subject = '<h3><font color="gray" size="14 pt">The user chose not to share the content of this message</font></h3>'; 
                $subj = 'The user chose not to share the content of this message';           
        } else {
            $translit_done = false;
            $msgbody = translit($row['body'], $translit_done);
            if (!is_null($msgbody) && strlen($msgbody) > 0 && !is_null($prefix) && strlen($prefix) > 0){
                $msgbody = $prefix . ' ' . str_replace("\n", "\n" . $prefix . ' ', $msgbody);
            }
            $msgbody = render_for_display($msgbody);
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
            $query = 'SELECT u.username, u.moder, p.subject, p.closed as post_closed, p.auth, p.status, p.parent, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as created, p.body, p.author, u.id user_id from confa_users u, confa_posts p where u.id=p.author and p.id=' . $parent;
            $result = mysql_query($query) or die('error to request parent message');
            if (mysql_num_rows($result) != 0) {
                $row = mysql_fetch_assoc($result) or die('error to fetch parent row');;
                $proceeded = false;
                $parent_author = htmlentities($row['username'], HTML_ENTITIES,'UTF-8');
                $parent_date = $row['created'];

                if ( $row['status'] == 2 ) {
                    $in_response = 'In response to: <I><font color="gray"><del>This message has been deleted</del></font></I>  by <b>' . $parent_author . '</b>' . ', ' . $parent_date . '<br> ';    
                } else {
                    get_show_hidden_and_ignored();
                    if ($ignored != null && in_array($row['user_id'], $ignored)) {
                      $in_response ='In response to: <font color="lightgrey"/>Hidden message</font>  by <b>' . $parent_author . '</b>, ' . $parent_date . '<br> ';
                    } else {
                      $parent_subject = htmlentities(translit($row['subject'], $proceeded), HTML_ENTITIES,'UTF-8');
                      $in_response ='In response to: <a href="' . $root_dir . $page_msg . '?id=' . $parent . '">' . $parent_subject . '</a> by <b>' . $parent_author . '</b>' . ', ' . $parent_date . '<br> ';
                    }
                }
            }
        }
    } else {
        die('No such message');
    }

    mysql_free_result($result);

    notify_about_new_pm($user_id, $last_pm_check_time, "bottom");
    
require_once('msg_form_inc.php');

?>



