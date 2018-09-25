<style>
.greyHover {
    //background-color: #FFFFFF;
}
.greyHover:hover {
    background-color: #ebebeb;
    cursor: pointer;
}
</style>

<?php

    if (strlen($err) != 0) {
        print('<B><font color="red">' . $err . '</font></B><BR>');
    }
?>
<script language="JavaScript" src="<?=autoversion('js/translit.js')?>"></script>
<script language="JavaScript" src="<?=autoversion('js/func.js')?>"></script>
<script>

    var imageGallery='<?=$imageGallery?>';
    var imageGalleryUploadOn=false;

    document.addEventListener("DOMContentLoaded", function(event) { 
        nsfw  = document.getElementById("nsfw").checked;
        if(nsfw)document.getElementById("nsfwPath").style.fill="red";
    });
    function toggleNsfw()
    {
      nsfw = document.getElementById("nsfw").checked;

      if(nsfw==false)
      {
        document.getElementById("nsfw").checked = true; 
        document.getElementById("nsfwPath").style.fill="red"; 
      }
      else
      {
        document.getElementById("nsfw").checked = false; 
        document.getElementById("nsfwPath").style.fill="black"; 
      }
    }
    function togglePreview()
    {
      preview = document.getElementById("preview").checked;

      if(preview==false)
      {
        document.getElementById("preview").checked = true; 
        document.getElementById("previewPath").style.fill="red"; 
      }
      else
      {
        document.getElementById("preview").checked = false; 
        document.getElementById("previewPath").style.fill="black"; 
      }
    }
    function toggleImageUpload()
    {
      if(imageGallery == 'amazon' || imageGallery == 'local')
      {
        if(imageGalleryUploadOn==false)
        {
           //turn on the upload form
           imageGalleryUploadOn=true;
           document.getElementById("imagePath").style.fill="red";
           document.getElementById("body").style.display="none";
           document.getElementById("galleryUploadFrame").style.display="block";
           //load gallery_upload_form.php
           $("#galleryUploadFrame").attr("src", "gallery_upload_form.php");
        }
        else
        {
           //turn off the upload form
           imageGalleryUploadOn=false;
           document.getElementById("imagePath").style.fill="black";
           document.getElementById("body").style.display="block";
           document.getElementById("galleryUploadFrame").style.display="none";
        }
      }
      else
      {
        //add the image url BB tag
        insertTag('body', 2);
      }
    }

function insertPoll()
{
       //open poll creation overlay
       parent.openOverlay('newPoll');
}

function sendMessage()
{
    //validate the subj
    if(document.getElementById("subj").value=='')
    {
        document.getElementById("subj_div").style.color='red';
        return;
    }
    document.getElementById("msgform").submit();
}

</script>

<!-- <h3><?php print($title);?></h3> -->
<div id="Msg_ribbon" class="ribbon" style="padding: 0px; color: #000000;background-color: #ffffff; display: block; width: 95vw; height: 200vh;">

