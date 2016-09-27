<?php
/*$Id: func.php 988 2014-01-05 01:14:33Z dmitriy $*/

/**********************************************************************/
/*                          Common function                           */ 
/**********************************************************************/ 

require_once('translit.php');
require_once('settings.php');
require_once('bbcode.php');

function local_time($time, $format) {
  
  global $prop_tz;
  global $server_tz;
  
  $offset = intval($prop_tz) - intval(explode(":", $server_tz)[0]);
  
  return gmdate($format, $time + intval($prop_tz) * 3600);
}

function autoversion($file) {
 global $root_dir;
 if(strpos($file, '/') !== 0)
    $full_path = $_SERVER['DOCUMENT_ROOT'] . $root_dir . $file;
 else 
    $full_path = $_SERVER['DOCUMENT_ROOT'] . $file;
 
 if (!file_exists($full_path))
    return $file;

  $mtime = filemtime($full_path);
  // return preg_replace('{\\.([^./]+)$}', ".$mtime.\$1", $file);
  return $file . '?' . $mtime;
}

function update_new_pm_count($user_id) {
    global $pm_new_mail;
    global $pm_deleted_by_receiver;
    global $new_pm;
    $query = 'SELECT count(*) from confa_pm where receiver=' . $user_id . ' and status & ' . $pm_new_mail . ' and not status & ' . $pm_deleted_by_receiver;
    $result = mysql_query($query);
    if (!$result) {
        mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed');
    }
    $row = mysql_fetch_row($result);
    $new_pm = $row[0];
    $query = 'UPDATE confa_users set new_pm=' . $new_pm . ' where id=' . $user_id;
    $result = mysql_query($query);
    if (!$result) {
        mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed');
    }
}

function get_regs_count() {
    $query = 'SELECT count(*) from confa_regs';
    $result = mysql_query($query);
    if (!$result) {
        mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed');
    }
    $row = mysql_fetch_row($result);
    return intval($row[0]);
}

function notify_about_new_pm($user_id, $last_login, $target="contents") {
    global $cur_page;
    global $page_pmail;
    
    if (!isset($user_id) || is_null($user_id) || !isset($last_login) || is_null($last_login))
      return;
    else if ($cur_page == $page_pmail) {
      // visiting Inbox counts as checking for pmail
      $query = "update confa_users set last_pm_check_time = CURRENT_TIMESTAMP where id=" . $user_id;
      $result = mysql_query($query);
      if (!$result) {
        mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed: ' . mysql_error() . ' QUERY: ' . $query);
      }      
      return;
    }

    global $pm_new_mail;

    $query = 'SELECT u.username, p.created from confa_pm p, confa_users u where u.id=p.sender and p.receiver=' . $user_id . ' and p.status & ' . $pm_new_mail  . " and p.created > '" . $last_login . "' order by p.created desc";
    $result = mysql_query($query);
    if (!$result) {
      mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
      die('Query failed: ' . mysql_error() . ' QUERY: ' . $query);
    }
    
    if ($row = mysql_fetch_assoc($result)) {       
      // You've got mail! Let's find out how many
      $pm_author = $row['username'];
      $pm_count = 0;
      do {
        $pm_count++;
      } while ($row = mysql_fetch_assoc($result));
      ?><script language="javascript">    
      var windowonload = window.onload;
      
      window.onload = function(e) {
        console.log("openModal starts");
        if (windowonload != null) {
          windowonload(e);        
        }
        location.hash = "#openModal";
        console.log("openModal ended");
      }
      </script><div id="close"/><div id="openModal" class="modalDialog"><div><a href="#close" target="<?=$target?>" title="Close" class="close">X</a>
        <table cellpadding="5"><tr><td><img src="images/ygm.png" style="width:80%;height:auto;"/></td><td width="75%">
          <h3>You've got mail!</h3><?php
          if ($pm_count == 1) {?>
            <p><b><?=$pm_author?></b> has sent you a private message.</p><?php
          } else { ?>
            <p>You have <b style="color: red;"><?=$pm_count?></b> new private messages.</p><?php
          } ?>
          <p>Click <a target="contents" href="<?=$page_pmail?>" onclick="javascript:location.hash='#close'; return true;">here</a> to go to your Inbox.</p>
        </td></tr></table>
      </div>
    </div><?php
    // No need to update this if no PM was found;
    $query = "update confa_users set last_pm_check_time = CURRENT_TIMESTAMP where id=" . $user_id;
    $result = mysql_query($query);
    if (!$result) {
      mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
      die('Query failed: ' . mysql_error() . ' QUERY: ' . $query);
    }      
  }    
}

function get_page_last_index( $where, $page_size, $page) {

    if ( $page == 1 ) {
        $query = 'SELECT max(id) from (select id from ' . $where . ' order by id ) p';
    } else {
        $query = 'SELECT min(id) from (select id from ' . $where . ' order by id desc limit ' . $page_size*($page-1) . ') p';
    }
    $result = mysql_query($query);
    if (!$result) {
        mysql_log(__FILE__, 'get_user_props failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed');
    }
    $row = mysql_fetch_row($result);
    return $row[0];
}

function generatePassword($length=9, $strength=0) {

    $vowels = 'aeuy';
    $consonants = 'bdghjmnpqrstvz';
    if ($strength & 1) {
        $consonants .= 'BDGHJLMNPQRSTVWXZ';
    }
    if ($strength & 2) {
        $vowels .= "AEUY";
    }
    if ($strength & 4) {
        $consonants .= '23456789';
    }
    if ($strength & 8) {
        $consonants .= '@#$%';
    }
 
    $password = '';
    $alt = time() % 2;
    for ($i = 0; $i < $length; $i++) {
        if ($alt == 1) {
            $password .= $consonants[(rand() % strlen($consonants))];
            $alt = 0;
        } else {
            $password .= $vowels[(rand() % strlen($vowels))];
            $alt = 1;
        }
    }
    return $password;
}


function get_thread_starts($min_thread_id, $max_thread_id) {

    global $prop_tz;
    global $server_tz;

    $query = 'SELECT u.username, u.id as user_id, u.moder, u.ban_ends, p.parent, p.closed as post_closed, p.views, p.likes, p.dislikes, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\')  as created, CONVERT_TZ(p.modified, \'' . $server_tz . '\', \'' . $prop_tz . ':00\')  as modified, p.subject,  p.content_flags, t.closed as thread_closed, t.status as thread_status, t.id as thread_id, p.level, p.status, p.id as msg_id, p.chars, t.counter, (SELECT count(*) from confa_bookmarks b where b.post=p.id) as bookmarks, (SELECT count(*) from confa_likes l where l.post=p.id and reaction is not null) as reactions from confa_posts p, confa_users u, confa_threads t ';
    if ( $min_thread_id < 0 ) {
        $min_thread_id = 0;
    }
    $query .= ' where p.author=u.id and thread_id >= ' . $min_thread_id . ' and thread_id <= ' . $max_thread_id . ' and p.thread_id = t.id and t.status != 2 and level = 0 order by thread_id desc';
    $result = mysql_query($query);
    if (!$result) {
        mysql_log(__FILE__, 'get_thread_starts failed ' . mysql_error() . ' QUERY: ' .$query);
        die('Query failed ');
    }
    return $result;
}

// NG: begin

function autoload_threads($last_thread, $limit) {?>
  <script language="JavaScript">
  
    set_max_id(<?=$last_thread?>, "<?=$limit?>");
    
  </script>
  <div id="scroll2top"><a href="#" target="contents" onclick="javascript:scroll2Top2('html_body');"><img border=0 src="images/up.png" alt="Up" title="Back to top" onmouseout="this.style.opacity=0.5;" style="opacity:0.5" onmouseover="this.style.opacity=1;"></a></div>
  <div id="loading" style="color:gray;position:fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;text-align: right;display:none">Loading...&nbsp;</div><?php 
}

function get_show_hidden_and_ignored() {
  
  global $link;
  global $user_id;
  global $show_hidden;
  global $ignored;
  
  $show_hidden = 2;
  $ignored = array();

  if (!is_null($user_id)) {
    $query = "SELECT show_hidden from confa_users where id=" . $user_id;
    if (!($result = mysql_query($query))) {
      mysql_log(__FILE__ . ':' . __LINE__, 'query failed: ' . $query . "|" .  mysql_error($link));
      die('query failed');
    } else {
      $row = mysql_fetch_assoc($result);
      $show_hidden = $row['show_hidden'];
    }
    if ($show_hidden == 1 || $show_hidden == 0) {
      $query = "SELECT ignored from confa_ignor where ignored_by=" . $user_id;
      $result_ignored = mysql_query($query);
      if (!$result_ignored) {
        mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query . 'test_user_id="' . $user_id . '"');
        die('Query failed ' );
      }
      while ($row = mysql_fetch_assoc($result_ignored)) {
        array_push($ignored, $row['ignored']);
      } 
    }
  }
}

function get_threads_ex($limit = 200, $thread_id = null) {

  global $prop_tz;
  global $work_page;
  global $server_tz;

  $query = 'SELECT u.username, u.id as user_id, u.moder, u.ban_ends, p.parent, p.closed as post_closed, p.views, p.likes, p.dislikes, p.level, CONVERT_TZ(p.created, \'' 
    . $server_tz . '\', \'' . $prop_tz . ':00\') as created, CONVERT_TZ(p.modified, \'' . $server_tz . '\', \'' . $prop_tz 
    . ':00\') as modified, p.subject, p.status, p.thread_id, p.id as msg_id, p.chars, p.content_flags, t.page, t.closed as thread_closed, t.status as thread_status, t.counter,'
    . ' (SELECT count(*) from confa_bookmarks b where b.post=p.id) as bookmarks, (SELECT count(*) from confa_likes l where l.post=p.id and reaction is not null) as reactions from confa_posts p, confa_users u, confa_threads t ';
  $query.= 'where p.author=u.id and t.id = p.thread_id and t.status != 2 ';
	
	if (is_null($thread_id)) {
		$query .= 'and t.page<=' . $work_page . ' and t.id > (select max(id) from confa_threads t where t.page =' . $work_page . ') - ' . $limit; 
	} else {
		$query .= 'and p.thread_id < ' . $thread_id . ' and p.thread_id >=' . ($thread_id - $limit);
	}

	$query .= ' order by thread_id desc, level, msg_id desc';
  $result = mysql_query($query);
  
  if (!$result) {
      mysql_log( __FILE__, 'get_threads failed ' . mysql_error() . ' QUERY: ' . $query);
      die('Query failed ');
  }
  
  return $result;
}

