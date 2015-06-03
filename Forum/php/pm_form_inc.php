<?php
/*$Id: pm_form_inc.php 816 2012-10-21 01:19:09Z dmitriy $*/
?>

<h3><?php print($subject); ?></h3>
Author: <b><?php print($author . '</b>' . ' '); 

print($created); ?><br>
<?php print($in_response); ?>


<hr>
<?php 
if (is_null($msgbody) || strlen($msgbody) == 0) {
  print(''); 
} else {
 // print($msgbody); 
  print($trans_body); 
}
?><hr>



