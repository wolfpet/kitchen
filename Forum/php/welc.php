<Html>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-Frame-Options" content="SAMEORIGIN">
<meta http-equiv="X-Content-Type-Options" content="nosniff">
<link rel="stylesheet" type="text/css" href="css/common.css?<?=filemtime('css/common.css')?>">
<link rel="stylesheet" type="text/css" href="css/disc2.css?<?=filemtime('css/disc2.css')?>">
<title>Welcome Frame - Kitchen</title>
</head>
<body>
<div id="github" style="color:lightgray;float:right;position:absolute;left: 0px;top:5px;width:100%;height:100%;z-index: 9999;text-align: right"><a target="_blank" href="https://github.com/wolfpet/kitchen">GitHub</a>&nbsp;&nbsp;</div> 
<H3> Free forum</H3>
<p>
<div id="history"></div>
<script language="javascript">
function onstart() {
  historyData.start(function(event) {
    return "<p><h5>" + event.date + ", " + event.year + "</h5>" + event.html +"</p>"
  });
}
window.onload=onstart;
</script>

<?php if (isset($tmdb_key)) {?>
<span id="tmdb-attribution" style="color:gray;height:10%;position:absolute;bottom:0;">This product uses the TMDb API but is not endorsed or certified by TMDb.&nbsp;<a target="_blank" href="https://www.themoviedb.org/"><img src="images/powered-by-rectangle-green.png" valign="middle" height="50%"></a></span>
<?php } ?>
</body>
</html>
