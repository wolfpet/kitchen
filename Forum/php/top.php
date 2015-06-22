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
<script language="JavaScript" src="<?=autoversion('js/junk.js')?>"></script>
<script language="JavaScript" src="<?=autoversion('js/autoload.js')?>"></script>
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
<!--<h3><?php //print($title);?></h3>-->
<!--</td>

</tr></table>-->
<?php

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

