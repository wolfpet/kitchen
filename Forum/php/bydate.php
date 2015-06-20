<?php
/*$Id: bydate.php 842 2012-11-23 21:20:42Z dmitriy $*/
$duration = 0;

require_once('head_inc.php');
$start_timestamp = microtime(true);

// 2 means show in standard mode - for users which are not logged in
$show_hidden = 2;
  $linki = mysqli_connect($dbhost, $dbuser, $dbpassword);
  if (!$linki) {
    mysql_log(__FILE__, 'Could not connect: ' . mysqli_error());
    die('Could not connect to database');
  }

  if (!mysqli_select_db($linki, $dbname)) {
    mysql_log(__FILE__ . ':' . __LINE__, 'Could not select database ' .  $dbname . ' '. mysqli_error($linki));
    die('Could not select database');
  }

    if (!is_null($user_id)) {
      $query = "SELECT show_hidden from confa_users where id=" . $user_id;
      if (!($resulti = mysqli_query($linki, $query))) {
        mysql_log(__FILE__ . ':' . __LINE__, 'Multiquery failed: ' . mysqli_error($linki));
        die('multiquery failed');
      }
      $row = mysqli_fetch_assoc($resulti);
      $show_hidden = $row['show_hidden'];
    }

    $test_user_id = $user_id;
    if (is_null($test_user_id)) {
      $test_user_id = 0;
    }
    if (!mysqli_query($linki, 'call get_last_ids(' . $test_user_id . ', @max_id, @last_id);')) {
      mysql_log(__FILE__ . ':' . __LINE__, 'Multiquery failed: ' . mysqli_error($linki));
      die('multiquery failed');
    }

    if (!($resulti = mysqli_query($linki, 'select @max_id as max_id, @last_id as last_id'))) {
      mysql_log(__FILE__ . ':' . __LINE__, 'Multiquery failed: ' . mysqli_error($linki));
      die('multiquery failed');

    }

   $row = mysqli_fetch_assoc($resulti);
    //die( ' done: max_id=' . $row['@max_id'] . ' last_id = ' . $row['@last_id'] );
    $cur_page = $page_bydate;

    $max_id = $row['max_id'];
    $last_id = $row['last_id'];

    if (is_null($last_id) || strlen($last_id) == 0) {
        if (isset($_SESSION['last_bydate_id'])) {
            $last_id = $_SESSION['last_bydate_id'];
        } else {
            $last_id = 0;
        }
    }
    $limit_id = $last_id;
    if (is_null($how_many) || $how_many == 0) {
       $how_many = 100;
    } else {
       $limit_id = $max_id - $how_many;
    }
    if ($show_hidden == 2 || $show_hidden == 1) {
    $query = 'SELECT u.username, u.moder, u.ban_ends, p.auth, p.closed as post_closed, p.views, p.likes, p.dislikes, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as created, p.subject, p.author as author, p.status, p.id as id, p.chars, p.content_flags from confa_posts p, confa_users u  where p.author=u.id and p.id > ' . $limit_id . ' and p.id <= ' . $max_id . ' and p.status != 2 order by id desc limit 100';
    } else if ($show_hidden == 0) {
    $query = 'SELECT u.username, u.moder, u.ban_ends, p.auth, p.closed as post_closed, p.views, p.likes, p.dislikes, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as created, p.subject, p.author, p.status, p.id as id, p.chars, p.content_flags from confa_posts p, confa_users u  where p.author=u.id and p.id > ' . $limit_id . ' and p.id <= ' . $max_id . ' and p.status != 2 and u.id not in (select ignored from confa_ignor where ignored_by=' . $test_user_id . ') order by id desc limit 100';
}

    $result = mysql_query($query);
    if (!$result) {
        mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query . 'last_id="' . $last_id . '"');
        die('Query failed ' );
    }

    $_SESSION['last_bydate_id'] = $max_id;
    $num = 1;

    $out = '';
    $ignored = array();
    if ($show_hidden == 1) {
      $query = "SELECT ignored from confa_ignor where ignored_by=" . $test_user_id;
      $result_ignored = mysql_query($query);
      if (!$result_ignored) {
        mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query . 'test_user_id="' . $test_user_id . '"');
        die('Query failed ' );
      }
      while ($row = mysql_fetch_assoc($result_ignored)) {
        array_push($ignored, $row['ignored']);
      }
    }
    while ($row = mysql_fetch_assoc($result)) {
        $id = $row['id'];
        $ban_ends = $row['ban_ends'];
        $banned = false;
        if ( !is_null( $ban_ends ) && strcmp( $ban_ends, '0000-00-00 00:00:00' ) ) {
            $banned = true;
        }
        $auth_moder = $row['moder'];

        $subj = $row['subject'];
        $subj = encode_subject( $subj );

        $enc_user = htmlentities($row['username'], HTML_ENTITIES,'UTF-8');
        $post_author = $enc_user;
        if ( $banned === true ) {
            $enc_user = '<font color="grey">' . $enc_user . '</font>';

        }
        $enc_user = '<a class="user_link" href="' . $root_dir . $page_byuser . '?author_id=' . $row['author'] . '" target="contents">' . $enc_user . '</a>';
        if ($num == 1) {
            setcookie('last_id2', $id, 1800000000, $root_dir, $host);
            $max_id = $id;
        }
        $icons = '';
        if ($row['content_flags'] & 0x02) {
          $icons = ' <img border=0 src="' . $root_dir . $image_img . '"/> ';
        }
        if ($row['content_flags'] & 0x04) {
          $icons .= ' <img border=0 src="' . $root_dir . $youtube_img . '"/> ';
        }
        $line = "";
        if ($show_hidden == 1 && in_array($row['author'], $ignored)) {
          $line= "<li><div style=\"visibility:visible\" id=\"hidden_msg_" . $id . "\"><font color=\"lightgrey\">Hidden message by " . $post_author . " <A href=\"#\" onclick=\"show_hidden_msg(" . $id . ");return false;\"><font color=\"lightgrey\"><b>show</b></font></A></font></div><div style=\"visibility:hidden; height:0;\" id=\"shown_msg_" . $id . "\">";
          $line .= "<A href=\"#\" onclick=\"hide_shown_msg(" .$id ."); return false;\" ><font color=\"lightgrey\"><b>hide</b></font></A> &nbsp;";
        } else {
          $line="<li>";
        }
          $line .=  $icons . '<a target="bottom" name="' . $id . '" href="' . $root_dir . $page_msg . '?id=' . $id . '"> ' . print_subject($subj) . '</a>  <b>' . $enc_user . '</b>' . ' ' . '[' . $row['views'] . ' views] ' . $row['created'] . ' <b>' . $row['chars'] . '</b> bytes';
          if (!is_null($row['likes'])) {
            $likes = $row['likes'];
            if ($likes > 0) {
              $line .= ' <font color="green"><b>+' . $likes . '</b></font>';
            }
          }
          if (!is_null($row['dislikes'])) {
            $dislikes = $row['dislikes'];
            if ($dislikes > 0) {
              $line .= ' <font color="red"><b>-' . $dislikes . '</b></font>';
            }
          }
          if ($show_hidden == 1 && in_array($row['author'], $ignored)) {
             $line .= "</div>";
          }
          $line .= "</li>";
        //}
        $out .= $line;
        $num++;
    }

