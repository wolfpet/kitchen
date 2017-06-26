<?php

function centralDBConnect()
{

    global $dbhost;
    global $dbuser;
    global $dbpassword;
    global $dbname;
    $link = mysql_connect($dbhost, $dbuser, $dbpassword);
    if (!$link) {return false;}
    if (!mysql_select_db($dbname)) {return false;}
    return true;
}

?>