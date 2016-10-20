<?php
/*$Id: top.php 378 2009-11-02 19:36:24Z dmitriy $*/
require_once('head_inc.php');
require_once('html_head_inc.php');
require_once('mysql_log.php');
?>
<base target="bottom">
<script type="text/javascript" src="js/threads_autoload.js"></script>
<script>

function setVerticaLayout()
{
    document.getElementById('frame1').style.float = 'left';
    document.getElementById('frame1').style.width = '50vw';
    document.getElementById('frame1').style.height = '90vh';
    document.getElementById('frame2').style.float = 'right';
    document.getElementById('frame2').style.width = '48vw';
    document.getElementById('frame2').style.height = '90vh';
    document.getElementById('hr1').style.display = 'none';            
    //document.getElementById('hr2').style.display = 'block';            
}
</script>
<title><?=$title?></title>
</head>
<body id="html_body" style="overflow: hidden;">
<?php
require('menu_inc.php');
require_once('tail_inc.php');
?>

<div id="frame1" style="position: static; height: 40vh; background-color: white;display: inline-block;width: 100vw;">
    <iframe style="border: none;" width="100%" height="100%" name="contents" src="threads.php"></iframe>
</div>
<div id="hr1"></div>
<div id="frame2" style="position: relative;height: 48vh; background-color: white;display: inline-block; width: 100vw;">
    <iframe style="border: none;" width="100%" height="100%" name="bottom" src="welc.php"></iframe>
</div>
<div style="display:none;" id="hr2"></div>
</body>
</html>
