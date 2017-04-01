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
      die('multiquery failed(1)');
    }

    if (!($resulti = mysqli_query($linki, 'select @max_id as max_id, @last_id as last_id'))) {
      mysql_log(__FILE__ . ':' . __LINE__, 'Multiquery failed: ' . mysqli_error($linki));
      die('multiquery failed(2)');

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
    //die($limit_id . '-' . $max_id );
    if (is_null($how_many) || $how_many == 0) {
       $how_many = 100;
    } else {
       $limit_id = $max_id - $how_many;
    }
    if (is_null($max_id) || strlen($max_id) == 0)
      $max_id = 0;

    if ($show_hidden == 2 || $show_hidden == 1) {
    $query = 'SELECT u.username, u.moder, u.ban_ends, p.auth, p.closed as post_closed, p.views, p.likes, p.dislikes, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as created, p.subject, p.author as author, p.status, p.id as id, p.chars, p.content_flags from confa_posts p, confa_users u  where p.author=u.id and p.id > ' . $limit_id . ' and p.id <= ' . $max_id . ' and p.status != 2 order by id desc limit 500';
    } else if ($show_hidden == 0) {
    $query = 'SELECT u.username, u.moder, u.ban_ends, p.auth, p.closed as post_closed, p.views, p.likes, p.dislikes, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as created, p.subject, p.author, p.status, p.id as id, p.chars, p.content_flags from confa_posts p, confa_users u  where p.author=u.id and p.id > ' . $limit_id . ' and p.id <= ' . $max_id . ' and p.status != 2 and u.id not in (select ignored from confa_ignor where ignored_by=' . $test_user_id . ') order by id desc limit 500';

}
    if ($show_hidden == 2 || $show_hidden == 1) {
    $oldquery = 'SELECT u.username, u.moder, u.ban_ends, p.auth, p.closed as post_closed, p.views, p.likes, p.dislikes, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as created, p.subject, p.author as author, p.status, p.id as id, p.chars, p.content_flags from confa_posts p, confa_users u  where p.author=u.id and p.id < ' . $limit_id . ' and p.status != 2 order by id desc limit 500';
    } else if ($show_hidden == 0) {
    $oldquery = 'SELECT u.username, u.moder, u.ban_ends, p.auth, p.closed as post_closed, p.views, p.likes, p.dislikes, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as created, p.subject, p.author, p.status, p.id as id, p.chars, p.content_flags from confa_posts p, confa_users u  where p.author=u.id and p.id < ' . $limit_id . ' and p.status != 2 and u.id not in (select ignored from confa_ignor where ignored_by=' . $test_user_id . ') order by id desc limit 500';

}

    $result = mysql_query($query);
    if (!$result) {
        mysql_log(__FILE__ . ':' . __LINE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query . '<--END_OF_QUERY, last_id="' . $last_id . '"');
        die('Query failed ' );
    }

    $oldresult = mysql_query($oldquery);
    if (!$oldresult) {
        mysql_log(__FILE__ . ':' . __LINE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query . '<--END_OF_QUERY, last_id="' . $last_id . '"');
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
        mysql_log(__FILE__ . ':' . __LINE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query . 'test_user_id="' . $test_user_id . '"');
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
            setcookie('last_id2', $id, 1800000000, $root_dir, $host, false, true);
            $max_id = $id;
        }
        $icons = '';
        if ($row['content_flags'] & 0x02) {
          $icons = ' <img border=0 src="' . $root_dir . $image_img . '"/> ';
        }
        if ($row['content_flags'] & 0x04) {
          $icons .= ' <img border=0 src="' . $root_dir . $youtube_img . '"/> ';
        }
        $nsfw = '';
        global $content_nsfw;
        if ($row['content_flags'] & $content_nsfw) {
          $nsfw .= ' <span class="nsfw">NSFW</span>';
        }                  
        $line = "";
        if ($show_hidden == 1 && in_array($row['author'], $ignored)) {
          $line= "<li><div style=\"visibility:visible\" id=\"hidden_msg_" . $id . "\"><font color=\"lightgrey\">Hidden message by " . $post_author . " <A href=\"#\" onclick=\"show_hidden_msg(" . $id . ");return false;\"><font color=\"lightgrey\"><b>show</b></font></A></font></div><div style=\"visibility:hidden; height:0;\" id=\"shown_msg_" . $id . "\">";
          $line .= "<A href=\"#\" onclick=\"hide_shown_msg(" .$id ."); return false;\" ><font color=\"lightgrey\"><b>hide</b></font></A> &nbsp;";
        } else {
          $line="<li>";
        }
          $line .=  $icons . '<a target="bottom" name="' . $id . '" href="' . $root_dir . $page_msg . '?id=' . $id . '"> ' . print_subject($subj) . '</a> '.$nsfw.' <b>' . $enc_user . '</b>' . ' ' . '[' . $row['views'] . ' views] ' . $row['created'] . ' <b>' . $row['chars'] . '</b> bytes';
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


    while ($row = mysql_fetch_assoc($oldresult)) {
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
            setcookie('last_id2', $id, 1800000000, $root_dir, $host, false, true);
            $max_id = $id;
        }
        $icons = '';
        if ($row['content_flags'] & 0x02) {
          $icons = ' <img border=0 src="' . $root_dir . $image_img . '"/> ';
        }
        if ($row['content_flags'] & 0x04) {
          $icons .= ' <img border=0 src="' . $root_dir . $youtube_img . '"/> ';
        }
        $nsfw = '';
        global $content_nsfw;
        if ($row['content_flags'] & $content_nsfw) {
          $nsfw .= ' <span class="nsfw">NSFW</span>';
        }                  
        $line = "";
        if ($show_hidden == 1 && in_array($row['author'], $ignored)) {
          $line= "<li><div style=\"visibility:visible\" id=\"hidden_msg_" . $id . "\"><font color=\"lightgrey\">Hidden message by " . $post_author . " <A href=\"#\" onclick=\"show_hidden_msg(" . $id . ");return false;\"><font color=\"lightgrey\"><b>show</b></font></A></font></div><div style=\"visibility:hidden; height:0;\" id=\"shown_msg_" . $id . "\">";
          $line .= "<A href=\"#\" onclick=\"hide_shown_msg(" .$id ."); return false;\" ><font color=\"lightgrey\"><b>hide</b></font></A> &nbsp;";
        } else {
          $line="<li>";
        }
          $line .=  $icons . '<a target="bottom" name="' . $id . '" href="' . $root_dir . $page_msg . '?id=' . $id . '"> ' . print_subject($subj) . '</a> '.$nsfw.' <b>' . $enc_user . '</b>' . ' ' . '[' . $row['views'] . ' views] ' . $row['created'] . ' <b>' . $row['chars'] . '</b> bytes';
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
        $oldout .= $line;
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

var monthNames = [
  "January", "February", "March",
  "April", "May", "June", "July",
  "August", "September", "October",
  "November", "December"
];

// dynamic loading of titles
function onNewMessageCount(count, elapsed_time) {
  console.log('onNewMessageCount ' + count);
  
  // update counter
  var cnt = document.getElementById("msg_count");
  if (cnt != null) {
    cnt.innerHTML = count;
  }
  
  var date = new Date();
  
  // add new titles, if necessary
  var list = document.getElementById("msg_list");
  if (list != null && count > 0) {
      var url1 = "./api/messages?mode=bydate&format=html";
      console.log("retrieving new message titles "+url1);
                
      $.ajax({
             type: "GET",
             url: url1,
             success: function(obj1) {
                console.log("bydate object=" + obj1);
                count = obj1.count;
                console.log("bydate returned " + count + " messages");
                
                // update list of messages 
                var new_content = '';
                
                for (var i = 0; i < count; i++) {
                  if (list.innerHTML.indexOf('name="' + obj1.messages[i].id + '"') < 0) {
                    new_content += '<li>' + obj1.messages[i].html + '</li>';
                  }
                }                
                
                list.innerHTML = new_content + list.innerHTML;                                        
             }
           });
  }
      
  // update timestamp
  var qts = document.getElementById("query_ts");
  if (qts != null) {
      var day = date.getDate();
      if (day < 10) day = '0' + day; 
      var monthIndex = date.getMonth();
      var year = date.getFullYear();
      var hours = date.getHours();
      var minutes = date.getMinutes();
      var seconds = date.getSeconds();
      
      hours = hours < 10 ? '0' + hours : hours;
      minutes = minutes < 10 ? '0'+minutes : minutes;
      seconds = seconds < 10 ? '0'+seconds : seconds;
      
      var strTime = hours + ':' + minutes + ':' + seconds;
      
      qts.innerHTML = '(in ' + (elapsed_time / 1000) +  ' seconds) <b>' + year + ' ' + monthNames[monthIndex] + ' ' + day + ' ' + strTime + '</b>';
  }                      
}

</script>
<base target="bottom">
<?php  require_once('custom_colors_inc.php'); ?>
</head>
<body id="html_body">
<?php
//require('menu_inc.php');
$end_timestamp = microtime(true);
$duration = $end_timestamp - $start_timestamp;

$newcount = $max_id - $last_id;
if($newcount <0)$newcount = 0;

?>
<div id="content" class="content">
<h3 style="margin: auto">New Messages:</h3><br>
<b id="msg_count"><?php print($newcount); ?></b> new message(s) since you checked last time<br>Queried: <span id="query_ts"><?php printf(' (in ' . round($duration, 5) . ' seconds) <b>'); print(local_time(time(), 'Y F d H:i:s')); ?></b></span><br>
    <div id="newMessages">
	<ol id="msg_list">
	    <?php print($out); ?>
	</ol>
    </div>
</div>
<hr>
<div class="content" id="old_messages">
<h3 style="margin: auto">Older Messages:</h3>
    <div id="oldMessages">
	<ol id="old_msg_list">
	    <?php print($oldout); ?>
	</ol>
    </div>
</body>
</html>

<?php require('tail_inc.php'); ?>
