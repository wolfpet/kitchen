<?php
/*$Id: pm.php 823 2012-11-02 23:43:52Z dmitriy $*/

$ip          = NULL;
$agent       = NULL;
$auth_cookie = NULL;
$user        = NULL;
$auth        = NULL;
$ban         = false;
$logout      = NULL;
$err         = '';
$user_id     = NULL;
$body        = NULL;
$re          = NULL;
$pass        = NULL;
$ban_time    = NULL;
$log         = '';
$ibody       = 'NULL';
$success = false;

$msg_page = 1;

require_once('head_inc.php');

$title = 'Private message';

    $to_id = NULL;
    if (is_null($to) || strlen($to) == 0) {
        $err .= "Recipient not defined.";
    } else {
        $query = 'SELECT u.id, i.ignored from confa_users u left join confa_ignor i on u.id=i.ignored_by and i.ignored='.$user_id.' where username=\'' . mysql_real_escape_string($to) . '\' and status != 2';
        $result = mysql_query($query);
        if (!$result) {
            mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed');
        }
        $row = mysql_fetch_assoc($result);
        $to_id = $row['id'];
        if (is_null($to_id)) {
            $err .= "No such recipient (" . $to . ")<BR>";
        } else if (!is_null($row['ignored'])) {
            $err .= "Sorry, " . $to ." prefers not to receive mail from you<BR>";
        }
    }
    if (strlen($subj) > 254){
        $err .= "Subject longer 254 bytes<BR>";
    }
    if (strlen(trim($subj)) == 0) {
        $err .= "No subject</BR>";
    }
    if (strlen($body) > 32765){
        $err .= "Body longer 32765 bytes<BR>";
    }
    $chars = 0;
    if (strlen($body) != 0) {
        $chars = strlen(utf8_decode($body));
        $new_body = render_for_db($body);
        $ibody = '\'' . mysql_escape_string($new_body) . '\'';
    }
    
    if (strlen($err) == 0 && !$preview) {
        if ( strlen($ticket) > 0 ) {
          $query = 'INSERT into confa_tickets(ticket) values(\'' . mysql_real_escape_string($ticket) . '\')';
          $result = mysql_query($query);
          if (!$result) {
              mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
              die('This is duplicated post (ticket ' . $ticket . ')');
          }
        }
        $query = 'SELECT id from confa_users where username=\'' . $user . '\' and status != 2';
        $result = mysql_query($query);
        if (!$result) {
            mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed');
        }
        $row = mysql_fetch_assoc($result);
        $user_id = $row['id'];

        $log .= "err empty and ban=false\n";
        $query = 'INSERT INTO confa_pm(sender, receiver, subject, body, chars) values(' . $user_id . ', ' . $to_id . ', \'' . mysql_escape_string($subj) . '\', ' . $ibody . ', ' . $chars . ')';
        $result = mysql_query($query);
        if (!$result) {
            mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed');
        }

        $msg_id = mysql_insert_id();
        update_new_pm_count($to_id);

        $query = 'SELECT email from confa_users where id=' . $to_id;
        $result = mysql_query($query);
        if (!$result) {
            mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed');
        }
        $row = mysql_fetch_assoc($result);
        $email=$row['email'];
        if (!is_null($email) && strlen($email) > 0) {
          #$to = $email;
          $subject = "You have new private message on $host forum website";
          $message = "";
          $message .= '<html><body><style type="text/css">';
          $message .= file_get_contents('css/disc2.css');          
          $message .= '</style><h3 id="subject">'.$subj.'</h3>';
          $message .= 'Author: <b>'.$user.'</b> ';
          $message .= '<hr><div id="msgbody">';
          $message .= render_for_display($body);
          $message .= '</div><hr/>';
          $message .= '<p>Visit <a href="http://'.$host.'/'.$page_goto.'?pm_id='.$msg_id.'">'.$host.'</a> to reply</p>';
          $message .= '</body></html>';
          $headers = "From: $from_email\r\n";
          $headers .= "MIME-Version: 1.0\r\n";
          $headers .= "Content-Type: text/html; charset=UTF-8\r\n"; // ISO-8859-1
          xmail($email,$subject,$message,$headers);
        }
        $username = $user;      
        $success = true;
        $confirm = $root_dir . $page_pm_confirm . '?id=' . $msg_id . '&subj=' . urlencode($subj) . '&author_name=' . urlencode( $user) . '&to=' . urlencode($to);
        header("Location: $confirm", TRUE, 302);
        die('Message has been sent');
    }

require_once('html_head_inc.php');

?>
    <base target="bottom">
    </head>
    <body style="background-color: #CCEEEE;">
    <h3><?php print($title);?></h3>
<?php
    if (!is_null($preview)) {
        $author = $user;
        $subject = $subj;
        $created = $time = strftime('%Y-%m-%d %H:%M:%S');
        $translit_done = false;
        $new_body = render_for_db($body);
        $msgbody = translit($new_body, $translit_done);
        if (!is_null($msgbody) && strlen($msgbody) > 0 && !is_null($prefix) && strlen($prefix) > 0){
            $msgbody = $prefix . ' ' . str_replace("\n", "\n" . $prefix . ' ', $msgbody);
        }        
        $msgbody = render_for_display($msgbody);
        $trans_body = $msgbody;
        if ($translit_done === true) {
            $trans_body .= '<BR><BR>[Message was transliterated]';
        }

require_once('pm_form_inc.php');
    }

require('send_inc.php');
require('tail_inc.php');

?>