function print_threads_ex($result, &$content, &$max_thread_id, $limit = 200, $collapsed=false) {

    global $work_page;

    $msgs = array();
    $content = array();
    $cur_content = &$content;
    $armass = array();
    $glob = array();
    $l = 0;
    
    while ($row = mysql_fetch_assoc($result)) {
        if ($l > $limit && $row['level'] == 0 && $row['page'] != $work_page) // always print everything on the work page to support Synchronize
          break;
      
        $max_thread_id = $row['thread_id'];      
        $msgs[$row['msg_id']] = print_line($row, $collapsed, false, true, false); // $row, $collapsed=false, $add_arrow=false, $add_icon=true, $indent=true

        $armass[$l] = array();

        if ($row['level'] == 0) {
            $content[$row['msg_id']] = &$armass[$l];
        } else {
            $cur_content = &$glob[$row['parent']];
            $cur_content[$row['msg_id']] = &$armass[$l];
        }
        $glob[$row['msg_id']] = &$armass[$l];

        $l++;        
    }
    
    return $msgs;
}

function print_subject($subj) {
  return preg_replace('#\((?:c|C|с|С)\)#', '©', preg_replace('#([^\.])\.$#', '$1', preg_replace('#(\(\-+\))|(\(edited\))#', '', trim(grammar_nazi($subj)))));
}

function print_line($row, $collapsed=false, $add_arrow=false, $add_icon=true, $indent=true) {
  
  global $root_dir;
  global $page_msg;
  global $page_byuser;
  global $page_topthread;
  global $page;
  global $cur_page;
  global $page_collapsed;
  global $page_thread;
  global $prop_bold;
  global $image_img;
  global $boyan_img;
  global $youtube_img;
  global $content_nsfw;
  global $content_boyan;

  global $show_hidden;
  global $ignored;
  
  global $user;

  $b_start = '';
  $b_end = '';
  
  if (!strcmp( $cur_page, $page_collapsed )) {
    $collapsed = true;
  }
  if ( !is_null($prop_bold) && $prop_bold > 0 && !$collapsed ) { 
      $b_start = '<b>';
      $b_end = '</b>';
  }

  if ($ignored != null) {
    if ($show_hidden == 1 && in_array($row['user_id'], $ignored)) {
      return ($indent ? '&nbsp;' : '') . "<font color=\"lightgrey\"/>Hidden msg by " . htmlentities($row['username'], HTML_ENTITIES,'UTF-8') . "</font>";
    }
    if ($show_hidden == 0 && in_array($row['user_id'], $ignored)) {
      return "";
    }
  }
  
  $length = $row['chars'];
  $moder = $row['moder'];
  $banned = false;
  $ban_ends = $row['ban_ends'];
  $subj = $row['subject'];
  $icons = '';
  $style = 'padding:0px 0px 3px 0px;';
  $nsfw = '';
  if ($row['content_flags'] & 0x02) {
    $icons = ' <img border=0 src="' . $root_dir . $image_img . '"/> ';
  }
  if ($row['content_flags'] & 0x04) {
    $icons .= ' <img border=0 src="' . $root_dir . $youtube_img . '"/> ';
  }
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
    $suffix_style = '';
    $suffix .= ' <span class="empty">(-)</span>';
  }
  
  $subj = encode_subject($subj);
  $enc_user = htmlentities($row['username'], HTML_ENTITIES,'UTF-8');
  if ( !is_null( $ban_ends ) && strcmp( $ban_ends, '0000-00-00 00:00:00' ) ) {
      $banned = true;
  }
  if ( $banned === true ) {
      $enc_user = '<font color="grey">' . $enc_user . '</font>';
  }
  $enc_user = '<a class="user_link" href="' . $root_dir . $page_byuser . '?author_id=' . $row['user_id'] . '" target="contents">' . $enc_user . '</a>';  
  if ($row['status'] == 2 ) {
      $line = ($indent ? '&nbsp;' : '') . '<span id="sp_'.$row['msg_id'].'">';
      if ($add_icon) {
        if ($row['level'] == 0) {
            $line .= '<img border=0 src="images/bs.gif" width=16 height=16 alt="*" align="top" style="'.$style.'"> ';
        } else {
            $line .= '<img border=0 src="images/dc.gif" width=16 height=16 alt="*" align="top" style="'.$style.'"> ';
        }
      }
      $line .= '<I><font color="gray"><del>This message has been deleted</del></font></I> ';
  } else {
      $subj = print_subject($subj);
      if ($row['level'] == 0) {
          $icon = "bs.gif";
          if ($row['thread_closed'] != 0) {
            $icon = "cs.gif";
          } else if ($row['counter'] == 0) {
            $icon = "es.gif";
          } else {
            $style .= 'cursor:pointer;';
          }
          $icon = '<img border=0 src="images/' . $icon . '" width=16 height=16 alt="*" onclick="javascript:toggle(this);" align="top" style="'.$style.'"> ';
      } else {
          $icon = '<img border=0 src="images/dc.gif" width=16 height=16 alt="*" align="top" style="'.$style.'"> ';          
      }
      $line = ($indent ? '&nbsp;' : '') . '<span id="sp_'.$row['msg_id'].'">';
      if ($add_icon) {
        $line .= $icon;
      }
      $line .= $icons . '<a id="' . $row['msg_id'] . '" name="' . $row['msg_id'] . '" target="bottom" onclick="selectMsg(\''.$row['msg_id'].'\');" href="' . $root_dir . $page_msg . '?id=' . $row['msg_id'] . '">' . $b_start . $subj . $b_end . '</a>'.$nsfw.$suffix.' ';
  }
  
  $line .= '<b>' . $enc_user . '</b>' .  ' [' . $row['views'] . ' views] ' . $date . ' <b>' . $length . '</b> bytes';
  
  if (!is_null($row['bookmarks'])) {
    $bookmarks = $row['bookmarks'];
    if ($bookmarks > 0) {
      $line .= ' <font color="blue"><b>' . $bookmarks . '</b></font>';
    }
  }
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
  if (array_key_exists('reactions', $row) && !is_null($row['reactions'])) {
    $reactions = $row['reactions'];
    if ($reactions > 0) {
      $line .= ' <font color="orange"><b>+' . $reactions . '</b></font>';
    }
  }

  if ( $collapsed ) {
      $line .= ' <font color="gray">[ <a href="' . $root_dir . $page_topthread . '?thread=' . $row['thread_id'] . '&page=' . $page . '" target="contents">+' . $row['counter'] . '</a> ] </font> </span>   ';
  } else {
      $line .= '&nbsp;</span>';
  }

  $arrow = ''; 

  return $line . $arrow;
}

function get_thread($thread_id) {

  global $prop_tz;
  global $server_tz;

  $query = 'SELECT u.username, u.moder, p.auth, p.parent, p.closed as post_closed, p.views, p.likes, p.dislikes,'.
    ' CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as created,'.
    ' CONVERT_TZ(p.modified, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as modified, p.subject, p.body, p.status, p.content_flags, LENGTH(IFNULL(p.body,"")) as len,'.
    ' p.thread_id, p.level, p.id as id, p.chars, p.page, t.closed as t_closed, (SELECT count(*) from confa_bookmarks b where b.post=p.id) as bookmarks, (SELECT count(*) from confa_likes l where l.post=p.id and reaction is not null) as reactions from confa_posts p, confa_users u, confa_threads t '.
    ' WHERE p.author=u.id and thread_id = ' . $thread_id . ' and t.id = thread_id order by thread_id desc, level, id desc';
  
  $result = mysql_query($query);
    if (!$result) {
        mysql_log( __FILE__, 'get_thread failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed ');
    }
    return $result;
}