require_once('html_head_inc.php');

?>
<script>
function show_hidden_msg(msg_id) {
  var to_hide = document.getElementById("hidden_msg_" + msg_id);
  to_hide.style.height=0;
  to_hide.style.visibility = "hidden";
  var to_show = document.getElementById("shown_msg_" + msg_id);
  to_show.style.height="auto";
  to_show.style.visibility = "visible";
}

function hide_shown_msg(msg_id) {
  var to_hide = document.getElementById("shown_msg_" + msg_id);
  to_hide.style.height=0;
  to_hide.style.visibility = "hidden";
  var to_show = document.getElementById("hidden_msg_" + msg_id);
  to_show.style.height="auto";
  to_show.style.visibility = "visible";

}

</script>
<base target="bottom">
</head>
<body>
<!--
<table width="100%"><tr><td width="40%"><H4></H4></td>
<td width="60%" align="right"> 
<a href="http://info.flagcounter.com/6tbt"><img src="http://s01.flagcounter.com/count/6tbt/bg_FFFFFF/txt_000000/border_CCCCCC/columns_8/maxflags_16/viewers_3/labels_0/pageviews_0/flags_0/" alt="Flag Counter" border="0"></a>
</td></tr></table>
-->
<!--<table width="95%"><tr>
<td>-->
<!--</td>

</tr></table>-->
<?php

require('menu_inc.php');
$end_timestamp = microtime(true);
    $duration = $end_timestamp - $start_timestamp;

?>


<br><b><?php print($max_id - $last_id); ?></b> new message(s) since you came here last time
&nbsp;&nbsp;&nbsp;&nbsp;Queried: <?php printf(' (in ' . round($duration, 5) . ' seconds) <b>');  print(date('Y F d H:i:s', time())); ?></b><br>
<ol>
<?php print($out); ?>
</ol>
<form target="contents" method=POST action="<?php print($root_dir . $page_bydate); ?>">
<?php 
    if (strlen($err) > 0) {
        print('<br><font color="red"><b>' . $err . '</b></font></br>');
    }
    print("<b>Want to see more? Say how many:</b> ");
    print('<input type="text" size="5" id="how_many" name="how_many" value="' . $how_many . '">');
?>
<!--
<b>Want to see more? Say how many:</b>
<input type="text" size="5" id="how_many" name="how_many" value="<? print($how_many); ?>">
-->
<input type="submit" value="Get them!">
</body>
</html>
<?php

require('tail_inc.php');

?>

