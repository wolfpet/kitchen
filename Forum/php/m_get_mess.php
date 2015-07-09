<?php
require_once('head_inc.php');
require_once('func.php');

$mess_id=empty($_REQUEST['mess_id'])?"":str_replace("'", "", $_REQUEST['mess_id']);
if(!empty($mess_id)){
	$sSQL="SELECT body, status FROM confa_posts WHERE id='$mess_id'";
	$result2=mysql_query($sSQL) or die ("MySQL err: " . mysql_error());
	if($row2 = mysql_fetch_array($result2)){ ?>
		<a id="reply" class="mbutton" href="#" onclick="this.parentElement.onclick=null;reply_message('<?=$mess_id?>');return false;"
      >Reply</a><a id="close" class="mbutton" href="#" onclick="document.getElementById('div_mes_<?= $mess_id ?>').style.display='none'; return false;">Close</a><div style='clear:both;'></div><?php

		if ( $row2['status'] == 3 ) { print( '<font color="red">Censor (Мат)</font>' ); }
        elseif ( $row2['status'] == 4 ) {print( '<font color="red">Censor (Хамство)</font>' ); }
        elseif ( $row2['status'] == 5 ) {print( '<font color="red">Censor (Наезд)</font>' ); }
        elseif ( $row2['status'] == 2 ) {print( '<font color="red">Delete message</font>' );}
		else{
			$translit_done = false;
			$s_mess=trim($row2['body']);
			$s_mess=str_replace($n_ff, "", $s_mess);
			$s_mess = translit($s_mess, $translit_done);
			if (!is_null($s_mess) && strlen($s_mess) > 0 && !is_null($prefix) && strlen($prefix) > 0){
				$s_mess = $prefix . ' ' . str_replace("\n", "\n" . $prefix . ' ', $s_mess);
			}
			$s_mess = htmlentities($s_mess, HTML_ENTITIES,'UTF-8');

      $s_mess = before_bbcode($s_mess);
      $s_mess = do_bbcode ( $s_mess );
      $s_mess = nl2br($s_mess);
      $s_mess = after_bbcode($s_mess);

			echo trim($s_mess);
			}	
		}
    $query = 'UPDATE confa_posts SET views=views + 1 where id=' . $mess_id;
    $result = mysql_query($query);
	}
require_once('tail_inc.php');  
?>

