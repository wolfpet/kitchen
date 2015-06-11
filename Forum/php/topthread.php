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

    function print_msgs2($ar, $msgs) {
        $keys = array_keys($ar);
        print("<dl style='position:relative; left:-20px'><dd>");
        foreach ($keys as $key) {
            print($msgs[$key]);
            print("<BR>");
            if (sizeof($ar[$key]) > 0) {
                print_msgs2($ar[$key], $msgs);
            }
        }
        print("</dd></dl>");
    }

    $result = get_thread($thread_id);
    $content = array();
    $msgs = print_thread($result, $content);
    
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

   print("<p/>");
   print_msgs2($content, $msgs);

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

