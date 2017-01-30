<?php 
notify_about_new_pm($user_id, $last_pm_check_time);

if(isset($classic_menu)) 
  $menu_style=1;
else if (!isset($menu_style) || !file_exists('menu_'.$menu_style.'.php')) 
  $menu_style=0;

require_once('menu_'.$menu_style.'.php');
?>
