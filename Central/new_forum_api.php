<?php

require_once('settings.php');
require_once('func.php');

require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Common\Aws;

$name = $_POST['name'];
$email = $_POST['email'];
$forum = $_POST['forum'];
$pwd = $_POST['pwd'];

$validationError = null;
//connect to central db
if(!centralDBConnect()){die('CentralDBError');}
//validate
if(!validate())die($validationError);
//add forum to central db
$query = 'INSERT INTO forums(forum_title, email, fullname) values(\''. mysql_escape_string($forum) .'\', \''  . mysql_escape_string($email) . '\', \''. mysql_escape_string($name) .'\')';
$result = mysql_query($query);
if (!$result) { die('Query failed'); }
//create the new forum db
createDB();
//add Route 53 subdomain
addDNS();
echo "success";



function validate()
{
    global $validationError;
    global $forum;
    
    $forum = mysql_escape_string($forum);
    $forum = strtolower($forum);
    //check if the forum name has already been taken.
    $query = 'select forum_title from forums where forum_title = \'' . mysql_real_escape_string($forum). '\';';
    $result = mysql_query($query);
    $numberOfSites = mysql_num_rows($result);
    if($numberOfSites >0){$validationError='Forum title has already been taken'; return false;}
    //check against sql injection, cross scripting, etc.
    return true;
}

function createDB()
{
    global $forum;
    $path = getcwd();
    
    //create db
    $command = 'mysql -uroot -pkitchen -e "CREATE DATABASE '.$forum.' /*\!40100 DEFAULT CHARACTER SET utf8 */;"';
    $output = shell_exec($command);
    
    //execute sql script.
    $command = "mysql -uroot -pkitchen -D ".$forum." < " . $path ."/empty.sql";
    $output = shell_exec($command);

    //add stored procedures
    $command = "mysql -uroot -pkitchen -D ".$forum." < " . $path ."/procedures.sql";
    $output = shell_exec($command);

}

function addDNS()
{

global $forum;
global $baseName;
global $hostedZoneId;
global $serverIp;

// Create the AWS service builder, providing the path to the config file
$aws = Aws::factory('aws_config.php');
$DNSClient = $aws->get('Route53');

$result = $DNSClient->changeResourceRecordSets(array(
    'HostedZoneId' => $hostedZoneId,
    'ChangeBatch'  => array(
    'Comment'      => 'K-Central adding: '.$forum,
    'Changes'      => array(
                      array(
                       'Action' => 'CREATE',
                       'ResourceRecordSet' => array(
                       'Name' => $forum .'.'.$baseName,
                       'Type' => 'A',
                       'TTL' => 600,
                       'ResourceRecords' => array(array('Value' =>  $serverIp,),),
                       ),
                       ),
                       ),
                       ),
));

//echo $result;
}


?>