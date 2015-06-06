<?php
/*$Id: pm_del.php 378 2009-11-02 19:36:24Z dmitriy $*/

require_once('head_inc.php');
  
    if (count($pmdel) == 0) {
        $err = 'No messages were selected<BR>';
    } else {
        $query = "UPDATE confa_pm set status = 2, flags = NULL where (receiver=".$user_id." or sender=".$user_id.") and id in (";
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

require_once('pmail.php');
?>

