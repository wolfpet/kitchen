<?php
/*$Id: top.php 378 2009-11-02 19:36:24Z dmitriy $*/
require_once('head_inc.php');
    // do orientation
    if ( isset($_GET) && is_array($_GET) && count($_GET) > 0 ) {
        if (array_key_exists('dummy', $_GET)) {
            $dummy = intval(trim($_GET['dummy']));
            if ($dummy) {
              $orientation = "rows";
              setcookie('orientation', '', time() - 100000, $root_dir, $host, false, true);              
            } else {
              $orientation = "cols";
              setcookie('orientation', $orientation, 1800000000, $root_dir, $host, false, true);              
            }
        }
    }    
require_once('html_head_inc.php');
require_once('mysql_log.php');

    $cur_page = $page_expanded;
    $max_page = get_max_pages_expanded();
    $work_page = ($max_page - $page) + 1;
    $start_timestamp = time();
?>
<script language="JavaScript" src="<?=autoversion('js/junk.js')?>"></script>
<script language="JavaScript" src="<?=autoversion('js/autoload.js')?>"></script>
<base target="bottom">
</head>
<body id="html_body">
<?php
function _pages_function($add_fluff=false) {
    global $max_page, $page, $cur_page;
    print('<a id="up" name="up"></a>');
    print_pages($max_page, $page, 'contents', $cur_page, '', $add_fluff, $add_fluff);
    // print_pages(5, $page, 'contents', $cur_page, '', $add_fluff, $add_fluff);
}
require('menu_inc.php');

    $show_hidden = 2;
    $ignored = array();

    get_show_hidden_and_ignored();

    _pages_function(false);  // to move to top, rename to "pages_function" and comment this call out
    
    print("<p/>");
    
    print('<div id="threads">');

    $limit = strpos($agent, 'iPad') ? 7 : (strpos($agent, 'iPhone') || strpos($agent, 'like Mac OS') ? 5 : 200); 
    
    $result = get_threads_ex($limit);
    $content = array();
    $last_thread = -1;
    $msgs = print_threads_ex($result, $content, $last_thread, $limit);
    print_msgs($content, $msgs);

    print('</div>');  
    
    // print_pages($max_page, $page, 'contents', $cur_page);
    print('<BR><a href="#up" target="contents">Up</a>');
    print('&nbsp;&nbsp;<a href="javascript:load_threads(document.getElementById(\'threads\'), '.$last_thread.','.$limit.');" target="contents">More</a>');

    $end_timestamp = time();
    $duration = $end_timestamp - $start_timestamp;
    print('<!-- ' . $duration . ' milliseconds -->'); 

   // print($show_hidden . "|");
   // print_r($ignored);
   autoload_threads($last_thread, $limit);
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

<?php

require_once('tail_inc.php');

?>

