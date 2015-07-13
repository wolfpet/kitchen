<?php
/*$Id: pm_del.php 378 2009-11-02 19:36:24Z dmitriy $*/

require_once('head_inc.php');
  
    if (count($pmdel) == 0) {
        $err = 'No messages were selected<BR>';
    } else {
        $query = "UPDATE confa_pm SET status = status ";

        if (!isset($lastpage) || $lastpage == $page_pmail) {
          $query .= "& ~".$pm_new_mail." | IF(receiver=".$user_id.",".$pm_deleted_by_receiver.",0)";  // so that deleted pmail doesn't counts as 'new'
        } else {
          $query .= "| IF(sender=".$user_id.",".$pm_deleted_by_sender.",0)";
        }
        $query .= ", flags = NULL WHERE (receiver=".$user_id." or sender=".$user_id.") and id in (";
        
        $add = '';
        for ($i = 0; $i < count($pmdel); $i++) {
            if (strlen($add) > 0) {
                $add .= ",";
            }
            $add .= $pmdel[$i] . " ";
        }
        
        $query .= $add;
        $query .= ')';
        $result = mysql_query($query);
        if (!$result) {
            mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed');
        }
        update_new_pm_count($user_id);

    }

if (isset($lastpage)) {
  $nextpage = $lastpage;
} else {
  $nextpage = 'pmail.php';
}

require_once($nextpage);
?>

