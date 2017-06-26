<?php

$baseName = 'xxxxxx.com'; //this server base domain name. for example, if installed to xxx.com then the sub forums will be created as zzz.xxx.com subdomains
$serverIp = 'xxx.xxx.xxx.xxx'; //this server public IP.
$hostedZoneId = 'ZZZZZZZZZZZZ'; //Open Route 53 service on AWS console and check your Hosted Zone ID. The solution currently requires Amazon.

$dbhost ='localhost';  
$dbuser = 'root';
$dbpassword = 'xxxxxx'; //change to the real mySQL pwd
$dbname ='central'; //this is where the list of sub-forums is.

?>