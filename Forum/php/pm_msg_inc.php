<?php
/*$Id: pm_msg_inc.php 814 2012-10-16 13:28:15Z dmitriy $*/

require_once('head_inc.php');
require_once('func.php');

    $moder = NULL;
    $proceeded = false;
    $in_response ='';

    // Performing SQL query
    $query = 'SELECT s.username, p.subject, p.id as msg_id, p.sender, p.receiver,  CONVERT_TZ(p.created, \'' . $server_tz . 
      '\', \''.$prop_tz.':00\') as created, p.body, s.id as id, p.status from confa_users s, confa_pm p where s.id=p.sender and p.id=' . $msg_id . ' and '. 
      '(p.sender='.$user_id.' and !(p.status & '.$pm_deleted_by_sender.') or p.receiver='.$user_id.' and !(p.status & '.$pm_deleted_by_receiver.'))';
    $result = mysql_query($query);
    if (!$result) {
        mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed');
    }
    if (mysql_num_rows($result) != 0) {
        $row = mysql_fetch_assoc($result);
        $subject = htmlentities(translit($row['subject'], $proceeded), HTML_ENTITIES,'UTF-8');
        $subj = $subject;
   
        $author = htmlentities($row['username'], HTML_ENTITIES,'UTF-8');
        $created = $row['created'];
        $status = $row['status'];
        $translit_done = false;
        $msgbody = translit($row['body'], $translit_done);
        if (!is_null($msgbody) && strlen($msgbody) > 0 && !is_null($prefix) && strlen($prefix) > 0){
            $msgbody = $prefix . ' ' . str_replace("\n", "\n" . $prefix . ' ', $msgbody);
        }
        
        $msgbody = render_for_display($msgbody);

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
        $query = 'UPDATE confa_pm set status = status ';
        if ($row['receiver'] == $user_id) {
          $query .= '& ~'.$pm_new_mail;
          $query .= '|'.$pm_read_by_receiver;
        } else {
          $query .= '| '.$pm_read_by_sender;
        }
        $query.= ' where id=' . $msg_id;
        $result = mysql_query($query);
        if (!$result) {
            mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed');
        }
        update_new_pm_count($user_id);
    } else {
        die('No such message');
    }
require_once('msg_form_inc.php');

?>



