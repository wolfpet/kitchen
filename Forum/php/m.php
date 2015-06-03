
<?php
/*$Id: top.php 378 2009-11-02 19:36:24Z dmitriy $*/
#require_once('head_inc.php');
require_once('html_head_inc.php');
    $cur_page = "m.php";
    $max_page = get_max_pages_expanded();
    $work_page = ($max_page - $page) + 1;
$pix=0;
$css = 'disc2.css';
if (!is_null($user_id) && $user_id != null) {
  $query = "SELECT css from confa_users where id = " . $user_id;
  $result = mysql_query($query);
  if ($result) {
    $row = mysql_fetch_assoc($result);
    $css = $row['css'];
  } 
}

?><title>Outline</title>
<script src="js/ajax.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="css/<?php print($css); ?>"> 
<style type="text/css">
.show_message{ background-color:#f0f0f0; border:#CCC 2px solid; position:absolute; width:320px; padding:4px; display:none; font-size:10pt;}
</style><meta name="viewport" content="width=240, height=320, user-scalable=yes, initial-scale=1.0, maximum-scale=5.0, minimum-scale=1.0">
</head> 
<body><form method="post" action="" id="form1" style="display:inline;">
<?php
#require('menu_inc.php');
    print_pages($max_page, $page, '_self', $cur_page);

    $result = get_threads();
    $content = array();
    $msgs = m_print_threads($result, $content);
    m_print_msgs($content, $msgs, $pix);

    print_pages($max_page, $page, '_self', $cur_page);

?></form></body></html><?
require_once('tail_inc.php');
?><script language="javascript">
var last_id="";
function show_message(mess_id){
	if(last_id!="")document.getElementById("div_mes_"+last_id).style.display="none";
	//alert(mess_id);
	if(document.getElementById("div_mes_"+mess_id).innerHTML==""){makeRequest("m_get_mess.php?mess_id="+mess_id+"&tttt="+tttt(), show_message1, mess_id);}
	else{ document.getElementById("div_mes_"+mess_id).style.display="block";}
	last_id=mess_id;
	}
function show_message1(content, mess_id){ //alert(content);
	//alert('='+content+"=");
	if(content!=""){document.getElementById("div_mes_"+mess_id).style.display='block'; document.getElementById("div_mes_"+mess_id).innerHTML=content;}
	}
</script>

