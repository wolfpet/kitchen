<?php
/*$Id: confirm.php 987 2014-01-01 21:03:31Z dmitriy $*/
require_once('head_inc.php');

    if (isset($_GET) && is_array($_GET)) {
        $msg_id = $_GET['id'];
        $author_name = $_GET['author_name'];
        $page = $_GET['page'];
        $subj = $_GET['subj'];
    }
?>
<html>
<head>
<link REL="STYLESHEET" TYPE="text/css" HREF="<?=autoversion('css/disc2.css')?>">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<script>
function toggleExpand()
{
    if(document.getElementById("expandMsg").style.display=='none')
    {
      //enable expanding
      document.getElementById("expandMsg").style.display='block';
      document.getElementById("restoreMsg").style.display='none';
      parent.restore();
    }
    else
    {
      document.getElementById("expandMsg").style.display='none';
      document.getElementById("restoreMsg").style.display='block';
      parent.expand();	
    }
}

function initExpand() {
  // collapse the message
  document.getElementById("expandMsg").style.display='block';
  document.getElementById("restoreMsg").style.display='none';
  parent.restore();
}
</script>
<title>Thank you, </title></head>
<body onload="initExpand();">
  <div id="expandMsg" onclick="toggleExpand();parent.expand();" style="float: right;position: relative;width: 0px;top: -20px;right: -5px;cursor: pointer;">
    <svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="grey" d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"></path></g></svg>
  </div>
  <div id="restoreMsg" onclick="toggleExpand();parent.restore();" style="display: none; float: right;position: relative;width: 0px;top: -20px;right: -5px;cursor: pointer;">
    <svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="red" d="M5 16h3v3h2v-5H5v2zm3-8H5v2h5V5H8v3zm6 11h2v-3h3v-2h-5v5zm2-11V5h-2v5h5V8h-3z"></path></g></svg>
  </div>
  <h3 onclick="toggleExpand();" style="cursor: pointer" id="subject">Confirmation</h3>
Thank you, <b><?php print(htmlentities($author_name, HTML_ENTITIES,'UTF-8')); ?></b>!<br/><p>
Your article, named "<b><?php print($subj); ?></b>", has been sent to forum.</p><p>

If you <a href="<?php print($root_dir . 'threads.php');?>?page=<?php print($page . '&id=' . $msg_id . '#' . $msg_id); ?>" target="contents">refresh the contents</a>, you should see its name in forum's contents.<p>
</body></html>

</html>


