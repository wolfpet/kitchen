<?php
/*$Id: collapsed.php 812 2012-10-15 23:22:28Z dmitriy $*/

require_once('head_inc.php');
require_once('html_head_inc.php');

    $cur_page = $page_collapsed;

    $max_thread_id = 1;
    $max_page = get_max_pages_collapsed($max_thread_id);
    $min_page = 1;

?>
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
<base target="contents">
</head>
<body id="body">
<!--<table width="95%"><tr>
<td>-->
<!--</td>

</td></tr></table> -->
<?php

require('menu_inc.php');

    $show_hidden = 2;
    $ignored = array();

    get_show_hidden_and_ignored();

    print('<a id="up" name="up"></a>');
    print_pages($max_page, $page, 'contents', $cur_page);

    $max_thread_id = $max_thread_id - 50 * ($page - 1);
    $min_thread_id = $max_thread_id - 50;
    if ($max_thread_id < 2) {
        die('No such page');
    }
    if ($min_thread_id < 1) {
        $min_thread_id = 1;
    }
  
    $limit = 1000;
    $min_thread = $max_thread_id - $limit;
    $result = get_thread_starts($min_thread_id, $max_thread_id);

    print("<P>");
    print('<div id="threads">');
    
    $content = array();
    $last_thread = -1;
    $msgs = print_threads_ex($result, $content, $last_thread, $limit);
    print_msgs($content, $msgs);
    print('</div>');  

    print_pages($max_page, $page, 'contents', $cur_page);
    print('<BR><a href="#up" target="contents">Up</a>');
    print('&nbsp;&nbsp;<a href="#up" target="contents" onclick="javascript:load_threads(document.getElementById(\'threads\'), '.$last_thread.',\'yes\'); return false;">More</a>');
?>
<script language="JavaScript">
function load_more() {
	var div = document.getElementById("threads");
	var contentHeight = document.getElementById("body").offsetHeight;
	var yOffset = window.pageYOffset; 
	var y = yOffset + window.innerHeight;
	if ( y >= contentHeight - 300) {
		load_threads(div, <?php print($last_thread);?>, "yes");
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