<form autocomplete="off" action="<?php print($root_dir . $page_post); ?>" method="post" id="msgform" name="msgform">
    <input autocomplete="false" name="hidden" type="text" style="display:none;">
    <?php if (isset($msg_id) && !is_null($msg_id)) { ?> <input type="hidden" name="id" id="id" value="<?php print($msg_id); ?>"/> <?php } ?>
    <input type="hidden" name="re" id="re" value="<?php print($re); ?>"/>
    <input type="hidden" name="ticket" id="ticket" value="<?php print($ticket); ?>"/>
    <?php  if (!$logged_in) { ?>

From: <input onfocus="javascript:RegisterField(this, true, false);" onkeypress="javascript:translate2(event);" onkeydown="javascript:text_OnKeydown(event);" type="text" id="user" name="user" value="<?php print($user); ?>" size="32" maxlength="64"/>
<br>
Password:<input type="password" id="password" name="password" size="16" maxlength="16" autocomplete="off"/>

<?php } else {?>

<div>From: &nbsp;&nbsp;&nbsp;&nbsp;<B><?php print($user); ?></B></div>
<?php
}
if (!isset($_SERVER['HTTP_USER_AGENT']) || FALSE === strpos( $_SERVER['HTTP_USER_AGENT'], 'Opera Mini' ) ) {
  $keyboard = true;
} else {
  $keyboard = false;
}
?>
<div id="subj_div" style="padding-top: 5px; padding-bottom: 5px">
Subject: <input style="width: 60%; border: lightgrey; border-style: solid; border-width: 1px;" type="text" <?php if ($keyboard) { ?> onfocus="javascript:RegisterField(this, true, false);" onkeypress="javascript:translate2(event);" onkeydown="javascript:text_OnKeydown(event);" <?php } ?> name="subj" id="subj" tabindex="1" maxlength="254" value='<?php /*print(htmlentities($subj, HTML_ENTITIES,'UTF-8'));*/ print(/*$subj*/str_replace("'", "&#39", $subj)); ?>' />
</div>

  
  <!-- EDITING TOOLS -->
  <div id="StyleRibbonGroup" style="border: lightgrey; border-style: solid; border-width: 1px;" class="ribbonGroup";>
  <div id="StyleRibbonGroupTitle" class="ribbonGroupTitle">Style</div>
    <div id="StyleRibbonGroupIconContainer">


	<span class="ribbonIcon tooltip greyHover" id="langIcon">
	 <a onclick="javascript:toggleCharset();" >
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path class="ribbonIcon" fill="#000000" d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm6.93 6h-2.95c-.32-1.25-.78-2.45-1.38-3.56 1.84.63 3.37 1.91 4.33 3.56zM12 4.04c.83 1.2 1.48 2.53 1.91 3.96h-3.82c.43-1.43 1.08-2.76 1.91-3.96zM4.26 14C4.1 13.36 4 12.69 4 12s.1-1.36.26-2h3.38c-.08.66-.14 1.32-.14 2 0 .68.06 1.34.14 2H4.26zm.82 2h2.95c.32 1.25.78 2.45 1.38 3.56-1.84-.63-3.37-1.9-4.33-3.56zm2.95-8H5.08c.96-1.66 2.49-2.93 4.33-3.56C8.81 5.55 8.35 6.75 8.03 8zM12 19.96c-.83-1.2-1.48-2.53-1.91-3.96h3.82c-.43 1.43-1.08 2.76-1.91 3.96zM14.34 14H9.66c-.09-.66-.16-1.32-.16-2 0-.68.07-1.35.16-2h4.68c.09.65.16 1.32.16 2 0 .68-.07 1.34-.16 2zm.25 5.56c.6-1.11 1.06-2.31 1.38-3.56h2.95c-.96 1.65-2.49 2.93-4.33 3.56zM16.36 14c.08-.66.14-1.32.14-2 0-.68-.06-1.34-.14-2h3.38c.16.64.26 1.31.26 2s-.1 1.36-.26 2h-3.38z"></path>
	    </g></svg>
	    <span style="padding-left: 20px; " class="tooltiptext">Language</span>
	    <!-- This is a badge sample that indicates that there are 4 new posts -->
    	    <span id="ruschars" class="button__badge" style="background-color: green;display: none;">Ru</span>
    	    <span id="latchars" class="button__badge" style="background-color: green;display: block;">En</span>
    	    
    	 </a> 
	</span> 

	<span class="ribbonIcon tooltip" id="BoldIcon">
	 <a onclick="javascript:insertBBCode('body', 'b');return false;" >
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path class="ribbonIcon" fill="#000000" d="M15.6 10.79c.97-.67 1.65-1.77 1.65-2.79 0-2.26-1.75-4-4-4H7v14h7.04c2.09 0 3.71-1.7 3.71-3.79 0-1.52-.86-2.82-2.15-3.42zM10 6.5h3c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5h-3v-3zm3.5 9H10v-3h3.5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5z" ></path>
	    </g></svg>
	    <span class="tooltiptext">Bold</span>
	    <!-- This is a badge sample that indicates that there are 4 new posts -->
    	    <!-- <span id="newPostsBadge" class="button__badge" style="display: block;">1</span> -->
    	 </a> 
	</span> 
	    <span id="ItalicIcon" class="ribbonIcon tooltip"><a onclick="javascript:insertBBCode('body', 'i');return false;">
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path class="ribbonIcon" fill="#000000" d="M10 4v3h2.21l-3.42 8H6v3h8v-3h-2.21l3.42-8H18V4z"></path>
	    </g></svg>
	    <span class="tooltiptext">Italic</span>
	</a>
	</span> 
	<span id="UnderlinedIcon" class="ribbonIcon tooltip"><a onclick="javascript:insertBBCode('body', 'u');return false;">
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path class="ribbonIcon" fill="#000000" d="M12 17c3.31 0 6-2.69 6-6V3h-2.5v8c0 1.93-1.57 3.5-3.5 3.5S8.5 12.93 8.5 11V3H6v8c0 3.31 2.69 6 6 6zm-7 2v2h14v-2H5z"></path>
	    </g></svg>
	    <span class="tooltiptext">Underlined</span></a>
	</span> 
	<span id="Strikethrough" class="ribbonIcon tooltip"><a onclick="javascript:insertBBCode('body', 's');return false;">
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path class="ribbonIcon" fill="#000000" d="M7.24 8.75c-.26-.48-.39-1.03-.39-1.67 0-.61.13-1.16.4-1.67.26-.5.63-.93 1.11-1.29.48-.35 1.05-.63 1.7-.83.66-.19 1.39-.29 2.18-.29.81 0 1.54.11 2.21.34.66.22 1.23.54 1.69.94.47.4.83.88 1.08 1.43.25.55.38 1.15.38 1.81h-3.01c0-.31-.05-.59-.15-.85-.09-.27-.24-.49-.44-.68-.2-.19-.45-.33-.75-.44-.3-.1-.66-.16-1.06-.16-.39 0-.74.04-1.03.13-.29.09-.53.21-.72.36-.19.16-.34.34-.44.55-.1.21-.15.43-.15.66 0 .48.25.88.74 1.21.38.25.77.48 1.41.7H7.39c-.05-.08-.11-.17-.15-.25zM21 12v-2H3v2h9.62c.18.07.4.14.55.2.37.17.66.34.87.51.21.17.35.36.43.57.07.2.11.43.11.69 0 .23-.05.45-.14.66-.09.2-.23.38-.42.53-.19.15-.42.26-.71.35-.29.08-.63.13-1.01.13-.43 0-.83-.04-1.18-.13s-.66-.23-.91-.42c-.25-.19-.45-.44-.59-.75-.14-.31-.25-.76-.25-1.21H6.4c0 .55.08 1.13.24 1.58.16.45.37.85.65 1.21.28.35.6.66.98.92.37.26.78.48 1.22.65.44.17.9.3 1.38.39.48.08.96.13 1.44.13.8 0 1.53-.09 2.18-.28s1.21-.45 1.67-.79c.46-.34.82-.77 1.07-1.27s.38-1.07.38-1.71c0-.6-.1-1.14-.31-1.61-.05-.11-.11-.23-.17-.33H21z"></path>	    
	    </g></svg>
	    <span class="tooltiptext">Strikethrough</span></a>
	</span> 

	

    </div>
  </div>
  
  <div id="InsertRibbonGroup" style="border: lightgrey; border-style: solid; border-width: 1px;" class="ribbonGroupMobile">
    <div id="InsertRibbonGroupTitle" class="ribbonGroupTitle">Insert</div>
    <div id="InsertRibbonGroupIconContainer">

	    <span id="ImageIcon" class="ribbonIcon tooltip">
	    <a onclick="javascript:toggleImageUpload();">
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path id="imagePath" class="ribbonIcon" fill="#000000" d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"></path>
	    </g></svg>
	    <span class="tooltiptext">Image</span>
	</a>
	</span> 

	<span class="ribbonIcon tooltip" id="PollIcon"><a onclick="javascript:insertPoll();return false;">
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path id="pollPath" class="ribbonIcon" style="fill: black;" d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"></path>
	    </g></svg>
	    <span class="tooltiptext">Poll</span>
    	 </a> 
	</span> 
