<?php
/*$Id: html_head_inc.php 942 2013-09-01 12:10:18Z dmitriy $*/

require_once('head_inc.php');

    if (is_null($doc_type)) {
        print('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">');
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
<title><?php 
/*    if (is_null($page_title)) {
        print($title);
    } else {
        print($page_title);
    } */?>"Кирдык"</title>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
<META name="description" content="Форум канадских эмигрантов">
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="<?=autoversion('css/'.$css)?>">
<link rel="stylesheet" type="text/css" href="<?=autoversion('css/common.css')?>">
<script src="js/jquery-1.10.2.min.js"></script>
<!--<script>
var $lastdiv = null;
function show_msg($msg, $body)
{
  $id = 'div' + $msg;
  if ($lastdiv != null) {
    $lastdiv.innerHTML = '';
  }
  $lastdiv = document.getElementById($id);
  $lastdiv.innerHTML = $body;
}
</script>
-->