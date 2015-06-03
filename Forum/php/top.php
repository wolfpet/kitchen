<?php
/*$Id: top.php 378 2009-11-02 19:36:24Z dmitriy $*/

require_once('head_inc.php');
require_once('html_head_inc.php');
require_once('mysql_log.php');

    $cur_page = $page_expanded;
    $max_page = get_max_pages_expanded();
    $work_page = ($max_page - $page) + 1;
    $start_timestamp = time();
?>
<script language="JavaScript" src="js/junk.js"></script>
<script language="JavaScript">
var max_id = "";
var loading = false;

function load_threads(div, id, count) {
  if (loading) return;
  loading = true;
  
  var indicator = document.getElementById('loading');
  if (indicator != null) 
    indicator.style.display = "block";
  
  if (max_id.length == 0) {
    max_id = "" + id;  
    console.log("max id=" + max_id);
  }
  
  // Initialize the Ajax request.
  var xhr = new XMLHttpRequest();
  var url = 'get_threads.php?id=' + max_id + "&custom=" + count;
  console.log("loading " + url);

  xhr.open('get', url);
   
  // Track the state changes of the request.
  xhr.onreadystatechange = function () {
    var DONE = 4; // readyState 4 means the request is done.
    var OK = 200; // status 200 is a successful return.
    if (xhr.readyState === DONE) {        
      try {
        var indicator = document.getElementById('loading');
        if (indicator != null) indicator.style.display = "none";
        if (xhr.status === OK) {
            // alert(xhr.responseText); // 'This is the returned text.'
            var text = xhr.responseText;
            var id = text.indexOf("id=");
            var start = text.indexOf("<dl");
            if (start >= 0) {
              max_id = text.substring(id + 3, start);
              console.log("new max id=" + max_id);
              div.innerHTML = div.innerHTML + " " + text.substring(start);
            }
        } else {
            // alert('Error: ' + xhr.status); // An error occurred during the request.
        }
      } finally {
        loading = false;
      }
    }
  };
   
  xhr.send(null);
}

function scroll2Top(element){ 
  var ele = document.getElementById(element); 
  setTimeout(window.scrollTo(ele.offsetLeft,ele.offsetTop), 100);
}

</script>
<base target="bottom">
</head>
<body id="body"> 
<!--
<table width="100%"><tr><td width="40%"><H4></H4></td>
<td width="60%" align="right">
<a href="http://info.flagcounter.com/6tbt"><img src="http://s01.flagcounter.com/count/6tbt/bg_FFFFFF/txt_000000/border_CCCCCC/columns_8/maxflags_16/viewers_3/labels_0/pageviews_0/flags_0/" alt="Flag Counter" border="0"></a>
</td></tr></table>
-->
<!--<table width="95%"><tr>
<td>-->
<!--<h3><?php print($title);?></h3>-->
<!--</td>

</tr></table>-->
<?php
// $page_expanded = 'top2.php'; // NG: remove when renamed

require('menu_inc.php');

    $show_hidden = 2;
    $ignored = array();

    get_show_hidden_and_ignored();

    print('<a id="up" name="up"></a>');
    print_pages($max_page, $page, 'contents', $cur_page);
    print("<p/>");
    
    print('<div id="threads">');

    $limit = 200;
    $result = get_threads_ex($limit);
    $content = array();
    $last_thread = -1;
    $msgs = print_threads_ex($result, $content, $last_thread, $limit);
    print_msgs($content, $msgs);

    print('</div>');  
    
    print_pages($max_page, $page, 'contents', $cur_page);
    print('<BR><a href="#up" target="contents">Up</a>');
    print('&nbsp;&nbsp;<a href="#up" target="contents" onclick="javascript:load_threads(document.getElementById(\'threads\'), '.$last_thread.','.$limit.'); return false;">More</a>');

    $end_timestamp = time();
    $duration = $end_timestamp - $start_timestamp;
    print('<!-- ' . $duration . ' milliseconds -->'); 

   // print($show_hidden . "|");
   // print_r($ignored);
?>
<script language="JavaScript">
function load_more() {
	var div = document.getElementById("threads");
	var contentHeight = document.getElementById("body").offsetHeight;
	var yOffset = window.pageYOffset; 
	var y = yOffset + window.innerHeight;
	if ( y >= contentHeight - 300) {
		load_threads(div, <?php print($last_thread);?>, <?php print($limit);?>);
	}
}

window.onscroll = load_more;
</script>
<div id="loading" style="color:gray;position:fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;text-align: right;display:none">Loading...&nbsp;</div>
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

<?php

require_once('tail_inc.php');

?>