<!--
	<span class="ribbonIcon tooltip" id="LinkIcon"><a onclick="javascript:insertTag('body', 1);return false;">
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path class="ribbonIcon" fill="#000000" d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"></path>
	    </g></svg>
	    <span class="tooltiptext">Link URL</span>
    	 </a> 
	</span> 
-->
	<span id="QuoteIcon" class="ribbonIcon tooltip"><a href="#" style="text-decoration: none" onclick="javascript:insertBBCode('body', 'quote');return false;">
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path class="ribbonIcon" fill="#000000" d="M6 17h3l2-4V7H5v6h3zm8 0h3l2-4V7h-6v6h3z"></path>
	    </g></svg>
	    <span class="tooltiptext">Quote</span></a>
	</span> 
	<span id="CodeIcon" class="ribbonIcon tooltip"><a onclick="javascript:insertBBCode('body', 'code');return false;">
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path class="ribbonIcon" fill="#000000" d="M9.4 16.6L4.8 12l4.6-4.6L8 6l-6 6 6 6 1.4-1.4zm5.2 0l4.6-4.6-4.6-4.6L16 6l6 6-6 6-1.4-1.4z"></path>
	    </g></svg>
	    <span class="tooltiptext">Code</span></a>
	</span> 
	<span id="EmojiIcon" class="ribbonIcon tooltip"><a onclick="javascript:smileys_on();">
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path class="ribbonIcon" fill="#000000" d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"></path>
	    </g></svg>
	    <span class="tooltiptext">Emoji</span></a>
	</span> 
	
	<span id="GifiIcon" class="ribbonIcon tooltip"><a onclick="javascript:gifs_on();">
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path class="ribbonIcon" fill="#000000" d="M11.5 9H13v6h-1.5zM9 9H6c-.6 0-1 .5-1 1v4c0 .5.4 1 1 1h3c.6 0 1-.5 1-1v-2H8.5v1.5h-2v-3H10V10c0-.5-.4-1-1-1zm10 1.5V9h-4.5v6H16v-2h2v-1.5h-2v-1z"></path>
	    </g></svg>
	    <span class="tooltiptext">Add GIF</span></a>
	</span> 

    </div>
  </div>
  <div id="RibbonGroup" style="border: lightgrey; border-style: solid; border-width: 1px;" class="ribbonGroupMobile" ;"="">
    <div id="PostRibbonGroupTitle" class="ribbonGroupTitle">Post</div>
    <div id="PostRibbonGroupIconContainer">

	    <span id="NsfwIcon" class="ribbonIcon tooltip"><a onclick="javascript:toggleNsfw();">
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path id="nsfwPath" class="ribbonIcon" fill="#000000" d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"></path>
	    </g></svg>
	    <span class="tooltiptext">NSFW</span>
	</a>
	</span> 
	<span class="ribbonIcon tooltip" id="PreviewIcon"><a onclick="javascript:parent.openOverlay('preview');">
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path id="previewPath" class="ribbonIcon" fill="#000000" d="M11.5 9C10.12 9 9 10.12 9 11.5s1.12 2.5 2.5 2.5 2.5-1.12 2.5-2.5S12.88 9 11.5 9zM20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-3.21 14.21l-2.91-2.91c-.69.44-1.51.7-2.39.7C9.01 16 7 13.99 7 11.5S9.01 7 11.5 7 16 9.01 16 11.5c0 .88-.26 1.69-.7 2.39l2.91 2.9-1.42 1.42z"></path>
	    </g></svg>
	    <span class="tooltiptext">Preview first</span>
    	 </a> 
	</span> 
    </div>
  </div>

	<span style="display: none;width: 110px;height: 49px;/* border: black; */border-style: hidden;border-width: 1px;position: absolute;padding-top: 7px;padding-left: 10px;">
	    <input  name="preview" id="preview" type="checkbox" readonly value="off"/>Preview First
	    <input name="nsfw" id="nsfw" type="checkbox" value="true" <?=isset($nsfw) && $nsfw?"checked":""?>/>NSFW
	</span>
  

  <!-- END OF EDITING TOOLS -->
    <div style="padding-top: 0px">
        <textarea style="margin-top: 6px;margin-bottom: 10px;width: 90%; height: 100px; border: lightgrey; border-style: solid; border-width: 1px;" id="body" name="body" <?php if ($keyboard) { ?> onfocus="javascript:RegisterField(this, true, false);" onkeypress="javascript:translate2(event);" onkeydown="javascript:text_OnKeydown(event);" onpaste="javascript:insertURL(this);"<?php } ?> cols="90" tabindex="2" rows="8"><?php  if (is_null($body) && $user == '486') { $body = '1';} print($body);?></textarea>
        <iframe id="galleryUploadFrame" style="margin-top: 6px;margin-bottom: 10px;display: none; width: 91%; height: 100px; border: lightgrey; border-style: solid; border-width: 1px;"></iframe>

        <div id="tenor_gifs" style="margin-bottom: 10px; overflow-y: scroll; padding-top: 5px; padding-bottom: 5px; width: 91%; height: 200px; display:none;border: lightgrey; border-style: solid; border-width: 1px;">
		Search gif: <input type="search" value="type here" id="gifSearchText" onclick="if(this.value=='type here')this.value='';" onload="gifsearch();" onkeypress="gifSearch();" style="width: 60%; border: lightgrey; border-style: solid; border-width: 1px;">
