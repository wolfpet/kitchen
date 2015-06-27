<?php
/*$Id: new.php 988 2014-01-05 01:14:33Z dmitriy $*/

require_once('head_inc.php');
require_once('html_head_inc.php');

$thread_owner = false;
    $title = 'New message';
    $ticket = '' . ip2long(substr($ip, 1, strlen($ip) - 2)) . '-' . time();
/*
    $query = 'INSERT into confa_tickets(ticket) values(\'' . $ticket . '\')';
    $result = mysql_query($query);
    if (!$result) {
        mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Failed to get ticket for new post. Posting too fast! Please, try again.');
    }
*/

?>

<base target="bottom">
</head>
<body onload="javascript:var subj = document.getElementById('subj'); addEvent(subj,'focus',function(){ this.selectionStart = this.selectionEnd = this.value.length;}); subj.focus();">

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
    }

require('new_inc.php'); 
require_once('tail_inc.php');

?>

