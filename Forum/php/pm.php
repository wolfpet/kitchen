<?php
/*$Id: pm.php 823 2012-11-02 23:43:52Z dmitriy $*/

/*
if(!extension_loaded('fastbbcode')) {
    dl('fastbbcode.' . PHP_SHLIB_SUFFIX);
}
*/


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
require_once('get_params_inc.php');
require_once('login_inc.php');

    $title = 'Private message';

?>
<!--
<base target="bottom">
</head>
<body>
<table width="95%"><tr>
<td>
<h3><?php print($title);?></h3>
</td>
</tr></table>
-->

<?php
    $to_id = NULL;
    if (is_null($to) || strlen($to) == 0) {
        $err .= "Recipient not defined.";
    } else {
        $query = 'SELECT id from confa_users where username=\'' . $to . '\' and status != 2';
        $result = mysql_query($query);
        if (!$result) {
            mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed');
        }
        $row = mysql_fetch_assoc($result);
        $to_id = $row['id'];
        if (is_null($to_id)) {
            $err .= "No such recipient(" . $to . ")";
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
        $ibody = '\'' . mysql_escape_string($body) . '\'';
    }
    
    if (strlen($err) == 0 && !$preview) {
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
        $subject = "You have new private message on kirdyk.com forum website";
        $message = $subject . ' sent by ' . $user . ' with subject: ' . $subj;
        $from = "kitchen@kirdyk.com";
        $headers = "From: $from";
        mail($email,$subject,$message,$headers);
        }
        $username = $user;      
        $success = true;
        $confirm = $root_dir . $page_pm_confirm . '?id=' . $msg_id . '&subj=' . htmlentities( $subj, HTML_ENTITIES,'UTF-8') . '&author_name=' . htmlentities( $user,  HTML_ENTITIES,'UTF-8') . '&to=' . htmlentities( $to, HTML_ENTITIES,'UTF-8');
        header("Location: $confirm",TRUE,302);
        die('Message has been sent');
    }

require_once('html_head_inc.php');

    $title = 'Private message';
?>
    <base target="bottom">
    </head>
    <body style="background-color: #CCEEEE;">
    <table width="95%"><tr>
    <td>
    <h3><?php print($title);?></h3>
    </td>
    </tr></table>
<?php
    if (!is_null($preview)) {
        $author = $user;
        $subject = $subj;
        $created = $time = strftime('%Y-%m-%d %H:%M:%S');
        $translit_done = false;
        $msgbody = translit($body, $translit_done);
        if (!is_null($msgbody) && strlen($msgbody) > 0 && !is_null($prefix) && strlen($prefix) > 0){
            $msgbody = $prefix . ' ' . str_replace("\n", "\n" . $prefix . ' ', $msgbody);
        }
        $msgbody = htmlentities($msgbody, HTML_ENTITIES,'UTF-8');
        $msgbody = bbcode ( $msgbody );
        $msgbody = nl2br($msgbody);
        $msgbody = after_bbcode($msgbody);
        
        $trans_body = $msgbody;
        if ($translit_done === true) {
            $trans_body .= '<BR><BR>[Message was transliterated]';
        }

require_once('pm_form_inc.php');

    }

require('send_inc.php');
require('tail_inc.php');

?>