<hr>
	<img id="preview_gif0" src="" alt="" style="width:110px;height:82px; cursor: pointer;" onclick="addGif(this.src);">
	<img id="preview_gif1" src="" alt="" style="width:110px;height:82px; cursor: pointer;" onclick="addGif(this.src);">
	<img id="preview_gif2" src="" alt="" style="width:110px;height:82px; cursor: pointer;" onclick="addGif(this.src);">
	<img id="preview_gif3" src="" alt="" style="width:110px;height:82px; cursor: pointer;" onclick="addGif(this.src);">
	<img id="preview_gif4" src="" alt="" style="width:110px;height:82px; cursor: pointer;" onclick="addGif(this.src);">
	<img id="preview_gif5" src="" alt="" style="width:110px;height:82px; cursor: pointer;" onclick="addGif(this.src);">
	<img id="preview_gif6" src="" alt="" style="width:110px;height:82px; cursor: pointer;" onclick="addGif(this.src);">
	<img id="preview_gif7" src="" alt="" style="width:110px;height:82px; cursor: pointer;" onclick="addGif(this.src);">
	<img id="preview_gif8" src="" alt="" style="width:110px;height:82px; cursor: pointer;" onclick="addGif(this.src);">
	<img id="preview_gif9" src="" alt="" style="width:110px;height:82px; cursor: pointer;" onclick="addGif(this.src);">
        </div>
        
        <div id="smileys_help" style="margin-bottom: 10px; overflow-y: scroll; padding-top: 5px; padding-bottom: 5px; width: 91%; height: 70px; display:none;border: lightgrey; border-style: solid; border-width: 1px;"><?=smileys('body')?></div> <!-- make display style depend on user settings-->    
   </div>

