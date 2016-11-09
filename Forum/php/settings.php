<?php
/*$Id: settings.php 378 2009-11-02 19:36:24Z dmitriy $*/

$dbhost     = 'localhost';
$dbuser     = '<user>';
$dbpassword = '<password>';
$dbname     = 'confa';

$host   = '<hostname>'; // 127.0.0.1 for running locally
$root_dir   = '/';
$server_tz = '-5:00';

$prop_tz_name = 'America/Toronto'; // default user timezone
date_default_timezone_set($prop_tz_name);

$title='<forum title>';
$from_email = '<supportemailaddress>';

// Registration mode (by default, closed)
$reg_type = REG_TYPE_CLOSED; // REG_TYPE_OPEN, REG_TYPE_CONFIRM;

$reactions = array(
  'clap' => 0,
  'lol' => 0,
  'rofl' => 0,
  'confused' => 0,
  'shock' => 0,
  'weep' => 0,
  'facepalm' => 0,
  'mad' => 0,
);

// $google_key = ""; // (optional) specify your Google API key here to enable YouTube metadata

// $tmdb_key = ""; // (optional) specify your The Moview Database API key here to enable IMDB metadata 

// $recaptcha_site_key = ''; // (optional) specify your Google reCAPTCHA site key here to enable bot protection for registration page
// $recaptcha_secret_key = ''; // if you specified $recaptcha_site_key, copy your reCAPTCHA secret key here 
?>

