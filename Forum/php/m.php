
<?php
/*$Id: top.php 378 2009-11-02 19:36:24Z dmitriy $*/
#require_once('head_inc.php');
require_once('html_head_inc.php');
    $cur_page = "m.php";
    $max_page = get_max_pages_expanded();
    $work_page = ($max_page - $page) + 1;
$pix=0;
$css = 'disc2.css';
if (!is_null($user_id) && $user_id != null && false) {
  $query = "SELECT css from confa_users where id = " . $user_id;
  $result = mysql_query($query);
  if ($result) {
    $row = mysql_fetch_assoc($result);
    $css = $row['css'];
  } 
}
?><title>Кирдык</title>
<script src="js/func.js?<?=filemtime('js/func.js')?>" type="text/javascript"></script>
<script src="js/translit.js?<?=filemtime('js/translit.js')?>" type="text/javascript"></script>
<script src="js/ajax.js?<?=filemtime('js/ajax.js')?>" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="css/m.css?<?=filemtime('css/m.css')?>">
<meta name="viewport" content="width=240, height=320, user-scalable=yes, initial-scale=1.0, maximum-scale=5.0, minimum-scale=1.0">
</head> 
<body><a target="_top" href="/" title="Full version"><img src="images/ukrainian-flag2.png" alt="Кирдык"/></a><!--<form method="post" action="" id="form1" style="display:inline;">-->
<?php
#require('menu_inc.php');
    print_pages($max_page, $page, '_self', $cur_page);
    print("<p/>");
    $result = get_threads();
    $content = array();
    $msgs = m_print_threads($result, $content);
    m_print_msgs($content, $msgs, $pix);

    print_pages($max_page, $page, '_self', $cur_page);

?><!--</form>--></body></html><?
require_once('tail_inc.php');
?><script language="javascript">
var last_id="";

function show_message(mess_id){
  
	if (last_id != "") { 
    document.getElementById("div_mes_" + last_id).style.display = "none"; 
  }
  
  var div = document.getElementById("div_mes_" + mess_id);
  console.log("show_message=" + mess_id);
  if (div.innerHTML == "") {
    makeRequest("m_get_mess.php?mess_id=" + mess_id + "&tttt=" + tttt(), show_message1, mess_id);
  } else {
    discard_message(mess_id);
    div.style.display="block";
  }
  last_id = mess_id;
}
  
function show_message1(content, mess_id){ //alert(content);
  //console.log(content);
	if (content != "") { 
    var div = document.getElementById("div_mes_" + mess_id);
    div.style.display='block'; 
    div.innerHTML = '<div id="div_mes_' + mess_id +'_content">' + content + '</div>'; 
  }
}

function reply_message(mess_id) {
  var reply = document.getElementById("div_mes_" + last_id + "_reply");
	if (reply == null) {
    makeRequest('<?=$root_dir."m_new.php"?>?re=' + mess_id, reply_message1, mess_id);
  } else {
    var content = document.getElementById("div_mes_" + last_id + "_content");
    if (content != null) content.style.display = "none";    
    reply.style.display='block'; 
  }
}

var old_onclick = null;

function reply_message1(text, mess_id){ //alert(content);
	if (text != "") { 
    console.log("reply to msg=" + mess_id);
    // console.log(text);
    var content = document.getElementById("div_mes_" + last_id + "_content");
    if (content != null) content.style.display = "none";    
    var div = document.getElementById("div_mes_" + mess_id);
    div.innerHTML = div.innerHTML + '<div id="div_mes_' + mess_id +'_reply">' + text + '</div>'; 
    old_onclick = div.onclick;
    div.onclick = null;
    div.style.display='block'; 
  }
}

function discard_message(mess_id) {
  var reply = document.getElementById("div_mes_" + last_id + "_reply");
	if (reply != null) {
    reply.style.display = "none";
    reply.parentNode.removeChild(reply);
  }
  var result = document.getElementById("div_mes_" + mess_id + "_result");
  if (result != null) {
    result.style.display = "none";
    result.parentNode.removeChild(reply);
  } 
  
  var content = document.getElementById("div_mes_" + last_id + "_content");
  if (content != null) content.style.display = "block";    
}

function send_message(btn, mess_id) {
  var error = document.getElementById("div_mes_" + mess_id + "_error");
  if (error != null) { error.style.display = "none"; error.innerHTML = ""; }
  btn.style.pointerEvents = "none";
  
  //var frm = serialize(btn.form);
  var frm = $('#msgform_' + mess_id).serialize();
  //console.log('#msgform_' + mess_id + ":" + frm);
  makePostRequest('<?=$root_dir."m_post.php"?>', sent_message, frm, mess_id);  
}

function sent_message(text, mess_id, status) {
  console.log("sent_message=" + mess_id + ", status=" + status);
  var reply = document.getElementById("div_mes_" + last_id + "_reply");
	if (reply == null) return;
  
  if (status == 400) {    
    // error
    var error = document.getElementById("div_mes_" + mess_id + "_error");
    if (error != null) {
      error.innerHTML = text.trim();
      error.style.display = "block";  
    } else {
      reply.innerHTML = '<div class="error" id="div_mes_' + mess_id + '_error">' + text + '</div>' + reply.innerHTML;
    }
    var sendBtn = document.getElementById("send_" + mess_id);
    if (sendBtn != null) { sendBtn.style.pointerEvents = "auto";}
  } else { 
    // success
    var div = reply.parentNode;
    reply.style.display = "none";
    div.removeChild(reply);
    var result = document.getElementById("div_mes_" + mess_id + "_result");
    if (result != null) {
      result.innerHTML = text;
      result.style.display = "block";
    } else {
      div.innerHTML += '<div class="result" id="div_mes_"' + mess_id + '"_result">' + text + '</div>';
    }
    div.onclick = old_onclick;
  }
}
</script>

