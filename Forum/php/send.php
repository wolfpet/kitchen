<?php
/*$Id: send.php 988 2014-01-05 01:14:33Z dmitriy $*/

require_once('head_inc.php');
require_once('get_params_inc.php');
    $cur_page = $page_pmail_send;
require_once('html_head_inc.php');
    $title = 'Private message';
    $ticket = '' . ip2long(substr($ip, 1, strlen($ip) - 2)) . '-' . time();
$thread_owner = false;
?>
<?=add_postimage()?>
<base target="bottom">
</head>
<body style="background-color: #CCEEEE;" onload="javascript:var field = document.getElementById('<?=is_null($pm_id) ? "to" : "subj"?>'); addEvent(field,'focus',function(){ this.selectionStart = this.selectionEnd = this.value.length;}); field.focus();">
<?php 
    if ((is_null($re) || strlen($re)== 0) && (is_null($pm_id) || strlen($pm_id)== 0)) {
?>
<table width="95%"><tr>
<td>
<h3><?php print($title);?></h3>
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

