<?php require_once('head_inc.php'); ?>

<Html>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-Content-Type-Options" content="nosniff">
<link rel="stylesheet" type="text/css" href="<?=autoversion('css/disc2.css')?>">
<link rel="stylesheet" type="text/css" href="<?=autoversion('css/common.css')?>">
<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
<script src="<?=autoversion('js/history.js')?>"></script>
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
<base target="_blank"></head>
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
<?php 
  $facts = array(
    'You can add searchable <span style="color: blue">#hashtags</span> to your messages similar to Twitter and Facebook!',
    'You can paste <span style="color: blue">Youtube, Facebook and Twitter</span> links and they will render inline!',
    'You can click on a <span style="color: blue">picture</span> to view it in full screen mode!',
    'Click GIF button to add <span style="color: blue">animated GIF</span> when editing your post!<br><br><img src="https://media.tenor.com/images/cc0f9d10e914ddb1abae13ca88630b57/tenor.gif"></img>',
    'Pmail <span style="color: blue">adds messages in real time.</span> You can have one on one chat without reloading!',
    'Threads are loading  <span style="color: blue">automatically</span> every 60 seconds; no need to refresh the view!',
    'Post your movie review <span style="color: blue">with IMDB link</span> and we will render the movie poster and the score!',
    'To quote someone select the text in the parent message <span style="color: blue"> and click QUOTE button.</span> This will create a properly formatted quote. Easy!',
    'To <span style="color: blue"> add the url tags</span> copy the address to clipboard, select the word in your message and paste the URL. This will wrap the word with tags. Nice huh?',
    'Small mobile screen? <span style="color: blue"> Rotate it 90 degrees</span> to see more options!',
    'You can change <span style="color: blue"> the forum colors!</span> Click Colors in the profile section.',
    'Don\'t like the graphic menu icons? <span style="color: blue"> Switch to classic</span> forum menu in the profile section.',
    'Resetting notifications without reloading is easy:<span style="color: blue"> Click on the bell icon, then click again</span> and the red badges will go away.',
    'You can edit your messages for '.$days_to_edit_post.' days after you sent them! Keep in mind that the changes are saved and can be viewed by anyone.',
    'The recepient of your <span style="color: red">Pmail</span> will be notified by a modal dialog when they visit forum. No need to create "Please check Pmail" topics. <i>Ever!</i>',
    'You can use *, _ and ~ to make text fragments bold, italic or strikethrough. Just like in WhatsApp!',
    'In user profile settings, you can ask not to display NSFW content on your <b>current device</b>.',
    'Dates and times can be displayed in your faviorite time zone. Check user profile settings!'
  );
  
  $i = rand(0, sizeof($facts) - 1);
  //for ($i = 0; $i < sizeof($facts); $i++) {  
?>
  <span id="know<?=($i + 1)?>" style="display:block"><?=$facts[$i]?></span>
<?php 
  //} 
?>
<p>
<div id="history"></div>
<?php if (isset($tmdb_key)) {?>
<br/>
<span id="tmdb-attribution" style=" display:block;color:lightgray;height:10%"><a target="_blank" href="https://www.themoviedb.org/"><img src="images/powered-by-rectangle-green.png" valign="middle" height="50%"></a>&nbsp;This product uses the TMDb API but is not endorsed or certified by TMDb.</span>
<?php } ?>
<br><br><br><br><br><br>
<script type="text/javascript">
  window.onload = function () {
    historyData.start();
  };
</script>

</body>
</html>
