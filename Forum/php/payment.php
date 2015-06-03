<?php
require_once('dump.php');
require_once('head_inc.php');

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value) {
$value = urlencode(stripslashes($value));
$req .= "&$key=$value";
}

// post back to PayPal system to validate
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);

// assign posted variables to local variables
$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$payment_status = $_POST['payment_status'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$payer_email = $_POST['payer_email'];

$log_name = "log/payments-" .  date("Y-m-d") . ".log";
$fpLog=fopen( $log_name, 'a' );
if (flock($fpLog, LOCK_EX)) {
  fputs($fpLog, "item_name=$item_name&");
  fputs($fpLog, "item_number=$item_number&");
  fputs($fpLog, "payment_status=$payment_status&");
  fputs($fpLog, "mc_gross=$mc_gross&");
  fputs($fpLog, "mc_currency=$mc_currency&");
  fputs($fpLog, "txn_id=$txn_id&");
  fputs($fpLog, "receiver_email=$receiver_email&");
  fputs($fpLog, "payer_email=$payer_email&");
  fputs($fpLog, "custom=$custom&");
  fputs( $fpLog, "pay_type=$pay_type&" );
  fputs( $fpLog, "pay_moder=$pay_moder&" );
  fputs( $fpLog, "pay_victom=$pay_victim&" );

  fputs($fpLog, "============\n");
  flock($fpLog, LOCK_UN);
}

if (!$fp) {
  if (flock($fpLog, LOCK_EX)) {
    fputs($fpLog, "Failed to connect to paypal. errno=$errno\n========");
  }    
} else {
fputs ($fp, $header . $req);
$locked = flock($fpLog, LOCK_EX);
if ($locked) {
  fputs( $fpLog, "PAYPAL_RESP_START=" );
}
while (!feof($fp)) {
$res = fgets ($fp, 1024);
if ( $locked ) {
  fputs( $fpLog, $res ); 
}
if (strcmp ($res, "VERIFIED") == 0) {
// check the payment_status is Completed
// check that txn_id has not been previously processed
// check that receiver_email is your Primary PayPal email
// check that payment_amount/payment_currency are correct
// process payment

// ADDED CODE - START
if (!is_null($custom) && !is_null($pay_type) && !strcmp($pay_type, 'a')) {
    do {
        $query = 'select id, username, moder, ban, ban_ends, date_add((case ban_ends when \'0000-00-00 00:00:00\' then current_timestamp else ban_ends end ), interval 1 day) as new_ban from confa_users where id=' . $pay_victim;
        $result = mysql_query($query);
        if (!$result) {
            mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            if ( $locked ) {
              fputs( $fpLog, 'Query failed\n' );
            }
            break;
        }
        $row = mysql_fetch_assoc($result);
        if ( !is_null( $row)) {
           if( is_null($row['moder']) || $row['moder'] == 0 ) {
                $ban_reason = 'уплочено';
                $query = 'INSERT into confa_ban_history( moder, expires, victim, ban_reason) values( ' . $pay_moder . ', \'' . $row['new_ban'] . '\', ' . $pay_victim . ', \'' . mysql_escape_string( trim( $ban_reason ) ) . '\' )';
                $result = mysql_query($query);
                if (!$result) {
                    mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                    if ( $locked ) {
                        fputs( $fpLog, 'Query failed\n' );
                    }
                    break; 
                }
                $ban_id = mysql_insert_id();
                $query = 'Update confa_users set pban=1, ban_ends=\'' . $row['new_ban'] . '\', ban=' . $ban_id . ' where id=' . $pay_victim;
                $result = mysql_query($query);
                if (!$result) {
                    mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                    if ( $locked ) {
                        fputs( $fpLog, 'Query failed\n' );
                    }
                    break;
                }
           } else {
                if ( $locked ) {
                    fputs( $fpLog, 'Moderator cannot be banned\n' );
                }
                break;
           }
        } else {
            if ( $locked ) {
                fputs( $fpLog, 'No such user\n' );
            }
            break;
        }
    } while ( false );

} else {
    if ( $locked ) {
        fputs( $fpLog, 'Something went wrong.\n' );
    }
}


// ADDED CODE - END


}
else if (strcmp ($res, "INVALID") == 0) {
// log for manual investigation
}
}
fclose ($fp);
}
fputs( $fpLog, "\nPAYPAL_RESP_END======\n" );
flock($fpLog, LOCK_UN);
fclose($fpLog);
require_once('tail_inc.php');

?>

