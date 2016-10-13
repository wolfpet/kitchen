<?php
/*$Id: login.php 379 2009-11-02 19:40:34Z dmitriy $*/

    $new_pm = 0;
    require_once('head_inc.php');
    $logged_in = login($user, $password);
    header("Location: top.php", TRUE, 302);
?>

