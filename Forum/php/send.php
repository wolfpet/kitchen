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
<script type="text/javascript" src="http://mod.postimage.org/website-english-hotlink-family.js" charset="utf-8"></script>
<base target="bottom">
</head>
<body style="background-color: #CCEEEE;" onload="javascript:var field = document.getElementById('<?=is_null($pm_id) ? "to" : "subj"?>'); addEvent(field,'focus',function(){ this.selectionStart = this.selectionEnd = this.value.length;}); field.focus();">
<?php 
    if (is_null($re) || strlen($re)== 0) {
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
      
    	$query = 'select subject from confa_pm where id=' . $pm_id;
    	$result = mysql_query($query);
    	if (!$result) {
        	mysql_log(__FILE__, ' query failed ' . mysql_error() . ' QUERY: ' . $query);
        	die('Query failed');
    	}
    	$row = mysql_fetch_row($result);
        if (!is_null($row[0]) && strncasecmp($row[0], 're: ', 4)) {
    	  $subj = 'Re: ' . $row[0];
        } else {
          $subj = $row[0];
        }
    }

require('send_inc.php'); 
require_once('tail_inc.php');

?>

