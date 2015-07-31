<?php
/*$Id: pm_msg.php 825 2012-11-03 00:55:01Z dmitriy $*/

require_once('head_inc.php');
require_once('get_params_inc.php');
require_once('html_head_inc.php');

?>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.6/styles/default.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.6/highlight.min.js"></script>
<base target="bottom">
</head>
<body style="background-color: #CCEEEE;">

<?php

require("pm_msg_inc.php");

?>

<!--<a href="">Reply</a> |-->

<a href="<?php print($root_dir . $page_pmail_send . '?to=' . $author . '&pm_id=' . $msg_id); ?>">Reply to sender (private)</a> |

</body>
</html>
<?php

require_once('tail_inc.php');

?>

