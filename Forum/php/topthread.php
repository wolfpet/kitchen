<?php
/*$Id: topthread.php 988 2014-01-05 01:14:33Z dmitriy $*/

require_once('head_inc.php');

    $cur_page = $page_bydate;
    $title = 'Thread contents';
    $prefix = NULL;

    $thread_id = NULL;
    $page = 1;

    if (isset($_GET) && is_array($_GET)) {
        $thread_id = trim($_GET['thread']);
        $page = trim($_GET['page']);
        if (is_null($thread_id) || strlen($thread_id) == 0 || !ctype_digit($thread_id)) {
            $thread_id = NULL;
        }
        if (/*is_null($page) || strlen($page) == 0 || !ctype_digit($page)*/ $page == 0) {
            $page = 1;
        }
    }

    if (is_null($thread_id)) {
        die('No thread id supplied');
    }


    function print_msgs2($ar, $msgs, $msg_id) {
        $keys = array_keys($ar);
        print("<dl><dd>\n");
        foreach ($keys as $key) {
            print($msgs[$key]);
            print("<BR>\n");
            if (sizeof($ar[$key]) > 0) {
                print_msgs2($ar[$key], $msgs, $msg_id);
            }
        }
        print("</dd></dl>\n");
    }

    $query = 'SELECT u.username, u.moder, p.auth, p.parent, p.closed as post_closed, p.views, p.likes, p.dislikes, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'EST\')  as created, p.subject, p.body, p.status, p.content_flags, LENGTH(IFNULL(p.body,"")) as len, p.thread_id, p.level, p.id as id, p.chars, p.page, t.closed as t_closed  from confa_posts p, confa_users u, confa_threads t ';
    $query .= ' where p.author=u.id and thread_id = ' . $thread_id . ' and t.id = thread_id order by thread_id desc, level, id desc';
    $result = mysql_query($query) or die('Query  failed ');
    $msgs = array();
    $content = array();
    $cur_content = &$content;
    $stack = array();
    $stack[0] = &$content;
    $level = 0;
    $armass = array();
    $glob = array();
    $l = 0;

    while ($row = mysql_fetch_assoc($result)) {
        $length = $row['chars'];
        if (is_null($length)) {
            $length =  $row['len'];
        }
        $armass[$l] = array();
        $msg_moder = $row['moder'];
        $subj = $row['subject'];
        $subj = translit(/*nl2br(*/htmlentities($subj, HTML_ENTITIES,'UTF-8')/*)*/, $proceeded);
        $enc_user = htmlentities($row['username'], HTML_ENTITIES,'UTF-8');

        $img = '';
        $thread_closed = $row['t_closed'];
        if ($row['level'] == 0) {
            if ($t_closed != 0) {
              $img = '<img border=0 src="images/cs.gif" width=16 height=16 alt="*"> ';
            } else {
              $img = '<img border=0 src="images/bs.gif" width=16 height=16 alt="*"> ';
            }
        } else {
            $img = '<img border=0 src="images/dc.gif" width=16 height=16 alt="*"> ';
        }

        if ( $row['status'] == 2 ) {
            $line = '&nbsp;' . $img . '<I><font color="gray"><del>This message has been deleted</del></font></I> '; 
        } else {
        $icons = '';
        if ($row['content_flags'] & 0x02) {
          $icons = ' <img border=0 src="' . $root_dir . $image_img . '"/> ';
        }
        if ($row['content_flags'] & 0x04) {
          $icons .= ' <img border=0 src="' . $root_dir . $youtube_img . '"/> ';
        }

            $line = '&nbsp;<a name="' . $row['id'] . '" target="bottom" href="' . $root_dir . $page_msg . '?id=' . $row['id'] . '">' . $img . $icons . $subj . '  </a>';
        }
        $line .= ' <b>' . $enc_user . '</b>' . ' ' . '[' . $row['views'] . ' views] ' . $row['created'] . ' <b>' . $length . '</b> bytes';
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
        $msgs[$row['id']] = $line;
        if ($row['level'] == 0) {
            $content[$row['id']] = &$armass[$l];
            $glob[$row['id']] = &$armass[$l];
        } else {
            $cur_content = &$glob[$row['parent']];
            $cur_content[$row['id']] = &$armass[$l];
            $glob[$row['id']] = &$armass[$l];
        }

        $l++;
    } 

require('html_head_inc.php');

?>
<base target="bottom">
</head>
<body>
<!--<table width="95%"><tr>
<td>-->
<!--<h3><?php print($title);?></h3>-->
<!--</td>

</tr></table>-->
<?php

require('menu_inc.php');

?>


<?php
   if (is_null($msg_id)) {
     $msg_id = 0;
   }
   print("<p/>");
   print_msgs2($content, $msgs, $msg_id);

require_once('tail_inc.php');

?>


<table cellpadding=1 cellspacing=0 width="90%">
  <tr>
    <td align="left">
      <h4></h4>
    </td>
    <td align="right">
      <h4></h4>
    </td>

  </tr>
</table>
</body>
</html>

