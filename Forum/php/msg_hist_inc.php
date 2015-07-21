<script language="Javascript">
function revisions_on() {
  if (document.getElementById('revisions').style.display != 'block') {
    document.getElementById('revisions').style.display='block';
  } else {
    document.getElementById('revisions').style.display='none';
  }
}
</script>
<?php
/*$Id: msg_inc.php 988 2014-01-05 01:14:33Z dmitriy $*/

require_once('head_inc.php');

if (/*is_null($user_id) || !in_array($user_id, $ignored)*/ true) {
  $query = 'SELECT p.id, p.subject, p.views, p.status, p.parent, CONVERT_TZ(p.created, \'' . $server_tz . '\', \'' 
    . $prop_tz . ':00\') as created, p.body, p.content_flags, p.chars from confa_versions p where p.parent=' . $msg_id . ' order by p.created desc';
      
  $result = mysql_query($query);
  if (!$result) {
    mysql_log( __FILE__, 'query 1 failed ' . mysql_error() . ' QUERY: ' . $query);
    die('Query failed');
  }
  
  $page_version = "ver.php";
  
  if (mysql_num_rows($result) != 0) {
    print('<br/><a class="revisions_link" href="javascript:revisions_on();"><font color="gray">Revision history</font></a><div class="revisions" id="revisions" '
      . (isset($version) ? 'style="display:block;"' : '') .'><br/>');
  
    while($row = mysql_fetch_assoc($result)) {
      if ($row['status'] == 2 && (is_null( $moder ) || $moder == 0 || strcmp( $mode, "del" ))) {
        $line = '<I><font color="gray"><del>This version has been deleted</del></font></I> ';
      } else {
        $icons = '';
        $nsfw = '';
        if ($row['content_flags'] & 0x02) {
          $icons = ' <img border=0 src="' . $root_dir . $image_img . '"/> ';
        }
        if ($row['content_flags'] & 0x04) {
          $icons .= ' <img border=0 src="' . $root_dir . $youtube_img . '"/> ';
        }
        if ($row['content_flags'] & $content_nsfw) {
          $nsfw .= '<span class="nsfw">NSFW</span>';
        }
      
        $subj = print_subject(encode_subject($row['subject']));
        $line = $icons .'<a id="' . $msg_id.'_'.$row['id'] . '" name="' . $msg_id.'_'.$row['id'] . '" target="bottom" href="' . $root_dir . $page_version . '?id=' . $msg_id . '&ver='.$row['id'].'">' . $subj . '</a> '.$nsfw.' '.
          ' [' . $row['views'] . ' views] ' . $row['created'] . ' <b>' . $row['chars'] . '</b> bytes'; 
      }
      if (isset($version) && $version == $row['id']) 
        print('<font color="green">&gt;</font>');
      else
        print('<span style="opacity:0;">&gt;</span>');
      print('<i><font color="gray">' . $line . '</font></i>');
      print('<br/>');
    }
    print("</div>");
  }
  mysql_free_result($result);
}
?>



