<?php
require_once('dump.php');
require_once('head_inc.php');
require_once('html_head_inc.php');
require_once('custom_colors_inc.php'); 

    $title= 'Profile Settings';

?>

<script>

</script>

<link rel="stylesheet" type="text/css" href="css/spectrum.css">
<script type="text/javascript" src="js/profinit.js"></script>
<script type="text/javascript" src="js/spectrum.js"></script>
<script type="text/javascript" src="js/profcolors.js"></script>

<base target="bottom">
</head>
<body>
<h3 id="status_text"><?php print($title);?></h3>
<!-- <span style="color:red; font-weight:bold;" id="status_text">&nbsp;</span>  -->
<?php

    $query=' SELECT email, prop_bold, prop_tz, show_smileys, reply_to_email, menu_style from confa_users where id = ' . $user_id;
    $result = mysql_query( $query );
    if (!$result) {
        mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed ' );
    }
    $row = mysql_fetch_assoc( $result );
    $email = $row['email'];
    $email2 = $email;
    $profile_bold = $row['prop_bold'];
    $prop_tz_name = $row['prop_tz'];
    $prop_tz = get_tz_offset($row['prop_tz']);
    $smileys = $row['show_smileys'];
    $reply_to_email = $row['reply_to_email'];
    $menu_style = $row['menu_style'];
?>
<?php
require_once("profile_inc.php");
require_once('tail_inc.php');
?>
