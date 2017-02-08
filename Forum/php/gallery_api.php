<?php

require_once('dump.php');
require_once('head_inc.php');

if (array_key_exists('get_imgUrls', $_POST)) {
        $get_imgUrls = $_POST['get_imgUrls'];
        if (is_numeric ($get_imgUrls)) 
        {
		$query = "select id, user_id, msg_id, URL from confa_assets where user_id=". $_POST['get_imgUrls'] ." ORDER BY msg_id DESC;";
        } else {
		$query = "select id, user_id, msg_id, URL from confa_assets ORDER BY msg_id DESC;";
        }

        $result = mysql_query($query);
        if (!$result) {
          mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
          die('Query failed');
        }
        //return the array of URLs for the user.
        $imageUrls[]= null;
        while ($row = mysql_fetch_assoc($result)) {
    	    $imageUrl = $row['URL'];
    	    array_push($imageUrls,$imageUrl);
	}
      header('Content-type:application/json;charset=utf-8');
      unset($imageUrls[0]);//removing the null
      echo json_encode($imageUrls);
}

if (array_key_exists('get_imgUrls_and_Posts', $_POST)) {
        $get_imgUrls_and_Posts = $_POST['get_imgUrls_and_Posts'];
        if (is_numeric ($get_imgUrls_and_Posts)) 
        {
		$query = "SELECT confa_assets.URL, confa_posts.id, confa_posts.subject, confa_users.username, confa_users.ID FROM confa_assets, confa_posts, confa_users  WHERE  confa_assets.user_id=". $_POST['get_imgUrls_and_Posts'] ." AND confa_assets.msg_id=confa_posts.id AND confa_assets.user_id=confa_users.ID ORDER BY confa_posts.id DESC;";
        } else {
		$query = "SELECT confa_assets.URL, confa_posts.id, confa_posts.subject, confa_users.username, confa_users.ID FROM confa_assets, confa_posts, confa_users  WHERE  confa_assets.msg_id=confa_posts.id AND confa_assets.user_id=confa_users.ID ORDER BY confa_posts.id DESC;";
        }
	//die($query);
	
        $result = mysql_query($query);
        if (!$result) {
          mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
          die('Query failed');
        }
        //return the array of URLs for the user.
        $imageUrls[]= null;
        while ($row = mysql_fetch_assoc($result)) {
    	    $imageUrl = $row['URL'];
    	    $username = $row['username'];
    	    $subject = $row['subject'];
    	    $image_data = array($imageUrl, $username, $subject, $row['id'], $row['ID']);
    	    array_push($imageUrls,$image_data);
	}
      header('Content-type:application/json;charset=utf-8');
      unset($imageUrls[0]);//removing the null
      echo json_encode($imageUrls);
}
    
  $text = "Hi Gallery API";
  $status = 201;

?>