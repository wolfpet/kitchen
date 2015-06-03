<?php

require_once('dump.php');

/***********************************/
/*****  Variables declaration  *****/
/***********************************/

// Variable used to calculate number of messages
// in By Date for unauthenticated users 
$last_post = 0;
// Id of current logged in user
$my_id = 0;
$my_name = "anonymous";


  /*********************/
  /*****  STARTUP  *****/
  /*********************/

  session_start();
  
  // Only values that are non mutable may be saved in 
  // _SESSION, as user may have many sessions and
  // non mutable values 
  if(isset($_SESSION['my_id'])) {
    $my_id = $_SESSION['my_id']; 
    $my_name = $_session['my_name'];


  } else {
    // Update settings for anonymous user
    if (isset($_SESSION['last_post'])) {
      $last_post = $_SESSION['last_post'];
    }
  }

  /***************************/
  /***** MAIN SELECTOR *******/
  /***************************/
  
header("Content-type: application/x-javascript");
////header("Content-Type application/json");
  $json = ($_GET['json']);
  $new_json = array();
  $new_json['test5'] = $json["test3"];
  $new_json['test8'] = $json['test4'];
  $new_json["test9"] = "Ok";
//$arr = array ('item1'=>"I love ",'item2'=>"You love ",'item3'=>"We love jQuery4u");
echo json_encode($new_json);


   /*********************************/
   /****** SAVE DATA TO SESSION *****/
   /*********************************/

  //Save data to _SESSION
  if($my_id != 0 && !isset($_SESSION['my_id'])) {
    $my_id = $_SESSION['my_id']; 
    $my_name = $_session['my_name'];


  } 

  if ($my_id == 0) {
    // Update settings for anonymous user
    $_SESSION['last_post'] = $last_post;
  }
?>