function print_thread($result, &$content, $print_function='print_line_in_one_thread') {
  
  $msgs = array();
  $content = array();
  $cur_content = &$content;
  $armass = array();
  $glob = array();
  $l = 0;

  while ($row = mysql_fetch_assoc($result)) {
    $armass[$l] = array();
    
    $line = $print_function($row);
    
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
  
  return $msgs;
}

// Collapsed threads - thread view
function print_line_in_one_thread($row) {
  
  global $root_dir;
  global $image_img;
  global $youtube_img;
  global $page_msg;
  
  global $show_hidden;
  global $ignored;
  
  $msg_moder = $row['moder'];
  //  $subj = translit(/*nl2br(*/htmlentities($row['subject'], HTML_ENTITIES,'UTF-8')/*)*/, $proceeded);
  $subj = print_subject(encode_subject($row['subject']));
  $enc_user = htmlentities($row['username'], HTML_ENTITIES,'UTF-8');

  $length = $row['chars'];
  if (is_null($length)) {
      $length =  $row['len'];
  }

  $img = '';
  $thread_closed = $row['t_closed'];
  if ($row['level'] == 0) {
      if ($thread_closed != 0) {
        $img = '<img border=0 src="images/cs.gif" width=16 height=16 alt="*"> ';
      } else {
        $img = '<img border=0 src="images/bs.gif" width=16 height=16 alt="*"> ';
      }
  } else {
      $img = '<img border=0 src="images/dc.gif" width=16 height=16 alt="*"> ';
  }

  if ( $row['status'] == 2 ) {
      $line = '&nbsp;<span>' . $img . '<I><font color="gray"><del>This message has been deleted</del></font></I> '; 
  } else {
    $icons = '';
    if ($row['content_flags'] & 0x02) {
      $icons = ' <img border=0 src="' . $root_dir . $image_img . '"/> ';
    }
    if ($row['content_flags'] & 0x04) {
      $icons .= ' <img border=0 src="' . $root_dir . $youtube_img . '"/> ';
    }
    $line = '&nbsp;<span id="sp_'.$row['id'].'"><a id="' . $row['id'] . '" name="' . $row['id'] . '" target="bottom" href="' . $root_dir . $page_msg . '?id=' . $row['id'] . '" onclick="selectMsg(\''. $row['id'] .'\')">' . $img . $icons . $subj . '  </a>';
  }
  if ($row['modified'] != null) {
    $date = $row['modified'] . '<span class="edited">*</span>';
  } else {
    $date = $row['created'];
  }
  $suffix = '';
  if ($length == 0) {
    $suffix_style = 'empty';
    $suffix = "(-)";
  }
  if ($suffix != "") {
    $suffix = ' <span class="' . $suffix_style . '">' . $suffix . '</span>';
  }
  $line .= ' <b>' . $enc_user . '</b>' . $suffix . ' ' . '[' . $row['views'] . ' views] ' . $date . ' <b>' . $length . '</b> bytes';
  
  if (!is_null($row['bookmarks'])) {
    $bookmarks = $row['bookmarks'];
    if ($bookmarks > 0) {
      $line .= ' <font color="blue"><b>' . $bookmarks . '</b></font>';
    }
  }
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
  
  return $line . " </span>";
}

// NG: end

function get_threads() {

    global $prop_tz;
    global $work_page;
    global $server_tz;
    
    $query = 'SELECT u.username, u.id as user_id, u.moder, u.ban_ends, p.parent, p.closed as post_closed, p.views, p.likes, p.dislikes, p.level, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\')  as created, p.subject, p.status, p.thread_id, p.id as msg_id, p.chars, p.content_flags, t.page, t.closed as thread_closed, t.status as thread_status, t.counter from confa_posts p, confa_users u, confa_threads t where p.author=u.id and t.id = p.thread_id and t.status != 2 and t.page=' . $work_page . ' order by thread_id desc, level, msg_id desc';

    $result = mysql_query($query);
    if (!$result) {
        mysql_log( __FILE__, 'get_threads failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed ');
    }
    return $result;
}

function get_max_pages_collapsed(&$max_thread_id) {

    $query = 'SELECT count(distinct(thread_id)), max(thread_id) from confa_posts where status != 2';
    $result = mysql_query($query);
    if (!$result) {
        mysql_log(__FILE__, 'Query page count failed: ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed.');
    }
    $row = mysql_fetch_row($result);
    $max_page = $row[0]/50;
    $max_thread_id = $row[1];
    return $max_page;
}

function get_max_pages_users(&$max_user_id, $user_like) {

    $query = 'Select count(*), max(id) from confa_users';
    if (!is_null($user_like)) {
      $query .= ' where username like \'%' . $user_like . '%\'';
    }
    $result = mysql_query($query);
    if (!$result) {
      mysql_log(__FILE__, 'Query users page count failed: ' . mysql_error() . ' QUERY: ' . $query);
      die('Query failed.');
    }
    $row = mysql_fetch_row($result);
    $max_page = $row[0]/50;
    $max_user_id = $row[1];
    return $max_page;
 
}
 
function get_users($min_user_id, $max_user_id, $user_like) {

    global $prop_tz;
    global $server_tz;
    
    if (is_null($user_like)) {
      $query = 'select id as user_id, username,  CONVERT_TZ(created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\')  as created, moder, CONVERT_TZ(ban_ends, \'' . $server_tz . '\', \'' . $prop_tz . ':00\')  as ban_ends from (select id, username, created, moder, ban_ends from confa_users  where status != 2 order by username) us where id >= ' . $min_user_id . ' and id <= ' . $max_user_id . ' order by username';
    } else {
      $query = 'select id as user_id, username,  CONVERT_TZ(created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\')  as created, moder, CONVERT_TZ(ban_ends, \'' . $server_tz . '\', \'' . $prop_tz . ':00\')  as ban_ends from (select id, username, created, moder, ban_ends from confa_users  where status != 2 order by username    ) us where username like \'%' . $user_like . '%\'  order by username';

    }
    $result = mysql_query($query);
    if (!$result) {
        mysql_log( __FILE__, 'get_users failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed ');
    }
    return $result;
}

function print_users($result) {

    global $root_dir;
    global $page_m_user;

    print('<table><tr><th>ID </th><th>Username </th><th>Registered </th><th align="right">Status </th></tr>');
    while ($row = mysql_fetch_assoc($result)) {
        $status = 'Active';
        if (!is_null($row['ban_ends'])) {
            $status = 'Banned till ' . $row['ban_ends'];
        } 
        $line = '<tr><td>' . $row['user_id'] . ' </td><td><a target="bottom" href="' . $root_dir . $page_m_user . '?userid=' . $row['user_id'] . '">' . htmlentities($row['username'], HTML_ENTITIES,'UTF-8') . '</a> </td><td>' . $row['created'] . ' </td><td align="right">' . $status . ' </td></tr>';
        print($line); 
    } 
    print('</table>');
}

function get_max_pages_expanded(){ 

    $query = 'SELECT max(page) as max_page from confa_threads';
    $result = mysql_query($query);
    if (!$result) {
        mysql_log(__FILE__, 'Query count failed: ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed.');
    }
    $row = mysql_fetch_assoc($result);
    $max_page = $row['max_page'];
    if (is_null($max_page)) {
        $max_page = 1;
    }
    return $max_page;
}

function print_pages($max_page, $page, $target, $cur_page, $param = '', $br = false, $prefix = false) {
/**
<div class="pagination">
				4219 тем
					<ul>
		<li class="active"><span>1</span></li>
      <li class="previous"><a href="./viewforum.php?f=8&amp;start=2555" rel="prev" role="button">Пред.</a></li>
			<li><a href="./viewforum.php?f=8&amp;start=35" role="button">2</a></li>
			<li><a href="./viewforum.php?f=8&amp;start=70" role="button">3</a></li>
			<li><a href="./viewforum.php?f=8&amp;start=105" role="button">4</a></li>
			<li><a href="./viewforum.php?f=8&amp;start=140" role="button">5</a></li>
			<li class="ellipsis" role="separator"><span>…</span></li>
			<li><a href="./viewforum.php?f=8&amp;start=4200" role="button">121</a></li>
			<li class="next"><a href="./viewforum.php?f=8&amp;start=35" rel="next" role="button">След.</a></li>
	</ul>
			</div>
*/  
    global $root_dir, $menu_style;
    
    if ($menu_style == 1) {
      return print_pages_obsolete($max_page, $page, $target, $cur_page, $param, true, true);
    }
    
    if ($br) print('<BR>');
    print('<div class="pagination">');
    if ($prefix) {
      print('<B>Pages</B>: ');
    }
    print("<ul>");
    
    if ($page > 1) {
      print('<li class="previous"><a target="' . $target .'" href="'. $root_dir . $cur_page . '?page=' . ($page - 1) . $param . '" rel="prev" role="button">Prev.</a></li>');
    }

    if ($page == 1) {
      print('<li class="active"><span>1</span></li>');
    } else {
       print('<li><a target="' . $target .'" href="'. $root_dir . $cur_page . '?page=1' . $param . '" role="button">1</a></li>');
    }
    
    if ($page > 4) {
        print('<li class="ellipsis" role="separator"><span>…</span></li>');
    }
    
    for ($i = $page - 2; $i <= $page + 2; $i++) {
        if ( $i < 2 || $i >= $max_page ) {
          continue;
        }
        if ($page == $i) {
          print('<li class="active"><span>'.$i.'</span></li>');
        } else {
          print('<li><a target="' . $target .'" href="'. $root_dir . $cur_page . '?page=' .$i. $param . '" role="button">'.$i.'</a></li>');
        }        
    }
    
    if ($max_page - $page > 3) {
        print('<li class="ellipsis" role="separator"><span>…</span></li>');
    }

    if ($max_page > 1) {
      if ($page == $max_page) {
        print('<li class="active"><span>'.$page.'</span></li>');
      } else {
         print('<li><a target="' . $target .'" href="'. $root_dir . $cur_page . '?page=' . $max_page . $param . '" role="button">'.$max_page.'</a></li>');
      }
    }    

    if ($page < $max_page) {
      print('<li class="next"><a target="' . $target .'" href="'. $root_dir . $cur_page . '?page=' . ($page + 1) . $param . '" rel="next" role="button">Next</a></li>');
    }

    print("</ul></div>");
}

function print_pages_obsolete($max_page, $page, $target, $cur_page, $param = '', $br = true, $prefix = true) {
    global $root_dir;
    
    if ($br) print('<BR>');
    print('<span id="pages">');
    if ($prefix) {
      print('<B>Pages</B>: ');
    }
    $how_many = 10;
    $start = $page - $how_many;
    $end = $page + $how_many - 1;
    if ( $start > 0 ) {
        if ( $start > 1 ) {
            print(' <a target="' . $target . '" href="' . $root_dir . $cur_page . '?page=1' . $param . '">&lt;&lt;</a> |');
        }
        print(' <a target="' . $target . '" href="' . $root_dir . $cur_page . '?page=' . $start . $param . '">&lt;</a> |');
        $start = $start + 1;
    } else {
        $start = 1;
    } 
    
    if ( $end > $max_page ) {
        $end = $max_page;
    }

    for ($i = $start; $i <= $end; $i++) {
        if ($i != $start) {
          print(' | ');
        }
        if ( $i == $page ) {
            print('' . $i);
        } else {
            print('<a target="' . $target . '" href="' . $root_dir . $cur_page . '?page=' . $i . $param . '">'. $i . '</a>');
        }
    }
    if ( $end < $max_page ) {
        $end = $end + 1;
        print(' <a target="' . $target . '" href="' . $root_dir . $cur_page . '?page=' . $end . $param . '">&gt;</a> ');
        if ( $end < $max_page ) {
            print(' <a target="' . $target . '" href="' . $root_dir . $cur_page . '?page=' . (int) $max_page . '' . $param . '">&gt;&gt;</a> ');
        }
    }    
    print('&nbsp;&nbsp;</span>');
}

function print_pages_old($max_page, $page, $target, $cur_page) {

    global $root_dir;
    print('<BR><B>Pages</B>: ');
    for ($i = 1; $i <= $max_page; $i++) {
        if ( $i == $page ) {
            print(' ' . $i . ' |');
        } else {
            print(' <a target="' . $target . '" href="' . $root_dir . $cur_page . '?page=' . $i . '">'. $i . '</a> |');
        }
    }
}

function print_msgs($ar, $msgs) {

    $keys = array_keys($ar);
    print("<dl><dd>\n");
    foreach ($keys as $key) {
        //if ($msgs[$key] != "") {
        print($msgs[$key]);
        print("<BR>\n");
        //}
        if (sizeof($ar[$key]) > 0) {
            print_msgs($ar[$key], $msgs);
        }
    }
    print("</dd></dl>\n");
}

function build_content_tree($msg_line, $msg_id, $msg_level, $msg_parent, &$content, &$msgs, &$glob_map, &$armass, &$l) {

    $msgs[$msg_id] = $line;
    if ($msg_level == 0) {
        $content[$row['id']] = &$armass[$l];
        $glob[$row['id']] = &$armass[$l];
    } else {
        $cur_content = &$glob[$row['parent']];
        $cur_content[$row['id']] = &$armass[$l];
        $glob[$row['id']] = &$armass[$l];
    }
    $l++;
}

function validateEmail($email){

#copyright (c)John W. List 2002-2003 http://www.technotoad.com
/*
This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*/

    $output = "";   
    $email = trim($email);
    #shortest valid email address is 5 characters
    If ((strlen($email)<5) or strstr($email," ")){
        $output = "Email is too short, " . strlen($email) . " characters.";
        return $output;
    }
    If (substr_count($email, "@")<>1){
        #If we find one and only one @, then the
        #email address is good to go.
        $output = "Email has missing @ character or too many @ characters.";
        return $output; 
    } else { #it has an at,split into 2 halves on the @
        $halves=explode ("@", $email);
        # look for text on either side of the at
        # at least 1 character on the lhs and at least 3 on the RHS
        If (strlen($halves[0])<1 or strlen($halves[1])<=3){
            $output = "Email address is invalid.";
            return $output; 
        }
        #dot search
        #look at LHS
        #no dots on the ends of LHS string
        If (substr($halves[0],0,1)=="." or substr($halves[0],-1,1)=="."){
            $output = "Email address is invalid.";
            //$output = "There is a dot on the end of the LHS of this address.";
            return $output; 
        }
        #look at RHS
        #no dots on the ends of RHS string
        If (substr($halves[1],0,1)=="." or substr($halves[1],-1,1)=="."){
            $output = "Email address is invalid.";
            //$output = "There is a dot on the end of the RHS of this address.";    
            return $output; 
        }
        #check it contains a dot and no dotdots
        if (!strstr($halves[1],".") or strstr($halves[1],"..")){
            //$output = "This address has the wrong number of dots in it.";
            $output = "Email address is invalid.";
            return $output; 
        }
    }
    return $output;
}


function encode_subject($subj) {

    $proceeded = false;
    $subj = htmlentities(translit($subj, $proceeded), HTML_ENTITIES,'UTF-8');
    return $subj;
}

function m_print_msgs($ar, $msgs, $pix) {
    $keys = array_keys($ar);
$pix=$pix+10;
#print ("s ". $pix. "\n");
#    print("<dd style='margin-left:" . $pix . "px' >\n");
    #print("<dl style='position:relative; left:-20px'><dd>\n");
    foreach ($keys as $key) {
print("<dd style='margin-left:" . $pix . "px' >\n");
#print ("inside ". $pix. "\n");
        print($msgs[$key]);
        print("\n");
        if (sizeof($ar[$key]) > 0) {
            m_print_msgs($ar[$key], $msgs, $pix);
        }
    }
#    print("</dd></dl>\n");
$pix=$pix-10;
#print ("e " . $pix . "\n");
}


function m_print_threads($result, &$content) {
#global $pix;
    global $root_dir;
    global $page_msg;
    global $page_byuser;
    global $page_topthread;
    global $page;
    global $cur_page;
    global $page_collapsed;
    global $page_thread;
    global $prop_bold;
    global $content_nsfw;
#$pix=0;
    $msgs = array();
    $content = array();
    $cur_content = &$content;
    $stack = array();
    $stack[0] = &$content;
    $level = 0;
    $armass = array();
    $glob = array();
    $l = 0;
    $b_start = '';
    $b_end = '';

    if ( !is_null($prop_bold) && $prop_bold > 0 &&  strcmp( $cur_page, $page_collapsed ) ) { 
        $b_start = '<b>';
        $b_end = '</b>';
    }

    while ($row = mysql_fetch_assoc($result)) {

        $length = $row['chars'];
        $armass[$l] = array();
        $auth_text = '';
        $moder = $row['moder'];
        #if (!is_null($moder)) {
        if ($moder == 1) {
            $auth_text = '<font color="green"> *</font>';
        } else {
            $auth_text = '';
            #$auth_text = '<font color="red">*</font>';
        }
        $banned = false;
        $ban_ends = $row['ban_ends'];
        $subj = $row['subject'];

        $subj = encode_subject($subj);
        $enc_user = htmlentities($row['username'], HTML_ENTITIES,'UTF-8');
        if ( !is_null( $ban_ends ) && strcmp( $ban_ends, '0000-00-00 00:00:00' ) ) {
            $banned = true;
        }
        if ( $banned === true ) {
            $enc_user = '<font color="grey">' . $enc_user . '</font>';
        }
        $enc_user = '<a class="user_link" href="' . $root_dir . $page_byuser . '?author_id=' . $row['user_id'] . '" target="contents">' . $enc_user . '</a>';  
        if ($row['status'] == 2 ) {

            if ($row['level'] == 0) {
                $line = '&nbsp;<img border=0 src="images/bs.gif" alt="*"> <I><font color="gray"><del>This message has been deleted</del></font></I> ';
            } else {
                $line = '&nbsp;<img border=0 src="images/dc.gif" alt="*"> <I><font color="gray"><del>This message has been deleted</del></font></I> ';
            }
        } else {
            $nsfw = '';
            if ($row['content_flags'] & $content_nsfw) {
              $nsfw .= ' <span class="nsfw">NSFW</span>';
            }          
            if ($row['level'] == 0) {
                $line = '<div id="div_mes_' . $row['msg_id'] . '" onclick="this.style.display=\'none\'" class="show_message"></div><a href="#" onclick="show_message(\'' . $row['msg_id'] . '\'); return false;" class="index"><b>' . $row["username"] . ': </b>' . $subj . '</a>'.$nsfw.'</dd>';
                #$line = '&nbsp;<a id="' . $row['msg_id'] . '" onClick="SelectAll(\'' . $row['msg_id'] . '\');"  name="' . $row['msg_id'] . '" target="' . detect_mobile() . '" href="' . $root_dir . $page_msg . '?id=' . $row['msg_id'] . '"><img border=0 src="images/bs.gif" width=16 height=16 alt="*"> ' . $b_start . $subj . $b_end . '  </a> ';
            } else {
                $line = '<div id="div_mes_' . $row['msg_id'] . '" onclick="this.style.display=\'none\'" class="show_message"></div><a href="#" onclick="show_message(\'' . $row['msg_id'] . '\'); return false;" class="index"><b>' . $row["username"] . ': </b>' . $subj . '</a>'.$nsfw.'</dd>';
                #$line = '&nbsp;<a id="' . $row['msg_id'] . '" onClick="SelectAll(\'' . $row['msg_id'] . '\');"  name="' . $row['msg_id'] . '" target="' . detect_mobile() . '" href="' . $root_dir . $page_msg . '?id=' . $row['msg_id'] . '"><img border=0 src="images/dc.gif" width=16 height=16 alt="*"> ' . $subj . '  </a> ';
            }
        }
        #$line .= '<b>' . $enc_user . '</b>' . $auth_text .  ' ' . $row['created'] . ' <b>' . $length . ' b</b>/' . $row['views'] . ' views';
        if ( !strcmp( $cur_page, $page_collapsed ) ) {
            $line .= ' <font color="gray">[ <a href="' . $root_dir . $page_topthread . '?thread=' . $row['thread_id'] . '&page=' . $page . '" target="contents">+' . $row['counter'] . '</a> ] </font>    ';
        }
        $msgs[$row['msg_id']] = $line;
        if ($row['level'] == 0) {
            $content[$row['msg_id']] = &$armass[$l];
            $glob[$row['msg_id']] = &$armass[$l];
        } else {
            $cur_content = &$glob[$row['parent']];
            $cur_content[$row['msg_id']] = &$armass[$l];
            $glob[$row['msg_id']] = &$armass[$l];
        }

        $l++;
    } 
    return $msgs;
}

function sendmail($address, $subj, $body) {
	$body = str_replace("\n.", "\n..", $body);
	$body = wordwrap($body, 70, "\r\n");
	$headers = 'From: kirdyk.forum@gmail.com' . "\r\n" .
    'Reply-To: do_not_reply@kirdyk.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
	
	$result = mail ( $address, $subject, $body, $headers);
	
	return $result;
}

// =================== YOUTUBE/RUTUBE ===================

function unless_in_url_tag($pattern) {
return '\[url='.$pattern.'|\[url\]'.$pattern.'|('.$pattern.')';
}

function youtube($body, $embed = true) {
  global $host, $google_key;
  
  $pattern = '(?:https?://)?(?:www\.|m\.)?(?:\byoutu\b\.be/|\byoutube\b\.com/(?:embed|v|watch\?(?:[^\s<\]"]*?)?v=))([\w-]{10,12})(?:(?:\?|&)[^\s<\]"]*)?';
	
  $result = preg_replace_callback('#'.unless_in_url_tag($pattern).'#i',
    function ($matches) use ($embed, $host, $pattern, $google_key) {
      // var_dump($matches);
      if(count($matches) < 5) return $matches[0];
      
      if(!preg_match('#'.$pattern.'#i', $matches[0], $matches)) 
        return $matches[0];

      $url = $matches[0];
			$id  = $matches[1];

      $new_body = $url;
      
      if (isset($google_key)) {
        if (strcmp($google_key, "TEST") == 0) {
            $duration = "11:22:33";
            $title = "Test title";
            $thumbnail = 'http://files.softicons.com/download/system-icons/oxygen-icons-by-oxygen/png/128x128/actions/thumbnail.png';
        } else {          
          $obj2 = file_get_contents(
            "https://www.googleapis.com/youtube/v3/videos?part=contentDetails,snippet&id=" . $id . "&key=".$google_key."&fields=pageInfo(totalResults),items(id,contentDetails/duration,snippet(title,thumbnails/default))");
          
          if($obj2 === FALSE) 
            return $url;

          $ar2 = json_decode($obj2);
          // var_dump($ar2);         			 
          if ($ar2->pageInfo->totalResults == 1) {
            $duration = '';
            $di = new DateInterval($ar2->items[0]->contentDetails->duration);
            if ($di->h > 0) {
              $duration .= $di->h.':';
            }
            $duration .= $di->i . ':' . $di->s;
            $title = $ar2->items[0]->snippet->title;
            $thumbnail = $ar2->items[0]->snippet->thumbnails->{'default'}->url;
          }
        }
      }
      if ($embed) {
          $params = ''; 
          if (preg_match('#\?(.*)$#i', $url, $times)) {
            $params .= '&'.$times[1];
          } 
          if (preg_match('/(?:\?|\#)t=([0-9]+)(?:$|&)/', $url, $times)) {
            $params .= '&start='.$times[1];
          } 
          //$new_body = '[iframe id="youtube" type="text/html" width="480" height="320" src="http://www.youtube-nocookie.com/embed/' . $id . '?fs=1&enablejsapi=1&start=0&wmode=transparent&origin=http://' . $host . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen]';
          $new_body = '[iframe id="youtube-video" type="text/html" width="480" height="320" src="http://www.youtube-nocookie.com/embed/' . $id . '?enablejsapi=1'.$params.'&wmode=transparent&origin=http://' . $host . '" frameborder="0" allowfullscreen]';
          if (isset($title)) {
            $new_body .= "\n[i][color=lightslategrey][url=".$url. "][b]" . $title . "[/b]; " . $duration . "[/url][/color][/i] ";
          } else {
            $new_body .= "\nLink: [url]".$url."[/url]";
          }
          $new_body = '[render=' . $url . ']' . $new_body . '[/render]';
      } else if (isset($title)) {
          $new_body = "\n[i][color=lightslategrey]( " . "[b]" . $title . "[/b]; " . $duration . ")[/color][/i] ";
          $new_body .= "\n[img=" . $thumbnail . "]";
          $new_body = '[render=' . $url . ']' . $new_body . '[/render]';
      }
      return $new_body;
		},
		$body
	);
  
	return rutube($result);
}

function rutube($body, $embed = true) {
  global $host;
  
  $pattern = '(?:https?://)?(?:www\.)?rutube\.ru/video/([\w-]{10,32})/(?:(?:\?|&)[^\s<\]"]*)?';
	
  $result = preg_replace_callback('#'.unless_in_url_tag($pattern).'#i',
    function ($matches) use ($embed, $host, $pattern) {
      // var_dump($matches);
      if(count($matches) < 5) return $matches[0];
      
      if(!preg_match('#'.$pattern.'#i', $matches[0], $matches)) 
        return $matches[0];

      $url = $matches[0];
			$id  = $matches[1];

			$obj2 = file_get_contents("http://www.rutube.ru/api/video/" . $id . "/");
			//var_dump($obj2);
      if($obj2 === FALSE) return $url;
      
			$ar2 = json_decode($obj2);       
			$new_body = $url;
      
			if ( $ar2 !== false ) {
        // calculate duration
        $di = intval($ar2->duration);
        $duration = '';
        if (((int)($di / 3600)) > 0) {
          $duration .= ((int)($di / 3600)).':';
        }
        $di %= 3600;
        $duration .= ((int)($di / 60)) . ':' . ($di % 60);
        $title = $ar2->title;
        
        if ($embed) {
          $new_body = preg_replace(array('#<iframe (.*)></iframe>#i', '#width="([0-9]*)"#i', '#height="([0-9]*)"#i'), array('[iframe id="rutube-video" $1]','width="480"','height="320"'), $ar2->html);
          $new_body .= "\n[i][color=lightslategrey][url=".$url. "][b]" . $title . "[/b]; " . $duration . "[/url][/color][/i] ";
        } else {
          $thumbnail = $ar2->thumbnail_url;
          $new_body .= "\n[i][color=lightslategrey]( " . "[b]" . $title . "[/b]; " . $duration . ")[/color][/i] ";
          $new_body .= "\n[img=" . $thumbnail . "]";
        }
        $new_body = '[render=' . $url . ']' . $new_body . '[/render]';        
			 }
			 return $new_body;
			},
			$body
		);
		
	return dailymotion($result);
}

// Dailymotion URLs e.g. http://www.dailymotion.com/video/x3anr9r_cat-goes-nuts-chasing-light-reflection_fun
function dailymotion($body, $embed = true) {
  global $host;
  
  $pattern = '(?:https?://)?(?:www\.)?dailymotion\.com/video/([0-9a-z]{6,8})_?[^\s<\]"]*(?:(?:\?|&)[^\s<\]"]*)?';
	
  $result = preg_replace_callback('#'.unless_in_url_tag($pattern).'#i',
    function ($matches) use ($embed, $host, $pattern) {
      // var_dump($matches);
      if(count($matches) < 5) return $matches[0];
      
      if(!preg_match('#'.$pattern.'#i', $matches[0], $matches)) 
        return $matches[0];

      $url = $matches[0];
			$id  = $matches[1];
  
			$obj2 = file_get_contents("https://api.dailymotion.com/video/".$id."?fields=allow_embed,embed_html,title,duration,thumbnail_360_url");
      // Example: {"allow_embed":true,"embed_html":"<iframe frameborder=\"0\" width=\"480\" height=\"270\" src=\"\/\/www.dailymotion.com\/embed\/video\/x26ezj5\" allowfullscreen><\/iframe>","title":"Greetings","duration":70}
			// var_dump($obj2); 
      if($obj2 === FALSE) return $url;
      
			$ar2 = json_decode($obj2);       
			$new_body = $url;
      
			if ( $ar2 !== false ) {
        // calculate duration
        $di = intval($ar2->duration);
        $duration = '';
        if (((int)($di / 3600)) > 0) {
          $duration .= ((int)($di / 3600)).':';
        }
        $di %= 3600;
        $duration .= ((int)($di / 60)) . ':' . ($di % 60);
        $title = $ar2->title;
        
        if ($embed && $ar2->allow_embed) {
          $new_body = preg_replace(array('#<iframe (.*)></iframe>#i', '#width="([0-9]*)"#i', '#height="([0-9]*)"#i'), array('[iframe id="dailymotion-video" $1]','width="480"','height="320"'), $ar2->embed_html);
          $new_body .= "\n[i][color=lightslategrey][url=".$url. "][b]" . $title . "[/b]; " . $duration . "[/url][/color][/i] ";
        } else {
          $thumbnail = $ar2->thumbnail_360_url;
          $new_body .= "\n[i][color=lightslategrey]( " . "[b]" . $title . "[/b]; " . $duration . ")[/color][/i] ";
          $new_body .= "\n[img=" . $thumbnail . "]";
        }
        $new_body = '[render=' . $url . ']' . $new_body . '[/render]';        
			 }
			 return $new_body;
			},
			$body
		);
		
	return $result;
}

function twitter($body, $embed = true) {
  if (!$embed) return $body;
  
  // e.g. https://twitter.com/elonmusk/status/627040381729906688 or https://twitter.com/K4rlHungus/status/772244915128598528?s=09
  $pattern = '(?:https?://)(?:twitter\.com/)(?:[^\s<\]"]*?)/status/([0-9]*)(?:/[^\s<\]"]*)?(?:(?:\?|&)[^\s<\]"]*)?\s*';
	
  $result = preg_replace_callback('#'.unless_in_url_tag($pattern).'#is',
    function ($matches) use ($embed, $pattern) {
      // var_dump($matches);
      if(count($matches) < 5) return $matches[0];
      
      if(!preg_match('#'.$pattern.'#i', $matches[0], $matches)) 
        return $matches[0];

      $url = preg_replace('/\s+/', '', $matches[0]);
			$id  = $matches[1];

      $obj2 = file_get_contents("https://api.twitter.com/1/statuses/oembed.json?url=" . $url);
      
      if($obj2 === FALSE) 
        return $url;

      $ar2 = json_decode($obj2);
      // var_dump($ar2);         			 
      return trim(preg_replace('/\s+/', ' ', $ar2->html));
		},
		$body
	);
  
	return $result;
}

function gfycat($body, $embed = true) {
  if (!$embed) return $body;
  
  // e.g. https://gfycat.com/BrightFragrantAmurstarfish
  $pattern = '(?:https?:\/\/)(?:gfycat\.com\/)([^\s<\]"]*)(?:\/[^\s<\]"]*)?';
	
  $result = preg_replace_callback('#'.unless_in_url_tag($pattern).'#is',
    function ($matches) use ($embed, $pattern) {
      // var_dump($matches);
      if(count($matches) < 4) return $matches[0];
      
      if(!preg_match('#'.$pattern.'#i', $matches[0], $matches)) 
        return $matches[0];

      $url = $matches[0];
			$id  = $matches[1];

      $obj2 = file_get_contents("https://gfycat.com/cajax/get/" . $id);
      
      if($obj2 === FALSE) 
        return $url;

      $ar2 = json_decode($obj2);
      // var_dump($ar2);
      return trim(preg_replace('/\s\s+/', ' ', $ar2->gfyItem->gifUrl)).'<br/>Direct link: <a href="'.$url.'">'.$url.'</a>';
		},
		$body
	);
  
	return $result;
}

function instagram($body, $embed = true) {
  if (!$embed) return $body;
  
  // e.g. https://www.instagram.com/p/BGyE7jfF2of/
  $pattern = '(?:https?://)(?:www\.)?(?:instagram\.com/p/)([0-9a-zA-Z]*)(?:/[^\s<\]"]*)?';
	
  $result = preg_replace_callback('#'.unless_in_url_tag($pattern).'#is',
    function ($matches) use ($embed, $pattern) {
      // var_dump($matches);
      if(count($matches) < 5) return $matches[0];
      
      if(!preg_match('#'.$pattern.'#i', $matches[0], $matches)) 
        return $matches[0];

      $url = $matches[0];
			$id  = $matches[1];

      $obj2 = file_get_contents("https://api.instagram.com/oembed/?url=" . $url);
      
      if($obj2 === FALSE) 
        return $url;

      $ar2 = json_decode($obj2);
      // var_dump($ar2);         			 
      return trim(preg_replace('/\s\s+/', ' ', $ar2->html));
		},
		$body
	);
  
	return $result;
}

// Returns random Chuck Norris fact in $percentage cases
function chuck($percentage) {
 if (rand(0, 100) < $percentage) {
	 $obj2 = file_get_contents("http://api.icndb.com/jokes/random?exclude=[explicit]");
	 //var_dump($obj2);                     			 
	 $ar2 = json_decode($obj2);
	 if ( $ar2 !== false && $ar2->type == "success") {
		return $ar2->value->joke;
	 }
 }
 return "";
}

function can_edit_post($msg_author, $msg_time, $current_user, $msg_id) {

  // Editing is enabled if the user is an author, the message is less than 1 day old and there were no answers
  if ( strcmp($msg_author, $current_user) != 0)
    return false;

  $time = strtotime($msg_time); // time the message was created (server time)
  $curtime = time(); // current time (server time)
   
  $diff = ($curtime - $time) / 3600;
  
  if ($diff > 24) return false;
/*    
  $query = "SELECT count(*) as cnt from confa_posts where parent = " . $msg_id;
  $result = mysql_query($query);
  if (!$result) {
    mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query . 'test_user_id="' . $user_id . '"');
    die('Query failed ' );
  }
  
  if ($row = mysql_fetch_assoc($result)) {
    return $row['cnt'] == 0;
  }
*/  
  return true;
}

function login($username, $passw, $create_session=true) {
  
    global $auth;
    global $moder;
    global $err_login;
    global $ban;
    global $user_id;
    global $db_pass;  // check if this is used anywhere else
    global $ban_ends;
    global $new_pm;
    global $prop_bold;
    global $server_tz;
    global $prop_tz;
    global $ban_time;
    global $logged_in;
    global $menu_style;
    global $ip;
    global $host;
    global $root_dir;
    
    global $user;
    global $password;
    
    $logged_in = false;
    $err_login = null;
    
    $user = $username;
    $password = $passw;
    
    do {
        if (is_null($user) || strlen($user) == 0) {
            $err_login = 'Username is required';
            break;
        }  
        if (is_null($password) || strlen($password) == 0) {
            $err_login = 'Password is required';
            break;
        }     
        $query = 'SELECT id, username, password, prop_bold, prop_tz, status, moder, ban, ban_ends, new_pm  FROM confa_users where username = \'' . mysql_real_escape_string($user) . '\' and password=password(\'' . mysql_real_escape_string($password) . '\')';
        $result = mysql_query($query);
        if (!$result) {
            mysql_log( __FILE__ . ':' . __LINE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed');
        }
        mysql_log( __FILE__ . ':' . __LINE__, 'query succeded numrows= ' . mysql_num_rows($result) . ' for username= ' . $user . ' QUERY: ' . $query);
        if (mysql_num_rows($result)  < 1) {
            mysql_log( __FILE__ . ':' . __LINE__, 'num_rows < 1' . $user . ' QUERY: ' . $query);
            $query = 'SELECT id, username, password, prop_bold, prop_tz, status, moder, ban, ban_ends, new_pm  FROM confa_users where username = \'' . mysql_real_escape_string($user) . '\' and password=old_password(\'' . mysql_real_escape_string($password) . '\')';
            $result = mysql_query($query);
            mysql_log( __FILE__ . ':' . __LINE__, 'result ' . $user . ' QUERY: ' . $query);
            if (!$result) {
                mysql_log( __FILE__ . ':' . __LINE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                die('Query failed');
            } 
            mysql_log( __FILE__ . ':' . __LINE__, '2 ' . $user . ' QUERY: ' . $query);
            if (mysql_num_rows($result) == 0) {
                $query = 'SELECT id, username, password, prop_bold, prop_tz, status, moder, ban, ban_ends, new_pm  FROM confa_users where username = \'' . mysql_real_escape_string($user) . '\' and password=\'' . mysql_real_escape_string($password) . '\'';
                $result = mysql_query($query);            
                mysql_log( __FILE__ . ':' . __LINE__, '2 ' . $user . ' QUERY: ' . $query);
                if (mysql_num_rows($result) == 0) {
                  $err_login = 'Wrong username or password';
                  break;
                } else {
                  mysql_log( __FILE__ . ':' . __LINE__, 'successfull login with plain password');
                }
            } else {
                mysql_log( __FILE__ . ':' . __LINE__, 'successfull login with old password');
            }
        }
        
        $ban = false;
        $row = mysql_fetch_assoc($result);
        $user_id = $row["id"];
        $db_pass = $row["password"];
        $ban_ends = $row["ban_ends"];
        $new_pm = $row["new_pm"];
        $prop_bold = $row["prop_bold"];
        $prop_tz  = $row['prop_tz'];
        $moder = $row['moder'];

        if (is_null($prop_tz)) {
          $prop_tz = explode(":", $server_tz)[0];
        }
        
        if ( $row['status'] == 2 ) {
            $err_login = ' This user has been disabled.';
            break;
        }
        
        mysql_log( __FILE__, 'before ban ends ' . $user . ' QUERY: ' . $query);
        
        if (!is_null($ban_ends)) {
            $ban_time = strtotime($ban_ends);
            if ($ban_time > time()) {
                $ban = true;
                #$err_login = 'Sorry, you have been banned form this forum till ' . $ban_ends;
                $query = 'SELECT CONVERT_TZ(ban_ends, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as ban_ends from confa_users where id=' . $user_id ;
                $result = mysql_query($query);
                if (!$result) {
                    mysql_log( __FILE__, 'insert failed ' . mysql_error() . ' QUERY: ' . $query);
                    die('Query failed');
                }
                $row = mysql_fetch_row($result);
                $ban_ends = $row[0];

            } else {
                $query = 'UPDATE confa_users set ban_ends = \'0000-00-00 00:00:00\' where id=' . $user_id;
                $result = mysql_query($query);
                if (!$result) {
                    mysql_log( __FILE__, 'update failed ' . mysql_error() . ' QUERY: ' . $query);
                    die('Query failed');
                }
                $ban_ends = NULL;
            }
        }

        $auth = '1';
        $logged_in = true;
      
        if ($create_session) {
          $tm = date('Y-m-d H:i:s');
          $md5 = md5($tm . $ip . $user);
          
          $query = 'INSERT into confa_sessions(created, user_id, hash) values(\'' . $tm . '\', ' . $user_id . ', \'' . $md5 . '\')';
          
          $result = mysql_query($query); 
          if (!$result) {
              mysql_log( __FILE__, 'insert failed ' . mysql_error() . ' QUERY: ' . $query);
              die('Query failed');
          }
          
          mysql_log( __FILE__, 'insert query succeded for username= ' . $user . ' QUERY: ' . $query);
          
          setcookie('auth_cookie2', $md5, 1800000000, $root_dir, $host, false, true);
          setcookie('user2', $user, 1800000000, $root_dir, $host, false, true);
        } else {
          mysql_log( __FILE__, 'login succeded for username= ' . $user . '. no session was created');
        }
        
    } while(false);

    if ($err_login != null) {
      mysql_log( __FILE__, 'Login error: <' . $err_login . '>');
    }
    
    return $logged_in;
}

function like($user_id, $msg_id, $val=1, $reaction=null) {
  global $logged_in, $ban;
  
  if (!$logged_in || $ban) { // just in case
    return false; // "User not logged in or banned from forum";
  }

  if (is_null($reaction)) 
    $query = 'INSERT INTO confa_likes(user, post, value) values(' .
      $user_id . ', ' . $msg_id . ', ' . $val . ') ON DUPLICATE KEY UPDATE value=IFNULL(value+' . $val.', '.$val.')';
  else
    $query = 'INSERT INTO confa_likes(user, post, reaction) values(' .
      $user_id . ', ' . $msg_id . ', \''. mysql_real_escape_string($reaction).'\') ON DUPLICATE KEY UPDATE reaction=IF(reaction=\'' . mysql_real_escape_string($reaction) . '\', NULL, \'' . mysql_real_escape_string($reaction) . '\')';
  
  mysql_log( __FILE__ . ":" . __LINE__, 'Reaction QUERY: ' . $query);
  
  $result = mysql_query($query);
  if (!$result) {
      mysql_log( __FILE__ . ":" . __LINE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
      return false;
  }
  
  $query = 'select value from confa_likes where user=' . $user_id . ' and post = ' . $msg_id;

  $result = mysql_query($query);
  if (!$result) {
      mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
      return false;
  }
  $row = mysql_fetch_assoc($result);
  mysql_free_result($result);
  if ($row != null) {
    $new_val = $row['value'];
    $query = '';
    switch ($new_val) {
    case 1:
         if ($val > 0) {
             $query = 'UPDATE confa_posts set likes=likes+1 where id=' . $msg_id;
         }
         break;
    case -1:
         if ($val < 0) {
             $query = 'UPDATE confa_posts set dislikes=dislikes+1 where id=' . $msg_id;
         }
         break;
    case 0:
         if ($val > 0) {
             $query = 'UPDATE confa_posts set dislikes=dislikes-1 where id=' . $msg_id;
         } else if ($val < 0)/* dislike */ {
             $query = 'UPDATE confa_posts set likes=likes-1 where id=' . $msg_id;
         }
        break;
    } 
    if (strlen($query) > 0 ) {
      $result = mysql_query($query);
      if (!$result) {
          mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
          return false;
      }
    }
    return $new_val;
  }

  return true;
}

function bookmark($user_id, $msg_id, $add=true) {
  if ($add) {
    $query = 'insert into confa_bookmarks(user, post) values(' . $user_id. ', ' . $msg_id . ');';
  } else {
    $query = 'delete from confa_bookmarks where user=' . $user_id. ' and post=' . $msg_id . ';';
  }
  $result = mysql_query($query);
  if (!$result) {
       mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
       return false;
  }
  return $result;
}

function report($user_id, $msg_id, $mode) {
  global $content_nsfw, $content_boyan, $link;
  $content_flags = 0;
  
  if (is_null($user_id)) return true; // do nothing
  
  if (!strcmp($mode, "nsfw"))
    $content_flags |= $content_nsfw;
  else if (!strcmp($mode, "boyan"))
    $content_flags |= $content_boyan;
  
  if ($content_flags == 0) return true;

  $query = 'update confa_reports set content_flags = content_flags ^ ' . $content_flags . ' where user=' . $user_id. ' and post=' . $msg_id . ';';
  $result = mysql_query($query);
  if (!$result) {
       mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
       return false;
  }
  if (mysql_affected_rows($link) == 0) {
    $query = 'insert into confa_reports(user, post, content_flags) values(' . $user_id. ',' . $msg_id . ','. $content_flags.');';
    $result = mysql_query($query);
    if (!$result) {
         mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
         return false;
    }
  }
  // mark the post only if several users reported the message as <$mode>
  $content_flags = 0;
  
  $query = 'select count(*) from confa_reports where content_flags & ' . $content_nsfw . ' and post=' . $msg_id . ';';
  $result = mysql_query($query);
  if (!$result) {
       mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
       return false;
  }
  if (mysql_result($result, 0) >= 2) {
    $content_flags |= $content_nsfw;
  }
  
  $query = 'select count(*) from confa_reports where content_flags & ' . $content_boyan . ' and post=' . $msg_id . ';';
  $result = mysql_query($query);
  if (!$result) {
       mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
       return false;
  }
  if (mysql_result($result, 0) >= 2) {
    $content_flags |= $content_boyan;
  } else {
    $content_flags &= ~$content_boyan;
  }
  
  if ($content_flags) {
    $query = 'update confa_posts set content_flags = content_flags | ' . $content_flags . ' where id=' . $msg_id . ';';
    $result = mysql_query($query);
    if (!$result) {
      mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
      return false;
    }
  }
  return true;
}

function validate($subj, $body, $to) {
  global $err_login, $logged_in, $ban, $ban_ends;

  $err = '';
  
  do {
    if (!is_null($err_login) && strlen($err_login) > 0 ) {
        $err = $err_login;
        break;
    }
    if (!$logged_in) {
        $err = 'You are not logged in';
        break;
    }
    if ($ban) {
        $err = 'You have been banned from this forum till ' . $ban_ends;
        break;
    }
    if (isset($to) && (is_null($to) || strlen($to) == 0)) {
        $err .= "Recipient not defined<BR/>";
    }
    if (strlen($subj) > 254) {
        $err .= "Subject longer 254 bytes<BR/>";
    } else if (strlen(trim($subj)) == 0) {
        $err .= "No subject<BR/>";
    }
    if (!is_null($body) && strlen($body) > 32765) {
        $err .= "Body longer 32765 bytes<BR/>";
    }
  } while(false);
  
  return $err;
}

// Returns an error string, or array with an ID if successful
function post($subj, $body, $re=0, $msg_id=0, $ticket="", $nsfw=false, $to) {
  global $err_login, $logged_in, $ban, $ip, $agent, $user_id, $content_nsfw, $from_email, $host, $user, $page_goto;
  
  $err = @validate($subj, $body, $to);
  
  if (strlen($err) != 0) {
    return $err;
  } else if (!$logged_in || $ban) { // just in case
    return "User not logged in or banned from forum";
  }
  
  $chars = 0;
  $content_flags = 0;
  
  if (!is_null($body) && strlen($body) != 0) {
    $chars = strlen(utf8_decode($body));
    $length = strlen($body);
    if (has_images($body)) {
        $content_flags |= 2;
    }
    $new_body = render_for_db($body);
    $has_video = false;
    before_bbcode($body, $has_video);
    if (/* check for vimeo/coub/fb clips */ $has_video || preg_match('/id="[a-z]*-video"/', $new_body)) {
        $content_flags |= 4;
    }
    $ibody = '\'' . mysql_real_escape_string($new_body) . '\'';
  } else {
    $ibody = "''";
  }
  
  if (isset($nsfw) && $nsfw !== false) {
    $content_flags |= $content_nsfw;
  }
  
  $subj = grammar_nazi($subj);

  if (isset($to)) {
    $query = 'SELECT id from confa_users where username=\'' . mysql_real_escape_string($to) . '\' and status != 2';
    $result = mysql_query($query);
    if (!$result) {
        mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        return 'Query failed';
    }
    $row = mysql_fetch_assoc($result);
    $to_id = $row['id'];
    if (is_null($to_id)) {
        return "No such recipient";
    }
  }
  
  if ( strlen($ticket) > 0 ) {
    $query = 'INSERT into confa_tickets(ticket) values(\'' . $ticket . '\')';
    $result = mysql_query($query);
    if (!$result) {
        mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        return 'This is duplicated post (ticket ' . $ticket . ')';
    }
  }
  
  if (isset($to_id)) {
    // send pmail
    $query = 'INSERT INTO confa_pm(sender, receiver, subject, body, chars) values(' . $user_id . ', ' . $to_id 
      . ', \'' . mysql_real_escape_string($subj) . '\', ' . $ibody . ', ' . $chars . ')';

    $result = mysql_query($query);
    if (!$result) {
        mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        return 'Query failed';
    }

    $id = mysql_insert_id();
    update_new_pm_count($to_id);
    
    return array("id" => $id);
    
  } else if ( isset($msg_id) && $msg_id > 0 ) {
    // update existing post
    $query = 'SELECT p.subject, p.body, p.status, p.author, p.created, p.thread_id, p.level, p.closed as post_closed, p.id, t.closed as thread_closed, ( select max(page) from confa_threads) - t.page + 1 as page from confa_posts p, confa_threads t where t.id=p.thread_id and p.id=' . $msg_id;
    $result = mysql_query($query);
    if (!$result) {
      mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
      return 'Query failed';
    }
    
    if (mysql_num_rows($result) == 0) {
      return "Message not found";
    }
    
    $row = mysql_fetch_assoc($result);
    
    $thread_id = $row['thread_id'];
    $old_subject = $row['subject'];
    $old_body = $row['body'];
    
    $closed = !is_null($row['post_closed']) && $row['post_closed'] > 0 || (!is_null($row['thread_closed']) && $row['thread_closed'] > 0 );
    if ( $closed || $row['status'] != 1 || !can_edit_post($row['author'], $row['created'], $user_id, $msg_id)) {
        return 'Modifications to this post are not allowed.';
    }
    
    if (strcmp($old_subject, $subj) != 0 || strcmp($old_body, $new_body) != 0) {
      // create a new version
      $query = 'INSERT INTO confa_versions (parent, subject, body, created, chars, IP, user_agent, views, content_flags) ' .
      ' SELECT id, subject, body, IF(ISNULL(modified), created, modified), chars, IP, user_agent, views, content_flags FROM confa_posts WHERE id=' . $msg_id;
      $result = mysql_query($query);
      if (!$result) {
        mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        return 'Query failed';
      } 
    }
    // update post
    $query = 'UPDATE confa_posts SET subject=\'' . mysql_real_escape_string($subj) . '\',body=' . $ibody . ',modified=now(),ip=' .$ip. ',user_agent=' .$agent. ',content_flags='.$content_flags . ', chars='. $chars . ',views=0 WHERE id=' . $msg_id;
    $result = mysql_query($query);
    if (!$result) {
        mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        return 'Query failed';
    }
    
    return array("id" => $msg_id);
    
  } else if (/*is_null($re) || strlen($re)*/ $re == 0) {
    // create new thread
    $query = 'select sum(counter) as cnt, page from confa_threads group by page desc limit 1';
    $result = mysql_query($query);
    if (!$result) {
      mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
      return 'Query failed';
    }
    $row = mysql_fetch_assoc($result);
    $last_page = $row['page'];
    if ($row['cnt'] > 200) {
      $last_page++;
    } 

    if (is_null($last_page)) {
        $last_page = 1;
    }
    $query = 'INSERT INTO confa_threads(author, page) values(' . $user_id . ', ' . $last_page . ')';
    $result = mysql_query($query);
    if (!$result) {
      mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
      return 'Query failed';
    }

    $thread_id = mysql_insert_id();
    $query = 'INSERT INTO confa_posts(status, parent, author, subject, body, created, thread_id, chars, auth, ip, user_agent, content_flags) values(1, 0, ' . $user_id . ',\'' . mysql_real_escape_string($subj) . '\', ' . $ibody . ', now(), ' .$thread_id . ', ' . $chars . ', 1, ' . $ip . ', ' . $agent . ', ' . $content_flags . ')';
    $result = mysql_query($query);
    if (!$result) {
      mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
      return 'Query failed';
    }
    $id = mysql_insert_id();
    $query = "UPDATE confa_users set status = 1 where id=" . $user_id;
    $result = mysql_query($query);
    if (!$result) {
      mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
      return 'Query failed';
    }
    
    return array("id" => $id, "thread_id" => $thread_id);
    
  } else {
    // respond to an existing post
    $query = 'SELECT p.thread_id, p.level, p.closed as post_closed, p.id, t.closed as thread_closed, ( select max(page) from confa_threads) - t.page + 1 as page, p.author as author_id, p.subject as old_subj, p.page as old_page from confa_posts p, confa_threads t where t.id=p.thread_id and p.id=' . $re;
    $result = mysql_query($query);
    if (!$result) {
      mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
      return 'Query failed';
    }
    
    if (mysql_num_rows($result) != 0) {
      $row = mysql_fetch_assoc($result);
      
      if ( (!is_null($row['post_closed']) && $row['post_closed'] > 0 ) || (!is_null($row['thread_closed']) && $row['thread_closed'] > 0 )) {
        return 'Replies to this post are disabled.';
      }
      
      $msg_page = $row['page'];
      if (is_null($msg_page)) {
          $msg_page = 1;
      }

      $thread_id = $row['thread_id'];
      $level = $row['level'];

      $level++;

      $query = 'UPDATE confa_threads set counter=counter+1 where id=' . $thread_id;
      $result = mysql_query($query);
      if (!$result) {
        mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        return 'Query failed';
      }

      $author_id = $row['author_id'];
      $old_subj = $row['old_subj'];
      $old_page = $row['old_page'];
    } else {
        return 'Cannot find parent for msg=' . $re;
    }
    $query = 'INSERT INTO confa_posts(status, parent, level, author, subject, body, created, thread_id, chars, auth, ip, user_agent, content_flags, page) values( 1, ' . $re . ', ' . $level . ', ' . $user_id . ',\'' . mysql_real_escape_string($subj) . '\', ' . $ibody . ', now(), ' . $thread_id . ', ' . $chars . ', 1, ' . $ip . ', ' . $agent . ', ' . $content_flags . ', '. $msg_page . ')'; 
    $result = mysql_query($query);
    if (!$result) {
      mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
      return 'Query failed';
    }
    $id = mysql_insert_id();
    
    // wtf is this for?
    $query = "UPDATE confa_users set status = 1 where id=" . $user_id;
    $result = mysql_query($query);
    if (!$result) {
      mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
      return 'Query failed';
    }
    
    // Send notificaton e-mail (if needed)
    if ($author_id != $user_id) {
      // Find e-mail of the author of the post being replied to
      $query = "SELECT u.email, u.reply_to_email, i.ignored FROM confa_users u left join confa_ignor i on u.id=i.ignored_by and i.ignored=$user_id WHERE u.id=$author_id";
      $result = mysql_query($query);
      if (!$result) {
        mysql_log( __FILE__ . __LINE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
      }else if (mysql_num_rows($result) != 0) {
        $row = mysql_fetch_assoc($result);
        if ($row['reply_to_email'] && is_null($row['ignored'])){
          // User wants to receive e-mail notifications
          $author_email = $row['email'];
          if (strlen($author_email) > 0){
            $who_replied = $user; // $row['username'];
            $email_subject = "You have a reply to your post on $host forum website";
  //            $message = "$who_replied replied to your post with subject: '$old_subj'\n\n--- The reply's body is below ---\n\n$body\n\n--- End of reply's body ---";
  //            $headers = "From: $from_email";
            $message = "";
            $message .= '<html><body><style type="text/css">';
            $message .= file_get_contents('css/disc2.css');          
            $message .= '</style><h3 id="subject">'.$subj.'</h3>';
            $message .= 'Author: <b>'.$who_replied.'</b><br/>';
            $message .= 'In response to your post: <a href="http://'.$host.'/'.$page_goto.'?id='.$re.'&page='.$old_page.'">'.$old_subj.'</a>';
            $message .= '<hr><div id="msgbody">';
            $message .= render_for_display($body);
            $message .= '</div><hr/>';
            $message .= '<p>Visit <a href="http://'.$host.'/'.$page_goto.'?re='.$id.'&page='.$msg_page.'">'.$host.'</a> to reply!</p>';
            $message .= '</body></html>';
            $headers = "From: $from_email\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n"; // ISO-8859-1
            mail($author_email,$email_subject,$message,$headers); 
          }
        }
      }
    }
    return array("id" => $id);
  }
  
  return "";
}

function get_answered($how_many=0, $update_ts=true) {
  global $last_answered_id, $server_tz, $prop_tz, $user_id, $auth_cookie;
  
  if (is_null($last_answered_id)) {
      $last_answered_id = 0;
  }

  if (/*!is_null($how_many) && ctype_digit($how_many*/ $how_many > 0) {  
    $query = 'SELECT b.id as my_id, b.author as me_author, u.username, u.moder, u.ban_ends, u.id as user_id, u.moder, p.closed as post_closed, p.level, p.page, CONVERT_TZ(p.modified, \'' . $server_tz . '\', \'' . $prop_tz . ':00\')  as modified, p.parent, p.auth, p.views, p.content_flags, p.likes, p.dislikes, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as created, p.subject, p.author, p.status, p.id as id, p.id as msg_id, p.chars, (select count(*) from confa_posts where parent = p.id) as counter, (SELECT count(*) from confa_bookmarks b where b.post=p.id) as bookmarks, (SELECT count(*) from confa_likes l where l.post=p.id and reaction is not null) as reactions from confa_posts p, confa_posts b, confa_users u where p.parent=b.id and b.author=' . $user_id . ' and p.author=u.id and p.status != 2 order by id desc limit ' . $how_many;
  } else {
    $query = 'SELECT b.id as my_id, b.author as me_author, u.username, u.moder, u.ban_ends, u.id as user_id, u.moder, p.closed as post_closed, p.level, p.page, CONVERT_TZ(p.modified, \'' . $server_tz . '\', \'' . $prop_tz . ':00\')  as modified, p.parent, p.auth, p.views, p.content_flags, p.likes, p.dislikes, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as created, p.subject, p.author, p.status, p.id as id, p.id as msg_id, p.chars, s.last_answered_time, (select count(*) from confa_posts where parent = p.id) as counter, (SELECT count(*) from confa_bookmarks b where b.post=p.id) as bookmarks, (SELECT count(*) from confa_likes l where l.post=p.id and reaction is not null) as reactions from confa_posts p, confa_posts b, confa_users u, confa_sessions s where s.hash=\'' . $auth_cookie .'\' and s.last_answered_time < p.created and p.parent=b.id and b.author=' . $user_id . ' and p.author=u.id and p.id > ' . $last_answered_id . ' and p.status != 2 order by id desc limit 100';
  }
  $result = mysql_query($query);
  if (!$result) {
      mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
      return false;
  } 

  if ($update_ts) {
    $query = 'UPDATE confa_sessions set last_answered_time=current_timestamp where user_id = ' . $user_id;
    $result2 = mysql_query($query);
    if (!$result2) {
        mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        return false;
    }
  }
  
  return $result;
}

function smileys($fieldId=null) {
  global $host, $root_dir;
  
  $out = "";
  
  if ($handle = opendir('images/smiles')) {

    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            $name = explode(".", $entry)[0];
            $out .= '<img src="http://'.$host.$root_dir.'images/smiles/'.$entry.'" title="'.$name.'" alt="'.$name.'"'.
              (is_null($fieldId) ? '' : (' onclick="javascript:insertSmiley(\''.$fieldId.'\',\''.$name.'\');"')).
            '/> ';
        }
    }
    
    closedir($handle);
  }
  return $out;
}

function isonline($host) {
  if($socket =@ fsockopen($host, 80, $errno, $errstr, 0.5)) {
    fclose($socket);
    return true;
  } else {
    return false;
  }  
}

function add_postimage() {
  if (isonline('mod.postimage.org')) {
    return '<script type="text/javascript" src="http://mod.postimage.org/website-english-hotlink-family.js" charset="utf-8"></script>';
  } else {
    return '<!-- postimage server is down -->';
  }
}

function get_tz_offset($tz_name) {
  $theTime = time(); // specific date/time we're checking, in epoch seconds.

  $tz = new DateTimeZone($tz_name); // e.g. 'America/Los_Angeles'
  $transition = $tz->getTransitions($theTime, $theTime);

  // only one array should be returned into $transition. Now get the data: 
  $offset = $transition[0]['offset']; 
  // $abbr = $transition[0]['abbr'];

  return $offset / 3600;  
}

function get_tz_list() {
    $all = timezone_identifiers_list();

    $i = 0;
    foreach($all AS $zone) {
      $zone = explode('/',$zone);
      $zonen[$i]['continent'] = isset($zone[0]) ? $zone[0] : '';
      $zonen[$i]['city'] = isset($zone[1]) ? $zone[1] : '';
      $zonen[$i]['subcity'] = isset($zone[2]) ? $zone[2] : '';
      $i++;
    }

    asort($zonen);
    $result = array();
    
    foreach($zonen AS $zone) {
      extract($zone);
      if($continent == 'America' || $continent == 'Asia' || /* $continent == 'Australia' || $continent == 'Pacific' */ $continent == 'Europe') {
        $key = $continent.'/'.$city;
        if (!array_key_exists($key, $result) ) {
          try {
            $offset = get_tz_offset($key);
            $result[$key] = array('name' => str_replace('_',' ',$city), 'offset' => $offset);
          } catch (Exception $e) {          
          }
        }
      }
    }
    
    uasort($result, function ($a, $b) {
      if ($a['offset'] == $b['offset']) {
          return strcmp($a['name'], $b['name']);
      }
      return ($a['offset'] < $b['offset']) ? -1 : 1;
    });
    
    return $result;
}
?>