<?php
/*$Id: send.php 988 2014-01-05 01:14:33Z dmitriy $*/

require_once('head_inc.php');
require_once('get_params_inc.php');
    $cur_page = $page_pmail_send;
require_once('html_head_inc.php');
    $title = 'Private message';
    $ticket = '' . ip2long(substr($ip, 1, strlen($ip) - 2)) . '-' . time();
$thread_owner = false;

//only add postimage script if specified in settings
if($imageGallery == 'postimage')
{
    print(add_postimage());
        }
?>
<script language="javascript">
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
function initExpand() 
{
  if (parent.expanded && parent.expanded()) 
  {
    document.getElementById("expandMsg").style.display='none';
    document.getElementById("restoreMsg").style.display='block';    
  }
  else
  {
    document.getElementById("expandMsg").style.display='block';
    document.getElementById("restoreMsg").style.display='none';
  }
}
function loadimage(img)
{
 setTimeout(function()
 {
  img.style.opacity= 1;
  var downloadingImage = new Image();
  downloadingImage.onload = function(){
      img.src = this.src;
      };
      downloadingImage.src = img.alt;
  //img.src = img.alt;
 }
 , 500);
}
</script>
<base target="bottom">
</head>
<body style="background-color: #CCEEEE;" onload="javascript: initExpand(); var field = document.getElementById('<?=is_null($pm_id) ? "to" : "subj"?>'); addEvent(field,'focus',function(){ this.selectionStart = this.selectionEnd = this.value.length;}); field.focus();">
  <div id="expandMsg" onclick="toggleExpand();parent.expand();" style="float: right;position: relative;width: 0px;top: -20px;right: -5px;cursor: pointer;">
    <svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="grey" d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"></path></g></svg>
  </div>
  <div id="restoreMsg" onclick="toggleExpand();parent.restore();" style="display: none; float: right;position: relative;width: 0px;top: -20px;right: -5px;cursor: pointer;">
    <svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="red" d="M5 16h3v3h2v-5H5v2zm3-8H5v2h5V5H8v3zm6 11h2v-3h3v-2h-5v5zm2-11V5h-2v5h5V8h-3z"></path></g></svg>
  </div>
<?php 
    if ((is_null($re) || strlen($re)== 0) && (is_null($pm_id) || strlen($pm_id)== 0)) {
?>
<table width="95%"><tr>
<td>
<h3 onclick="toggleExpand();" style="cursor: pointer" id="subject"><?php print($title);?></h3>
</td>

</tr></table>

<?php
    }    
    if (!is_null($re) && strlen($re) > 0) {
        $msg_id = $re;
require("msg_inc.php");
        if (strncasecmp($subj, 're: ', 4)) {
             $subj = 'Re: ' . $subj;
        }
    } else if (!is_null($pm_id) && strlen($pm_id) > 0) {
        $msg_id = $pm_id;
require("pm_msg_inc.php");
      if (strncasecmp($subj, 're: ', 4)) {
             $subj = 'Re: ' . $subj;
        }        
    } 
    if (isset($quote) && strlen($quote) > 0) {
      $body = "[quote]".$quote."[/quote]";
    }

require('send_inc.php'); 
require_once('tail_inc.php');

?>

