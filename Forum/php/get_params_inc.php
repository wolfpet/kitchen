<?php
/*$Id: get_params_inc.php 879 2013-03-04 17:32:16Z dmitriy $*/
    $debug = getenv("DEBUG");

    if (strlen($debug) > 0) {
      $ip = getenv("IP");
      $agent = getenv("agent");
      $auth_cookie = getenv("auth_".$dbname);
      $user = getenv("user_".$dbname);
      $last_id = getenv("last_id2");
      $last_answered_id = getenv("last_answered_id2");
      $action = getenv("action");
      $mode = getenv("mode");
      $user_id = getenv("userid");
      $msg_id = getenv("id");
      $re = getenv("re"); 
      $link = getenv("link");
      $password = getenv("password");
      $password2 = getenv("password2");
      $email = getenv("email");
      $email2 = getenv("email2");
      $logout = getenv("logout");
      $page = getenv("page");
      $to = getenv("to");
      $subj = getenv("subj");
      $author_id = getenv("author_id");
      $moduserid = getenv("moduserid");
      $author_name = getenv("author_name");
      $byip = getenv("byip");
      $custom = getenv("custom");
      $likedby = getenv("likedby");
      $howmanylikes = getenv("howmanylikes");
    }
    $mode = '';

    if ( isset($_SERVER) && is_array($_SERVER) && count($_SERVER) > 0 ) {
        $ip = $_SERVER['REMOTE_ADDR'];
        $agent = $_SERVER['HTTP_USER_AGENT'];
    }

    if ( isset($_COOKIE) && is_array($_COOKIE) && count($_COOKIE) > 0 ) {
        if (array_key_exists('auth_'.$dbname, $_COOKIE)) {
            $auth_cookie = $_COOKIE['auth_'.$dbname];
        }
        if (array_key_exists('user_' .$dbname, $_COOKIE)) {
            $user = $_COOKIE['user_'.$dbname];
        }
        if (array_key_exists('last_id2', $_COOKIE)) {
            $last_id = $_COOKIE['last_id2'];
        }
        if (array_key_exists('last_answered_id2', $_COOKIE)) {
            $last_answered_id = $_COOKIE['last_answered_id2'];
        }
        if (array_key_exists('orientation', $_COOKIE)) {
            $orientation = $_COOKIE['orientation'];
        }
    }

    if ( isset($_GET) && is_array($_GET) && count($_GET) > 0 ) {
        if (array_key_exists('action', $_GET)) {
            $action = trim($_GET['action']);
        }
        if (array_key_exists('mode', $_GET)) {
            $mode = trim($_GET['mode']);
        }
        if (array_key_exists('userid', $_GET)) {
            $user_id = intval(trim($_GET['userid']),10);
        }
        if (array_key_exists('id', $_GET)) {
            $msg_id = intval(trim($_GET['id']), 10);
        }
        if (array_key_exists('re', $_GET)) {
            $re = intval(trim($_GET['re']), 10);
        }
        if (array_key_exists('act_link', $_GET)) {
            $act_link = trim($_GET['act_link']);
        }     
        if (array_key_exists('password', $_GET)) {
            $password = trim($_GET['password']);
        }
        if (array_key_exists('password2', $_GET)) {
            $password2 = trim($_GET['password2']);
        }
        if (array_key_exists('email', $_GET)) {
            $email = trim($_GET['email']);
        }
        if (array_key_exists('email2', $_GET)) {
            $email2 = trim($_GET['email2']);
        }
        if (array_key_exists('logout', $_GET)) {
            $logout = true;
        }
        if (array_key_exists('page', $_GET)) {
            $page = intval(trim($_GET['page']),10);
        }
        if (array_key_exists('to', $_GET)) {
            $to = trim($_GET['to']);
        }
        if (array_key_exists('subj', $_GET)) {
            $subj = trim($_GET['subj']);
        }
        if (array_key_exists('author_id', $_GET)) {
            $author_id = intval(trim($_GET['author_id']),10);
        }
        if (array_key_exists('moduserid', $_GET)) {
            $moduserid = trim($_GET['moduserid']);
        }
        if (array_key_exists('byip', $_GET)) {
            $byip = trim($_GET['byip']);
        }
        if ($page == 0) {
            $page=1;
        }
        if (array_key_exists('author_name', $_GET)) {
            $author_name = trim($_GET['author_name']);
        }
        if (array_key_exists('byip', $_GET)) {
            $byip = trim($_GET['byip']);
        }
        if (array_key_exists('custom', $_GET)) {
            $custom = trim($_GET['custom']);
        }
        if (array_key_exists('pm_id', $_GET)) {
            $pm_id = intval(trim($_GET['pm_id']),10);
        }
        if (array_key_exists('likedby', $_GET)) {
            $likedby = trim($_GET['likedby']);
        }
        if (array_key_exists('howmanylikes', $_GET)) {
            $howmanylikes = trim($_GET['howmanylikes']);
        }
        if (array_key_exists('bantime', $_GET)) {
            $bantime = $_GET['bantime'];
        }
        if (array_key_exists('ver', $_GET)) {
            $version = $_GET['ver'];
        }
    }

    if ( isset($_POST) && is_array($_POST) && count($_POST) > 0 ) {
        if (array_key_exists('user', $_POST)) {
            $user = trim($_POST["user"]);
        }
        if (array_key_exists('pass', $_POST)) { 
            $pass = trim($_POST["pass"]);
        }
        if (array_key_exists('to', $_POST)) {
            $to = trim($_POST['to']);
        }
        if (array_key_exists('password', $_POST)) {
            $password = trim($_POST['password']);
        }
        if (array_key_exists('password2', $_POST)) {
            $password2 = trim($_POST['password2']);
        }   
        if (array_key_exists('email', $_POST)) {
            $email = trim($_POST['email']);
        }
        if (array_key_exists('email2', $_POST)) {
            $email2 = trim($_POST['email2']);
        }
        if (array_key_exists('userlike', $_POST)) {
            $user_like = $_POST["userlike"];
        }
        if (array_key_exists('subj', $_POST)) {
            $subj = $_POST["subj"];
        }
        if (array_key_exists('body', $_POST)) {
            $body = $_POST["body"];
        }
        if (array_key_exists('re', $_POST)) {
            $re = intval($_POST["re"],10);
        }
        if (array_key_exists('preview', $_POST)) {
            $preview = $_POST["preview"];
        }
        if (array_key_exists('how_many', $_POST)) {
            $how_many = intval($_POST["how_many"],10);
        }
        if (array_key_exists('lastpage', $_POST)) {
            $lastpage = $_POST["lastpage"];
        }
        if (array_key_exists('pmdel', $_POST)) {
            $pmdel = $_POST['pmdel'];
        }
        if (array_key_exists('bantime', $_POST)) {
            $bantime = $_POST['bantime'];
        }
        if (array_key_exists('moduserid', $_POST)) {
            $moduserid = intval($_POST['moduserid'],10);
        }

        if (array_key_exists('fromday', $_POST)) {
            $fromday = intval($_POST['fromday'],10);
        }
        if (array_key_exists('frommonth', $_POST)) {
            $frommonth = intval($_POST['frommonth'],10);
        }
        if (array_key_exists('fromyear', $_POST)) {
            $fromyear = intval($_POST['fromyear'],10);
        }
        if (array_key_exists('today', $_POST)) {
            $today = intval($_POST['today'],10);
        }
        if (array_key_exists('tomonth', $_POST)) {
            $tomonth = intval($_POST['tomonth'],10);
        }
        if (array_key_exists('toyear', $_POST)) {
            $toyear = intval($_POST['toyear'],10);
        }
        if (array_key_exists('text', $_POST)) {
            $text = $_POST['text'];
        }
        if (array_key_exists('author', $_POST)) {
            $author = $_POST['author'];
        }
        if (array_key_exists('searchin', $_POST)) {
            $searchin = intval($_POST['searchin'],10);
        }
        if (array_key_exists('ban_reason', $_POST)) {
            $ban_reason = $_POST['ban_reason'];
        }
        if (array_key_exists('author_id', $_POST)) {
            $author_id = intval(trim($_POST['author_id']),10);
        }
        if (array_key_exists('profile_bold', $_POST)) {
            $profile_bold = 1;
        }
        if (array_key_exists('show_smileys', $_POST)) {
            $show_smileys = true;
        }
        if (array_key_exists('reply_to_email', $_POST)) {
            $send_reply_to_email = true;
        }
        if (array_key_exists('custom', $_POST)) {
            $custom = trim($_POST['custom']);
        }
        if (array_key_exists('ticket', $_POST)) {
            $ticket = trim($_POST['ticket']);
        }
        if (array_key_exists('pm_id', $_POST)) {
            $pm_id = intval(trim($_POST['pm_id']),10);
        }
        if (array_key_exists('likedby', $_POST)) {
            $likedby = trim($_POST['likedby']);
        }
        if (array_key_exists('howmanylikes', $_POST)) {
            $howmanylikes = trim($_POST['howmanylikes']);
        }            
        if (array_key_exists('tz', $_POST)) {
            $tz = trim($_POST['tz']);
        }
        if (array_key_exists('nsfw', $_POST)) {
            $nsfw = $_POST["nsfw"];
        }    
        if (array_key_exists('id', $_POST)) {
            $msg_id = intval(trim($_POST['id']), 10);
        }
        if (array_key_exists('mode', $_POST)) {
            $mode = trim($_POST['mode']);
        }
        if (array_key_exists('menu_style', $_POST)) {
            $send_menu_style = trim($_POST['menu_style']);
        }
    }
?>