<?php
/*$Id: mymessages.php 829 2012-11-04 20:36:54Z dmitriy $*/

require_once('head_inc.php');

    $cur_page = $page_my_bookmarks;
    
    if ( isset($_POST) && is_array($_POST) && count($_POST) > 0 ) {
      if (!isset($pmdel) || count($pmdel) == 0) {
        $err = 'No messages were selected<BR>';
      } else {
        $query = 'DELETE FROM confa_bookmarks WHERE user='.$user_id.' and post in(';

        $add = '';
        for ($i = 0; $i < count($pmdel); $i++) {
            if (strlen($add) > 0) {
                $add .= ",";
            }
            $add .= $pmdel[$i] . " ";
        }        
        $query .= $add;          
        $query .= ')';
        //print('executing query: '.$query.'<br/>');
        $result = mysql_query($query);
        if (!$result) {
            mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed');
        }          
      }      
    }
    
    $how_many = 50;
    $max_id = 1;
    $last_id = 0;

    $query = 'SELECT count(*) from confa_bookmarks where user=' . $user_id;
    $result = mysql_query($query);
    if (!$result) {
        mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die(' Query failed ' );
    }
    $row = mysql_fetch_row($result);
    $count = $row[0]; 


    if (is_null($page)) {
        $page = 1;
    }
    $last_id = get_page_last_index('confa_bookmarks where user=' . $user_id , $how_many, $page );
    if (is_null($last_id)) {
      $last_id = 0;
    }
    $query = 'SELECT u.username, u.moder, p.auth, p.closed as post_closed, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as created, CONVERT_TZ(p.modified, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as modified, p.subject, p.content_flags, p.views, p.likes, p.dislikes, p.status, p.id as msg_id, p.chars, b.user, b.post  from confa_posts p, confa_users u, confa_bookmarks b where b.user=' . $user_id . ' and b.post=p.id and p.author=u.id and  p.status != 2 and b.id <= ' . $last_id . ' order by msg_id desc limit 50'; 

    $result = mysql_query($query);
    if (!$result) {
        mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed ' );
    }

    $num = 1;  

    $out = '';
    if (mysql_num_rows($result) == 0) {
        $max_id = $last_id;
    }
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $msg_id = $row['msg_id'];
        $moder2 = $row['moder'];
        $length = $row['chars'];

        $subj = $row['subject'];
        $subj = htmlentities($subj, HTML_ENTITIES,'UTF-8');
        $enc_user = htmlentities($row['username'], HTML_ENTITIES,'UTF-8');
        $icons = '';
        if ($row['content_flags'] & 0x02) {
          $icons = ' <img border=0 src="' . $root_dir . $image_img . '"/> ';
        }
        if ($row['content_flags'] & 0x04) {
          $icons .= ' <img border=0 src="' . $root_dir . $youtube_img . '"/> ';
        }
        $nsfw = '';
        if ($row['content_flags'] & $content_nsfw) {
          $nsfw .= ' <span class="nsfw">NSFW</span>';
        }
        if ($row['content_flags'] & $content_boyan) {
          $icons .= ' <img border=0 src="' . $root_dir . $boyan_img . '"/> ';
        }
        $suffix = '';
        if ($row['modified'] != null) {
          $date = $row['modified'] . '<span class="edited">*</span>';   
        } else {
          $date = $row['created'];
        }
        if ($length == 0) {
          $suffix .= ' <span class="empty">(-)</span>';
        }

        $line = '<li><INPUT TYPE=CHECKBOX NAME="pmdel[]" value="' . $msg_id . '"/>';
        $line .= ' <a target="bottom" name="' . $msg_id . '" href="' . $root_dir . $page_msg . '?id=' . $msg_id . '">' . $icons . $subj . '</a> '.$nsfw.$suffix.' <b>' . $enc_user . '</b>' . ' ' . '[' . $row['views'] . ' views] '  . $date . ' <b>' . $length . '</b> bytes';

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
        $line .= "</li>";

        $out .= $line;
        $num++;
    }

require_once('html_head_inc.php');

?>
<base target="bottom">
</head>
<body id="html_body">
<!--<table width="95%"><tr>
<td>-->
<!--<h3><?php print($title);?></h3>-->
<!--</td>

</tr></table>-->
<?php

require('menu_inc.php');

    $max_page = $count/20;
    $max_page++;
    print_pages($max_page, $page, 'contents', $cur_page, '&author_id=' . $user_id);
    if (!is_null($err) && strlen($err) > 0) {
        print('<BR/><br/><font color="red"><b>' . $err . '</b></font>');
    }
?>
<form method=POST target="contents" action="<?php print($root_dir . $page_my_bookmarks); ?>">
  <ol>
  <?php print($out); ?>
  </ol>
  <input type="hidden" name="dummy" value="10">
  <input type="submit" value="Delete selected">
</form>
</body>
</html>
<?php
require('tail_inc.php');
?>

