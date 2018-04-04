<?php
/*$Id: top.php 378 2009-11-02 19:36:24Z dmitriy $*/
require_once('head_inc.php');
require_once('html_head_inc.php');
require_once('mysql_log.php');

//temp woraround for iOS. Ugly, I know. Need time to figure out what to do here
$iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
$iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");

if( $iPod || $iPhone || $iPad)
{
//redirect to top_apple
$url='top_apple.php';
echo '<META HTTP-EQUIV=REFRESH CONTENT="1; '.$url.'">';
die();
}
?>
<base target="bottom">

<script type="text/javascript" src="<?=autoversion('js/controls.js')?>"></script>
<script type="text/javascript" src="js/jquery-ui.js"></script>
<script>
function expand()
{
verticalLayout = getCookie("verticalLayout");
if(verticalLayout=='true')return;
document.getElementById("frame1").style.height='0vh';
document.getElementById("frame2").style.height='88vh';
}
function restore()
{
verticalLayout = getCookie("verticalLayout");
if(verticalLayout=='true')return;
document.getElementById("frame1").style.height='40vh';
document.getElementById("frame2").style.height='48vh';
}

$( function() { $( "#slider" ).draggable({ containment: "#slider-area", scroll: false, axis: "y", 
      start: function() {
                    document.getElementById("resizer").style.fill="red";
                    document.getElementById("hr1").style.borderColor="red";
            },
      drag: function() {
                    var h = $("#slider").position().top;
                    var vhpx = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
                    var menu_h = $("#Ribbon").height();
                    var frame1_vh = ((((h+menu_h - 10) * 100)/vhpx) || 0);
                    document.getElementById("frame1").style.height=frame1_vh + 'vh';
                    var frame2_vh = 100 - 10 - frame1_vh;
                    document.getElementById("frame2").style.height= frame2_vh  + 'vh';
            },
      stop:  function() {
                    document.getElementById("resizer").style.fill="grey";
                    document.getElementById("hr1").style.borderColor="grey";
            } 
          });
});

</script>
<title><?=$title?></title>
</head>
<body id="html_body" style="height:100%;">
<div id="confaFrameContainer" style="position: absolute; height:100%; overflow-y:hidden; width: 100%">
<?php
require('menu_inc.php');
?>
<div id="frame1" style="position: static; height: calc(50% - 74px); background-color: white;display: inline-block;width: 100vw;">
    <iframe style="border: none;" width="100%" height="100%" name="contents" src="threads.php"></iframe>
</div>
<hr id="hr1">
<div id="slider-area">
    <div id="slider" class="draggable ui-widget-content"><svg viewBox="0 0 24 24" preserveAspectRatio="xMidYMid meet" style="pointer-events: none; display: block; width: 100%; height: 100%;"><g><path id="resizer" fill="grey" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM6.5 9L10 5.5 13.5 9H11v4H9V9H6.5zm11 6L14 18.5 10.5 15H13v-4h2v4h2.5z"></path></g></svg></div>
</div>
<div id="frame2" style="position: relative;height: 49%; background-color: white;display: inline-block; width: 100vw;">
    <iframe style="border: none;" width="100%" height="100%" name="bottom" id="bottom" src="welc.php"></iframe>
</div>
<?php
require('gallery_inc.php');
require('overlay_inc.php');
require_once('tail_inc.php');
?>
</div>
</body>
</html>
