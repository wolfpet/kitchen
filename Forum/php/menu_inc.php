<?php 
notify_about_new_pm($user_id, $last_pm_check_time);

if (!isset($menu_style) || !file_exists('menu'.$menu_style.'.php')) $menu_style=0;

?>
