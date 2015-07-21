<?php
/*$Id: msg.php 988 2014-01-05 01:14:33Z dmitriy $*/

require_once('head_inc.php');
require_once('html_head_inc.php');

?><script src="js/jsdiff.js"></script>
<base target="bottom">
</head>
<body>
<?php
require_once('ver_inc.php');
?>
<a href="<?php print($root_dir . $page_msg); ?>?id=<?php print($msg_id); ?>">Back to the message</a> 
<?php
  if ( !is_null( $moder ) && $moder > 0 ) {
      print( '&nbsp;&nbsp;&nbsp;<SPAN STYLE="background-color: #FFE0E0">[ ' );
      if ( $status == 3 ) {
          print( '<a href="' . $root_dir . 'modcensor.php' . '?action=uncensor&id=' . $msg_id . '"><font color="green">Uncensor version</font></A> |' );
      } else {
          print( '<a href="' . $root_dir . 'modcensor.php' . '?action=censor&id=' . $msg_id . '"><font color="green">Censor version</font></A> |' );
      }
      if ( $status == 2 ) {
          print( ' <a href="' . $root_dir . 'modcensor.php' . '?action=undelete&id=' . $msg_id . '"><font color="green">Undelete version</font></A> |' );
      } else {
          print( ' <a href="' . $root_dir . 'modcensor.php' . '?action=delete&id=' . $msg_id . '"><font color="green">Delete version</font></A> |' );
      }
      print( ']</SPAN>' );
  }
print("<br/>");
require_once('msg_hist_inc.php');    
require_once('tail_inc.php');
?>
</body>
</html>


