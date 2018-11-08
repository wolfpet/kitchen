<?php require_once('head_inc.php'); ?>

<Html>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-Content-Type-Options" content="nosniff">
<link rel="stylesheet" type="text/css" href="css/disc2.css?<?=filemtime('css/disc2.css')?>">
<link rel="stylesheet" type="text/css" href="css/common.css?1476981264">
<script>
function loadimage(img)
{
 setTimeout(function()
  {
    img.style.opacity= 1;
    var downloadingImage = new Image();
    downloadingImage.onload = function(){
    img.src = this.src;
    };
    downloadingImage.src = img.alt;
  }
      , 50);
}
function resizeMe(iframe)
{
    iframe.width  = iframe.contentWindow.document.body.scrollWidth + 5;
    iframe.height = iframe.contentWindow.document.body.scrollHeight + 25;
}
</script>
</head>
<body>
<div id="postPreview" style="display: none; height: 90vh; width: 95vw; position: absolute; background-color: white;"></div>
<H3> <?=$title;?> forum</H3>
Hi and welcome! There are only 3 rules in this forum: <br><br>
1. Nothing illegal is allowed. <br>
2. No personal attacks/mocking is allowed.<br>
3. No persistent activism is welcome, including, but not limited to political propaganda. Positive political discussions are welcome, however.<br>
<br>
All 3 rules are subject to moderators' interpretation. Please do not complain. They are also subject to change without notice.
<p>
<h3>Did you know?</h3>
<span id="know1" style="display:none">You can add searchable <span style="color: blue">#hashtags</span> to your messages similar to Twitter and Facebook!</span>
<span id="know2" style="display:none">You can paste <span style="color: blue">Youtube, Facebook and Twitter</span> links and they will render inline!</span>
<span id="know3" style="display:none">You can click on a <span style="color: blue">picture</span> to view it in full screen mode!</span>
<span id="know4" style="display:none">Click GIF button to add <span style="color: blue">animated GIF</span> when editing your post!<br><br><img src="https://media.tenor.com/images/cc0f9d10e914ddb1abae13ca88630b57/tenor.gif"></img></span>
<span id="know5" style="display:none">Pmail <span style="color: blue">adds messages in real time.</span> You can have one on one chat without reloading!</span>
<span id="know6" style="display:none">Threads are loading  <span style="color: blue">automatically</span> every 60 seconds; no need to refresh the view!</span>
<span id="know7" style="display:none">Post your movie review <span style="color: blue">with IMDB link</span> and we will render the movie poster and the score!</span>
<span id="know8" style="display:none">To quote someone select the text in the parent message <span style="color: blue"> and click QUOTE button.</span> This will create a properly formatted quote. Easy!</span>
<span id="know9" style="display:none">To <span style="color: blue"> add the url tags</span> copy the address to clipboard, select the word in your message and paste the URL. This will wrap the word with tags. Nice huh?</span>
<span id="know10" style="display:none">Small mobile screen? <span style="color: blue"> Rotate it 90 degrees</span> to see more options!</span>
<span id="know11" style="display:none">You can change <span style="color: blue"> the forum colors!</span> Click Colors in the profile section.</span>
<span id="know12" style="display:none">Don't like the graphic menu icons? <span style="color: blue"> Switch to classic</span> forum menu in the profile section.</span>
<span id="know13" style="display:none">Resetting notifications without reloading is easy:<span style="color: blue"> Click on the bell icon, then click again</span> and the red badges will go away.</span>


<?php if (isset($tmdb_key)) {?>
<p><br/><p>
<span id="tmdb-attribution" style=" display:block;color:lightgray;height:10%"><a target="_blank" href="https://www.themoviedb.org/"><img src="images/powered-by-rectangle-green.png" valign="middle" height="50%"></a>&nbsp;This product uses the TMDb API but is not endorsed or certified by TMDb.</span>
<?php } ?>

<br><br><br><br><br><br>
<script type="text/javascript">
        function knowAddress() {
        var x = Math.floor((Math.random() * 13) + 1);
        document.getElementById("know" + x).style.display="block";
}
window.onload = knowAddress;
</script>

</body>
</html>
