<?php
/*$Id: login.php 379 2009-11-02 19:40:34Z dmitriy $*/

  require_once('login_inc.php');
  $lastpage = basename($lastpage);
  header("Location: $lastpage", TRUE, 302);
  require_once($lastpage);
?>

