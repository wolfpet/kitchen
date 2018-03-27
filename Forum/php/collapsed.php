<?php
/*$Id: collapsed.php 812 2012-10-15 23:22:28Z dmitriy $*/

require_once('head_inc.php');
require_once('html_head_inc.php');
require_once('custom_colors_inc.php');

    $cur_page = $page_collapsed;

    $max_thread_id = 1;
    $max_page = get_max_pages_collapsed($max_thread_id);
    $min_page = 1;

?>
<script src="js/jquery-1.10.2.min.js"></script>
<script language="JavaScript" src="<?=autoversion('js/autoload.js')?>"></script>
<script language="JavaScript" src="<?=autoversion('js/junk.js')?>"></script>
<script language="JavaScript" src="js/threads_autoload.js"></script>
<base target="contents">
</head>
<body id="threads_body">
<!--<table width="95%"><tr>
<td>-->
<!--</td>

</td></tr></table> -->
<?php

//require('menu_inc.php');"

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

    print('<div id="threads">');
    
    $content = array();
    $last_thread = -1;
    $msgs = print_threads_ex($result, $content, $last_thread, $limit);
    
    //display the threads if set to display to anonymous viewers and
    if($show_content)
    {
        print_msgs($content, $msgs);
    }
    else
    {
        if(!is_null($user_id) && $user_id != null){print_msgs($content, $msgs);}
        else {print('Please <a target="bottom" href="new_user.php">register</a> or <a style="cursor: pointer; color: blue"  onclick="top.openLoginForm();">login</a> for full experience.');die();}
    }
                                                  
    //print_msgs($content, $msgs);
    print('</div>');  

    print_pages($max_page, $page, 'contents', $cur_page);
    print('<BR><a href="#up" target="contents">Up</a>');
    print('&nbsp;&nbsp;<a href="javascript:load_threads(document.getElementById(\'threads\'), '.$last_thread.',\'yes\');" target="contents">More</a>');
    
    autoload_threads($last_thread, "yes");
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

