<?php
/*$Id: top.php 378 2009-11-02 19:36:24Z dmitriy $*/
require_once('head_inc.php');
require_once('html_head_inc.php');
require_once('mysql_log.php');
?>
<base target="bottom">
<script type="text/javascript" src="js/threads_autoload.js"></script>
<script type="text/javascript" src="js/controls.js"></script>
<script>
function expand()
{
document.getElementById("frame1").style.height='0vh';
document.getElementById("frame2").style.height='88vh';
}
function restore()
{
document.getElementById("frame1").style.height='40vh';
document.getElementById("frame2").style.height='48vh';
}
</script>
<title><?=$title?></title>
</head>
<body id="html_body" style="overflow: hidden;">
<?php
require('menu_inc.php');
?>
<div id="frame1" style="position: static; height: 40vh; background-color: white;display: inline-block;width: 100vw;">
    <iframe style="border: none;" width="100%" height="100%" name="contents" src="threads.php"></iframe>
</div>
<hr id="hr1">
<div id="frame2" style="position: relative;height: 48vh; background-color: white;display: inline-block; width: 100vw;">
    <iframe style="border: none;" width="100%" height="100%" name="bottom" id="bottom" src="welc.php"></iframe>
</div>
<?php
require('gallery_inc.php');
require('overlay_inc.php');
require_once('tail_inc.php');
?>
</body>
</html>
