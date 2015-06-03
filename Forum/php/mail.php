<?php
 require_once "Mail.php";
 
function send_mail($to, $subject, $body)
{
 $from = "Kirdyk <dmitriy@radier.ca>";

 //$to = "Dmitriy Fitisov <dmitriy@radier.ca>";
 //$subject = "email";
 //$body = "Hi,\n\nHow are you?";
 
 $host = "server.radier.ca";
 $port = "25";
 $username = "dmitriy";
 $password = "empty1";
 
 $headers = array ('From' => $from,
   'To' => $to,
   'Subject' => $subject);
 $smtp = Mail::factory('smtp',
   array ('host' => $host,
     'localhost' => 'kirdyk.radier.ca',
     'port' => $port,
     'auth' => true,
     'username' => $username,
     'password' => $password));
 
 $mail = $smtp->send($to, $headers, $body);
 
 if (PEAR::isError($mail)) {
   return ("<p>" . $mail->getMessage() . "</p>");
  } else {
   return ("<p>Message successfully sent!</p>");
  }
}

 ?>

