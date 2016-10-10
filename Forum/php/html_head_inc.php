<?php
/*$Id: html_head_inc.php 942 2013-09-01 12:10:18Z dmitriy $*/

require_once('head_inc.php');

    if (is_null($doc_type)) {
        print('<!DOCTYPE html>');
    } else {
        print($doc_type);
    }
$css = 'disc2.css';
if (!is_null($user_id) && $user_id != null) {
  $query = "SELECT css from confa_users where id = " . $user_id;
  $result = mysql_query($query);
  if ($result) {
    $row = mysql_fetch_assoc($result);
    $css = $row['css'];
  } 
}
?>
<html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, minimum-scale=1, initial-scale=1, user-scalable=no">
<link rel="stylesheet" type="text/css" href="<?=autoversion('css/'.$css)?>">
<link rel="stylesheet" type="text/css" href="<?=autoversion('css/common.css')?>">
<?php if (!isset($menu_style) || $menu_style == 0) { ?>
<link rel="stylesheet" type="text/css" href="<?=autoversion('css/ribbon.css')?>">
<?php } ?>
<script src="js/jquery-1.10.2.min.js"></script>