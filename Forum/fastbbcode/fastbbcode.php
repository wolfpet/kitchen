<?php
$br = (php_sapi_name() == "cli")? "":"<br>";

if(!extension_loaded('fastbbcode')) {
	dl('fastbbcode.' . PHP_SHLIB_SUFFIX);
}
$module = 'fastbbcode';
$functions = get_extension_funcs($module);
echo "Functions available in the test extension:$br\n";
foreach($functions as $func) {
    echo $func."$br\n";
}
echo "$br\n";
$function = 'bbcode';
$str="123";
if (extension_loaded($module)) {
	$str = $function('[b]' . $module . '[/b]');
} else {
	$str = "Module $module is not compiled into PHP";
}
echo "$str\n";
?>