<script>

function gifSearch()
{
    var url = "https://api.tenor.com/v1/anonid?key=" + "<?php print($tenorGifKey); ?>";
    httpGetAsync(url,tenorCallback_anonid);
}

function addGif(img)
{
    insertBodyText('body',img);
    document.getElementById('tenor_gifs').style.display='none';

}
</script>




<?php
if ($keyboard) {
?>
<?php
}
?>


<input onclick="sendMessage();" tabindex="3" value="Send!" type="button" style="width: 180px; height: 45px; cursor: pointer;">
</form>


<!-- overlay test -->
<!-- <a href="javascript:parent.openOverlay(name);"><U>Smileys Temp</U></a><br> -->

<!-- old tables -->
<br><br>
<table width="100%">
<tbody>
<tr>
<td align="left" valign="top" width="100%" nowrap><a href="javascript:bbcode_on();"><U>BBCode help</U></a>&nbsp;&nbsp;<a href="javascript:translit_on()"><U>Translit help</U><a></td></tr>
<tr><td align="left" valign="top" width="100%">
<div id="bbcode_help"><table border="1">
<tbody><tr><td>[b]<font color="gray">bolded text</font>[/b]</td><td><strong>bolded text</strong></td></tr>
<tr><td>[i]<font color="gray">italicized text</font>[/i]</td><td><i>italicized text</i></td></tr>
<tr><td>[u]<font color="gray">underlined text</font>[/u]</td><td><span style="text-decoration: underline;">underlined text</span></td></tr>
<tr><td>[s]<font color="gray">strikethrough text[</font>/s]</td><td><del>strikethrough text</del></td></tr>
<tr><td>[url]<font color="gray">http://example.org</font>[/url]</td><td><a target="_blank" href="http://example.org/">http://example.org</a></td></tr>
<tr><td>[url=<font color="gray">http://example.com]Example</font>[/url]</td><td><a target="_blank" href="http://example.com/">Example</a></td></tr>
<tr><td>[quote]<font color="gray">quoted text</font>[/quote]</td><td><q>quoted text</q></td></tr>
<tr><td>[code]<font color="gray">monospaced text[</font>/code]</td><td><code>monospaced text</code></td></tr>
<tr><td>[sarcasm]<font color="gray">reverse italicized text</font>[/sarcasm]</td><td><em>sarcasm</em></td></tr>
<tr><td>[color=red]<font color="gray">Red Text</font>[/color]</td><td><span style="color: red;">Red Text</span></td></tr>
<tr><td>[color=#FF0000]<font color="gray">Red Text</font>[/color]</td><td><span style="color: rgb(255, 0, 0);">Red Text</span></td></tr>
<tr><td>[color=FF0000]<font color="gray">Red Text</font>[/color]</td><td><span style="color: rgb(255, 0, 0);">Red Text</span></td></tr>
<tr><td>[size=15]<font color="gray">Large Text</font>[/size]</td><td><span style="font-size: 15pt;">Large Text</span></td></tr>
<tr><td>[img=<font color="gray">http://<?php print( $host); ?>/images/Tip-Hat.gif</font>]</td><td><img src="http://<?php print( $host); ?>/images/Tip-Hat.gif"></td></tr>
</tbody></table>
</div>
  
<div id="translit_help"><font color="gray"><table><tbody><tr><td>А=<font color="gray">A</font></td><td>Б=<font color="gray">B</font></td><td>В=<font color="gray">V</font></td><td>Г=<font color="gray">G</font></td><td>Д=<font color="gray">D</font></td><td>Е=<font color="gray">E</font></td><td>Ё=<font color="gray">JO</font></td><td>Ж=<font color="gray">ZH</font></td><td>З=<font color="gray">Z</font></td><td>И=<font color="gray">I</font></td></tr>
               <tr><td>Й=<font color="gray">J</font></td><td>К=<font color="gray">K</font></td><td>Л=<font color="gray">L</font></td><td>М=<font color="gray">M</font></td><td>Н=<font color="gray">N</font></td><td>О=<font color="gray">O</font></td><td>П=<font color="gray">P</font></td><td>Р=<font color="gray">R</font></td><td>С=<font color="gray">S</font></td><td>Т=<font color="gray">T</font></td></tr>
               <tr><td>У=<font color="gray">U</font></td><td>Ф=<font color="gray">F</font></td><td>Х=<font color="gray">H</font></td><td>Ц=<font color="gray">C</font></td><td>Ч=<font color="gray">CH</font></td><td>Ш=<font color="gray">SH</font></td><td>Щ=<font color="gray">XH</font></td><td>Ъ=<font color="gray">##</font></td><td>Ы=<font color="gray">Y</font></td><td>Ь=<font color="gray">''</font></td></tr>
			   <tr><td>Э=<font color="gray">W</font></td><td>Ю=<font color="gray">JU</font></td><td>Я=<font color="gray">JA</font></td></tr>
			   <tr><td>а=<font color="gray">a</font></td><td>б=<font color="gray">b</font></td><td>в=<font color="gray">v</font></td><td>г=<font color="gray">g</font></td><td>д=<font color="gray">d</font></td><td>е=<font color="gray">e</font></td><td>ё=<font color="gray">jo</font></td><td>ж=<font color="gray">zh</font></td><td>з=<font color="gray">z</font></td><td>и=<font color="gray">i</font></td></tr>
			   <tr><td>й=<font color="gray">j</font></td><td>к=<font color="gray">k</font></td><td>л=<font color="gray">l</font></td><td>м=<font color="gray">m</font></td><td>н=<font color="gray">n</font></td><td>о=<font color="gray">o</font></td><td>п=<font color="gray">p</font></td><td>р=<font color="gray">r</font></td><td>с=<font color="gray">s</font></td><td>т=<font color="gray">t</font></td></tr>
			   <tr><td>у=<font color="gray">u</font></td><td>ф=<font color="gray">f</font></td><td>х=<font color="gray">h</font></td><td>ц=<font color="gray">c</font></td><td>ч=<font color="gray">ch</font></td><td>ш=<font color="gray">sh</font></td><td>щ=<font color="gray">xh</font></td><td>ъ=<font color="gray">#</font></td><td>ы=<font color="gray">y</font></td><td>ь=<font color="gray">'</font></td></tr>
			   <tr><td>э=<font color="gray">w</font></td><td>ю=<font color="gray">ju</font></td><td>я=<font color="gray">ja</font></td></tr>
		</tbody></table></div><!--</div>-->

<div id="smileys_help" style="display:none;"><?=smileys('body')?></div> <!-- make display style depend on user settings-->    
	</td>
<!--</tr>
<tr> -->
</tr>

</tbody></table>

</td>
</tr>
</table>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>

</div>

<!----  OLD STUFFS --------------->






</body></html>

