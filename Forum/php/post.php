<?php
/*$Id: post.php 986 2014-01-01 20:42:58Z dmitriy $*/

    $ibody       = 'NULL';

    $msg_page = 1;

require_once('head_inc.php');

    $title = 'New message';
    if ($logged_in == false ) {
require_once('login_inc.php');
    }
require_once('html_head_inc.php');
require_once('dump.php');
?>
</head>
<body>
<?php
    do {
        if (!is_null($err_login) && strlen($err_login) > 0 ) {
            $err = $err_login;
            break;
        }
        if (!$logged_in) {
            $err = 'You are not logged in';
            break;
        }
        if ($ban) {
            $err = 'You have been banned from this forum till ' . $ban_ends;
            break;
        }
        if (strlen($subj) > 254) {
            $err .= "Subject longer 254 bytes<BR>";
        }
        if (strlen(trim($subj)) == 0) {
            $err .= "No subject</BR>";
        }
        if (!is_null($body) && strlen($body) > 32765) {
            $err .= "Body longer 32765 bytes<BR>";
        }
        $chars = 0;
        if (!is_null($body) && strlen($body) != 0) {
           $chars = strlen(utf8_decode($body));
           $length = strlen($body);
/* This mechanism actually shoud be done using mb_xxx string function, but it should work */
            if (stristr(do_bbcode($body), "<img src=\"")) {
                $content_flags |= 2;
            }
            $new_body = youtube($body);
            if (strcmp($body, $new_body) != 0 || /* check for vimeo/coub/fb clips */ strcmp($body, before_bbcode($body)) != 0) {
                $content_flags |= 4;
            }
            if (isset($nsfw)) {
                $content_flags |= $content_nsfw;
            }
            $ibody = '\'' . mysql_escape_string($new_body) . '\'';
        }
    } while(false);

    if ( strlen($err) == 0) {
        if (strlen($err) == 0 && $ban == false && !$preview) {
            $log .= "err empty and ban=false\n";
            if ( strlen($ticket) > 0 ) {
                $query = 'INSERT into confa_tickets(ticket) values(\'' . $ticket . '\')';
                $result = mysql_query($query);
                if (!$result) {
                    mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                    die('This is duplicated post (ticket ' . $ticket . ')');
                }
            }
            if ( isset($msg_id) && $msg_id > 0 ) {
                $query = 'SELECT p.status, p.author, p.created, p.thread_id, p.level, p.closed as post_closed, p.id, t.closed as thread_closed, ( select max(page) from confa_threads) - t.page + 1 as page from confa_posts p, confa_threads t where t.id=p.thread_id and p.id=' . $msg_id;
                $result = mysql_query($query);
                if (!$result) {
                  mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                  die('Query failed');
                }
                $closed = false;

                if (mysql_num_rows($result) != 0) {
                    $row = mysql_fetch_assoc($result);
                    $thread_id = $row['thread_id'];
                    if ( (!is_null($row['post_closed']) && $row['post_closed'] > 0 ) ||
                      (!is_null($row['thread_closed']) && $row['thread_closed'] > 0 )) {
                        $closed = true;
                    }
                    if ( $closed || $row['status'] != 1 || !can_edit_post($row['author'], $row['created'], $user_id, $msg_id)) {
                        die('Modifications to this post are not allowed.');
                    }
                }
                $query = 'UPDATE confa_posts SET subject=\'' . mysql_escape_string($subj) . '\',body=' . $ibody . ',created=now(),ip=' .$ip. ',user_agent=' .$agent. ',content_flags='.$content_flags . ', chars='. $chars . ' WHERE id=' . $msg_id;
                $result = mysql_query($query);
                if (!$result) {
                    mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                    die('Query failed');
                } else {
                    mysql_log( __FILE__, 'query executed ' . mysql_error() . ' QUERY: ' . $query);
                }
            } else if (/*is_null($re) || strlen($re)*/ $re == 0) {

                $query = 'select sum(counter) as cnt, page from confa_threads group by page desc limit 1';
                $result = mysql_query($query);
                if (!$result) {
                    mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                    die('Query failed');
                }
                $row = mysql_fetch_assoc($result);
                $last_page = $row['page'];
                if ($row['cnt']>200) {
                    $last_page++;
                } 

                if (is_null($last_page)) {
                    $last_page = 1;
                }
                $query = 'INSERT INTO confa_threads(author, page) values(' . $user_id . ', ' . $last_page . ')';
                $result = mysql_query($query);
                if (!$result) {
                    mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                    die('Query failed');
                }

                $thread_id = mysql_insert_id();
                $query = 'INSERT INTO confa_posts(status, parent, author, subject, body, created, thread_id, chars, auth, ip, user_agent, content_flags) values(1, 0, ' . $user_id . ',\'' . mysql_escape_string($subj) . '\', ' . $ibody . ', now(), ' .$thread_id . ', ' . $chars . ', 1, ' . $ip . ', ' . $agent . ', ' . $content_flags . ')';
                $result = mysql_query($query);
                if (!$result) {
                    mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                    die('Query failed');
                }
                $msg_id = mysql_insert_id();
                $query = "UPDATE confa_users set status = 1 where id=" . $user_id;
                $result = mysql_query($query);
                if (!$result) {
                    mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                    die('Query failed');
                }
            } else {
                $query = 'SELECT p.thread_id, p.level, p.closed as post_closed, p.id, t.closed as thread_closed, ( select max(page) from confa_threads) - t.page + 1 as page from confa_posts p, confa_threads t where t.id=p.thread_id and p.id=' . $re;
                $result = mysql_query($query);
                if (!$result) {
                    mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                    die('Query failed');
                }
                $closed = false;

                if (mysql_num_rows($result) != 0) {
                    $row = mysql_fetch_assoc($result);
                    $thread_id = $row['thread_id'];
                    $level = $row['level'];
                    $msg_page = $row['page'];
                    if ( (!is_null($row['post_closed']) && $row['post_closed'] > 0 ) ||
                      (!is_null($row['thread_closed']) && $row['thread_closed'] > 0 )) {
                      $closed = true;
                    }
                    if ( $closed ) {
                        die('Replies to this post are disabled.');
                    }
                    if (is_null($msg_page)) {
                        $msg_page = 1;
                    }
                    $level++;
                    $query = 'UPDATE confa_threads set counter=counter+1 where id=' . $thread_id;
                    $result = mysql_query($query);
                    if (!$result) {
                        mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                        die('Query failed');
                    }
                } else {
                    die('cannot find parent for msg=' . $re );
                }
                $query = 'INSERT INTO confa_posts(status, parent, level, author, subject, body, created, thread_id, chars, auth, ip, user_agent, content_flags) values( 1, ' . $re . ', ' . $level . ', ' . $user_id . ',\'' . mysql_escape_string($subj) . '\', ' . $ibody . ', now(), ' . $thread_id . ', ' . $chars . ', 1, ' . $ip . ', ' . $agent . ', ' . $content_flags . ')'; 
                $result = mysql_query($query);
                if (!$result) {
                    mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                    die('Query failed');
                }
                $msg_id = mysql_insert_id();
                $query = "UPDATE confa_users set status = 1 where id=" . $user_id;
                $result = mysql_query($query);
                if (!$result) {
                    mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                    die('Query failed');
                }
            }
            $result = mysql_query('SELECT username from confa_users where id=' . $user_id) or die('Cannot get username');
            $row = mysql_fetch_row($result);
            $username = $row[0];      
            $success = true;
            $confirm = $root_dir . $page_confirm . '?id=' . $msg_id . '&subj=' . urlencode(htmlentities($subj, HTML_ENTITIES,'UTF-8')) . '&page=' . $msg_page . '&author_name=' . urlencode(htmlentities( $username, HTML_ENTITIES,'UTF-8'));
            header("Location: $confirm",TRUE,302);
        } else {
            if (!is_null($preview)) {
                $author = $user;
                $subject = $subj;
                $created = $time = strftime('%Y-%m-%d %H:%M:%S');
                $translit_done = false;
                $msgbody = translit($new_body, $translit_done);
                if (!is_null($msgbody) && strlen($msgbody) > 0 && !is_null($prefix) && strlen($prefix) > 0){
                    $msgbody = $prefix . ' ' . str_replace("\n", "\n" . $prefix . ' ', $msgbody);
                }
                $msgbody = htmlentities( $msgbody, HTML_ENTITIES,'UTF-8');
                $msgbody = before_bbcode($msgbody);
                $msgbody = do_bbcode ( $msgbody );
                $msgbody = nl2br($msgbody);
                $msgbody = after_bbcode($msgbody);
                
                $trans_body = $msgbody; 
                if ($translit_done === true) {
                    $trans_body .= '<BR><BR>[Message was transliterated]';
                }
require_once('msg_form_inc.php');
            }
        }
  }

require('new_inc.php');
require_once('tail_inc.php');
?>


