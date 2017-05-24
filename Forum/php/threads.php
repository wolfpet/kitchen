<?php
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

    $cur_page = 'threads.php';
    $max_page = get_max_pages_expanded();
    $work_page = ($max_page - $page) + 1;
    $start_timestamp = time();
?>
<script src="js/jquery-1.10.2.min.js"></script>
<script language="JavaScript" src="<?=autoversion('js/junk.js')?>"></script>
<script language="JavaScript" src="<?=autoversion('js/autoload.js')?>"></script>
<!-- <script language="JavaScript" src="js/threads_autoload.js"></script> -->
<script>
//"
var selected_id = "";
var previewInProgress=false; 

function selectMsg(id) {
  id = "sp_" + id;
  //console.log('selectMsg  id=' + id);
  if (selected_id != "") {
  // reset selection
  //console.log('resetting selected id=' + selected_id);
  var selected = document.getElementById(selected_id);
  if (selected != null) {
  selected.className = null;
  }
 }
 var selected = document.getElementById(id);
 if (selected != null) {
  //console.log('selected element id=' + id);
  // select message
  selected.className = "selected";
  selected_id = id;
 } else {
    // message not found
    selected_id = "";
    //console.log('id=' + id + "not found");
 }
}

function previewMsg(msgId)
{
 if(parent.bottom.document.getElementById("postPreview")==null)return; //nowhere to preview
 parent.bottom.document.getElementById("postPreview").innerHTML='';
 parent.bottom.document.getElementById("postPreview").style.display = "block";
 if(previewInProgress)return;//something else is being queried. Don't compete.
 //retreive from api/messages/xxxxxx
 previewInProgress=true;
 total_count=0;
 $.ajax({
         type: "GET",
         url: "./api/messages/"+ msgId,
         success: function(data)
       {
        renderPreview(data);
       }
    });
}

function renderPreview(data)
{

    var html  = data.body.html;
    parent.bottom.document.getElementById("postPreview").innerHTML= html;
    previewInProgress=false;
}
function clearPreview()
{
  if(parent.bottom.document.getElementById("postPreview")==null)return; //nowhere to preview
  parent.bottom.document.getElementById("postPreview").style.display = "none";
}

</script>
<base target="bottom">
<?php

if (!is_null($user_id) && $user_id != null) {

    $colorquery = "SELECT color_ribbon, color_ribbon_background, color_icon_hover, color_group_border, color_topics_unread, color_topics_hover, color_topics_visited, color_titles from confa_users where id = " . $user_id;
    $colorresult = mysql_query($colorquery);
    if ($colorresult) {
	$colorrow = mysql_fetch_assoc($colorresult);
        $ribbonColor=$colorrow['color_ribbon'];
        $ribbonBackground=$colorrow['color_ribbon_background'];  
        $iconHover=$colorrow['color_icon_hover'];
        $groupBorder=$colorrow['color_group_border'];
              
        $color_topics_unread= $colorrow['color_topics_unread'];
        $color_topics_hover=$colorrow['color_topics_hover'];
        $color_topics_visited=$colorrow['color_topics_visited'];
        }
     }
     else{
        $ribbonColor='white';
	$ribbonBackground='#0080c0';  
        $iconHover='#0090c0';
        $groupBorder='#0090c0';
                                   
        $color_topics_unread= '#0000FF';
        $color_topics_hover= '#FF0000';
        $color_topics_visited='#0080c0';
         }
                                         
?>
                                         
<style>
     a:link    {color:<?=$color_topics_unread?>; font-family:Verdana,Arial; text-decoration:none;}
     a:visited {color:<?=$color_topics_visited?>; font-family:Verdana,Arial; text-decoration:none;}
     a:active  {color:<?=$color_topics_hover?>; font-family:Verdana,Arial; text-decoration:none;}
     a:hover   {color:<?=$color_topics_hover?>; font-family:Verdana,Arial; text-decoration:underline;}
     a.user_link {color:black;}
     .selected  {background:#E0F1FF; /* Yellow = #FFFF99 */}
     h1<---->{font-family:Verdana,Arial; font-size:36pt;}
     h2<--->{font-family:Verdana,Arial; font-size:24pt; color:#0080c0;}
     h3<--->{font-family:Verdana,Arial; font-size:14pt; color:#0080c0;}
     /* Pager */
     .pagination li.active span {
         background-color: <?=$ribbonBackground?>;
         border-color: <?=$ribbonBackground?> ;
         color: <?=$ribbonColor?>;
     }
     .pagination li a:hover, .pagination .dropdown-visible a.dropdown-trigger, .nojs .pagination .dropdown-container:hover a.dropdown-trigger {
         background-color: <?=$iconHover?>;
         border-color: <?=$ribbonBackground?>;
         color:  <?=$ribbonColor?>;
     }
</style>

</head>
<body id="threads_body">
<?php
function _pages_function($add_fluff=false) {
    global $max_page, $page, $cur_page;
    print('<a id="up" name="up"></a>');
    print_pages($max_page, $page, 'contents', $cur_page, '', $add_fluff, $add_fluff);
    // print_pages(5, $page, 'contents', $cur_page, '', $add_fluff, $add_fluff);
}

    $show_hidden = 2;
    $ignored = array();

    get_show_hidden_and_ignored();

    _pages_function(false);  // to move to top, rename to "pages_function" and comment this call out
    
    print('<div id="threads">');

    $limit = strpos($agent, 'iPad') ? 7 : (strpos($agent, 'iPhone') || strpos($agent, 'like Mac OS') ? 5 : 200); 
    
    $result = get_threads_ex($limit);
    $content = array();
    $last_thread = -1;
    $msgs = print_threads_ex($result, $content, $last_thread, $limit);
    print_msgs($content, $msgs);

    print('</div>');  
    
    // print_pages($max_page, $page, 'contents', $cur_page);
    print('<BR><a href="#up" target="_top">Up</a>');
    print('&nbsp;&nbsp;<a href="javascript:load_threads(document.getElementById(\'threads\'), '.$last_thread.','.$limit.');" target="_top">More</a>');

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

