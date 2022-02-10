<?php
/*$Id: top.php 378 2009-11-02 19:36:24Z dmitriy $*/
require_once('head_inc.php');
require_once('html_head_inc.php');
require_once('mysql_log.php');

?>
<link rel="stylesheet" type="text/css" href="<?=autoversion('css/disc2.css')?>">
<link rel="stylesheet" type="text/css" href="<?=autoversion('css/common.css')?>">
<link rel="stylesheet" type="text/css" href="<?=autoversion('css/diff.css');?>">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.6/styles/default.min.css">

<script type="text/javascript" src="<?=autoversion('js/controls.js')?>"></script>
<script type="text/javascript" src="js/jquery-ui.js"></script>
<!-- overreading the custom ribbon background to light grey for this row of buttons -->
<style>
.ribbonIcon:hover { 
    background-color: #cccccc;
    cursor: pointer;
}    
#scroll2top {
    top: 4.6em !important;
}
    
</style>
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.6/highlight.min.js"></script>
<script src="js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="<?=autoversion('js/func.js')?>"></script>
<script type="text/javascript" src="<?=autoversion('js/junk.js')?>"></script>
<script>
// msg.php
  function reply() {
    submitReply('frmReply');
  }
  function private() {
    submitReply('frmReplyPrivate');
  }  
  function submitReply(frmName) {
    var f = document.getElementById(frmName);
    f['quote'].value = getQuote();
    f.submit();
  }  
  function showMod() {
    if(document.getElementById("msgModCensor").style.display=='none') {
      //enable Mod Tools
      document.getElementById("msgModCensor").style.display='inline-block';
      document.getElementById("msgModDel").style.display='inline-block';
      document.getElementById("msgModCloseThread").style.display='inline-block';
      document.getElementById("msgModCloseMsg").style.display='inline-block';
    } else {
      document.getElementById("msgModCensor").style.display='none';
      document.getElementById("msgModDel").style.display='none';
      document.getElementById("msgModCloseThread").style.display='none';
      document.getElementById("msgModCloseMsg").style.display='none';
    }
  }
</script>
<script language="javascript">
// msg.php
function report_on() {
  toggleDiv("report");
}

function moderate_on() {
  toggleDiv("moderate") 
}

function toggleDiv(id) {
  var div = document.getElementById(id);  
  if (div != null) {
    if (div.style.display != 'inline')
      div.style.display = 'inline';
    else
      div.style.display = 'none';
  }
}

var test = false;

function load_rating(data) {
  console.log("Loading the rating " + JSON.stringify(data));
  var rating = '<font color="green">';
  var likes = '';
  for (var i=0; i < data.ratings.length; i++) {
    if (data.ratings[i].count > 0) {
      if (likes.length > 0) likes += ',';
      likes += ' ' + data.ratings[i].name;
      if (data.ratings[i].count > 1) 
      likes += '(' + data.ratings[i].count + ')';
    }
  }
  rating += likes + '</font><font color="red">';
  likes = '';
  for (var i=0; i < data.ratings.length; i++) {
    if (data.ratings[i].count < 0) {
      if (likes.length > 0) likes += ',';
      likes += ' ' + data.ratings[i].name;
      if (data.ratings[i].count < -1) 
      likes += '(' + (-data.ratings[i].count) + ')';
    }
  }
  rating += likes + '</font><font color="lightgray">';
  likes = '';
  for (var i=0; i < data.ratings.length; i++) {
    if (data.ratings[i].count == 0) {
      if (likes.length > 0) likes += ',';
      likes += ' ' + data.ratings[i].name;
    }
  }
  rating += likes + '</font> ';
  console.log(rating);
  $('#rating').html(rating); // show response from the php script.
}
    
function like(msg_id, rating) {
  var method = rating > 0 ? "PUT" : "DELETE";
  var action = "/api/messages/"  + msg_id + "/like";
  console.log(method + " " + action);
  if (test) {
    load_rating({ratings:[{name:"name1",count:1},{name:"name2",count:-2},{name:"name3",count:2}, {name:"name4", count:0}]});
  } else {
    $.ajax({
      type: method,
      url: action,
      success: load_rating
    });
  }
}

function load_reaction(data) {
  console.log("Loading the reaction " + JSON.stringify(data));
  var reaction = '';
  var index = 0;
  for (var r in data.reactions) {
    if (index > 0) reaction+='&nbsp';
    reaction += '<img src="' + data.reactions[r].url + '" alt="' + r + '"/>&nbsp;' + data.reactions[r].names.join(", ");
    index++;
  }
  console.log(reaction);
  $('#reaction').html(reaction); // show response from the php script.
}

function react(msg_id, reaction) {
  console.log(reaction + " to " + msg_id);
  document.getElementById("react_div").style.display='none';
  console.log("...");
  var method = "PATCH";
  var action = "/api/messages/"  + msg_id + "/reactions/" + reaction;
  console.log(method + " " + action);
  $.ajax({
    type: method,
    url: action,
    success: load_reaction
  });
}

function openReactDiv()
{
 if(document.getElementById("react_div").style.display=='none'||document.getElementById("react_div").style.display=='' )document.getElementById("react_div").style.display='block';
 else document.getElementById("react_div").style.display='none';
}

function toggleExpand()
{
    if(document.getElementById("expandMsg").style.display=='none')
    {
	//enable expanding
	document.getElementById("expandMsg").style.display='block';
	document.getElementById("restoreMsg").style.display='none';
	parent.restore();
    }
    else
    {
	document.getElementById("expandMsg").style.display='none';
	document.getElementById("restoreMsg").style.display='block';
	parent.expand();
	
    }
}
function initExpand() 
{
  if (parent.expanded && parent.expanded()) 
  {
    document.getElementById("expandMsg").style.display='none';
    document.getElementById("restoreMsg").style.display='block';    
  }
  else
  {
    document.getElementById("expandMsg").style.display='block';
    document.getElementById("restoreMsg").style.display='none';
  }  
}
function resizeMe(iframe)
{
    iframe.width  = iframe.contentWindow.document.body.scrollWidth + 5;
    iframe.height = iframe.contentWindow.document.body.scrollHeight + 25;
}

function hashtag(text) {
  console.log("hashtag " + text);
  $('input[name="text"]').val(text);
  $('input[name="searchin"]').val(1);
  document.querySelector('form[name="hashtag"]').submit();
}

function pinThread(threadID)
{
  var user_id = <?php print($user_id);  ?>;
  //add the thread to pinned
    $.ajax({
            type: "POST",
            url: "pin_api.php",
            data: {user: user_id, thread: threadID, action: 'pin'} ,
            success: function(data) {
            if(data.includes('thread pinned')){document.getElementById("pinThreadIcon").style.fill='red';document.getElementById("pin_title").innerHTML="Unpin Thread";}
            else {document.getElementById("pinThreadIcon").style.fill='black';document.getElementById("pin_title").innerHTML="Pin Thread";}
            //reload top frame
            window.open('threads.php', 'contents');
            }
         });
}

function addToBooks(msgid)
{
  var user_id = <?php print($user_id);  ?>;
  var message_id=msgid;
  //add the msgid to books
    $.ajax({
            type: "POST",
            url: "books_api.php",
            data: {user: user_id, msg: message_id} ,
            success: function(data) {
            //alert("Thank you for sharing the book!");
            document.getElementById("addBookIcon").style.fill='red';
            }
         });
}

function addToMovies(msgid)
{
  var user_id = <?php print($user_id);  ?>;
  var message_id=msgid;
  //add the msgid to movies
    $.ajax({
            type: "POST",
            url: "movies_api.php",
            data: {user: user_id, msg: message_id} ,
            success: function(data) {
            //alert("Thank you for sharing the movie!");
            document.getElementById("addMovieIcon").style.fill='red';
            }
         });
}

function loadimage(img) {
 setTimeout( function() {
  img.style.opacity = 1;
  var downloadingImage = new Image();
  downloadingImage.onload = function() {
    img.src = this.src;
  };
  downloadingImage.src = img.alt;
  //img.src = img.alt;
 }, 500);
}

</script>
<script src="js/tenorgif.js"></script>
<script>
// new.php
function toggleExpand()
{
    if(document.getElementById("expandMsg").style.display=='none')
    {
      //enable expanding
      document.getElementById("expandMsg").style.display='block';
      document.getElementById("restoreMsg").style.display='none';
      parent.restore();
    }
    else
    {
      document.getElementById("expandMsg").style.display='none';
      document.getElementById("restoreMsg").style.display='block';
      parent.expand();	
    }
}

function initExpand() 
{
  var a = navigator.userAgent||navigator.vendor||window.opera; // agent
  
  if (parent.expanded && parent.expanded()) 
  {
    document.getElementById("expandMsg").style.display='none';
    document.getElementById("restoreMsg").style.display='block';    
  }
  else if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) {
      // if mobile, expand
      toggleExpand();
  } 
  else
  {
    document.getElementById("expandMsg").style.display='block';
    document.getElementById("restoreMsg").style.display='none';
  }  
}

var changes = false;

function beforeunload() {
    if (changes)
    {
        var message = "Are you sure you want to navigate away from this page?\n\nYou have started writing or editing a post.\n\nPress OK to continue or Cancel to stay on the current page.";
        if (confirm(message)) return true;
        else return false;
    }      
}
function changed(flag) {
  changes = flag;
}

</script>

<script>
// new_inc.php
    var imageGallery='<?=$imageGallery?>';
    var imageGalleryUploadOn=false;

    document.addEventListener("DOMContentLoaded", function(event) { 
        var nsfw  = document.getElementById("nsfw");
        if (nsfw && nsfw.checked) document.getElementById("nsfwPath").style.fill="red";
    });
    function toggleNsfw()
    {
      var nsfw = document.getElementById("nsfw");
      if (nsfw) nsfw = nsfw.checked;
      
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

function showPreview()
{
       console.log("attempt to open preview");
       openOverlay('preview');
       /*
       updateFrame("bottom", function () {
       });
       */
}

function sendMessage()
{
    changed(false);
    //validate the subj
    if(document.getElementById("subj").value=='')
    {
        document.getElementById("subj_div").style.color='red';
        return;
    }
    document.getElementById("msgform").submit();
}
</script>
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
<script>
function expand()
{
  verticalLayout = getCookie("verticalLayout");
  if(verticalLayout=='true') return;
  document.getElementById("frame1").style.height='0vh';
  document.getElementById("frame2").style.height='88vh';
  document.getElementById("hr1").style.display='none';
}
function restore()
{
  verticalLayout = getCookie("verticalLayout");
  if (verticalLayout=='true') return;
  document.getElementById("frame1").style.height='40vh';
  document.getElementById("frame2").style.height='48vh';
  document.getElementById("hr1").style.display='block';
}
function expanded() 
{
  return document.getElementById("hr1").style.display == 'none';
}
function toggle() 
{
  if (expanded()) {
    restore();
  } else {
    expand();
  }
}
// from threads.php
var selected_id = "";
var previewInProgress=false; 

function selectMsg(id) {
  console.log('selectMsg id=' + id);
  var sp_id = "sp_" + id;
  if (selected_id != "") {
  // reset selection
  //console.log('resetting selected id=' + selected_id);
  var selected = document.getElementById(selected_id);
  if (selected != null) {
  selected.className = null;
  }
 }
 var selected = document.getElementById(sp_id);
 if (selected != null) {
  //console.log('selected element id=' + id);
  // select message
  selected.className = "selected";
  selected_id = sp_id;
  /*
  // prevent navigation by href
  document.getElementById(id).removeAttribute("href");
  document.getElementById(id).removeAttribute("target");
  // show message
  showMsg(id);
  // suppress navigation
  return false;
  */
 } else {
    // message not found
    selected_id = "";
    //console.log('id=' + id + "not found");
 }
}

function showMsg(msgId) {
 console.log("loading msg " + msgId);
 $.ajax({
         type: "GET",
         url: "msg.php?id="+ msgId,
         success: function(data)
       {
          // var html = '<h3>' + data.subject + '</h3> Author: <b>' + data.author.name  +'</b> [' + data.views + ' views] ' + data.created + '<br><hr>' + data.body.html;
          var html = data.substring(data.indexOf(">", data.indexOf("<body") + 1) + 1, data.indexOf("</body>"));
          document.getElementById("bottom").innerHTML = html;
       }
    });  
}

function previewMsg(msgId)
{
 if (document.getElementById("postPreview")==null) {
   console.log("nowhere to preview");   
   return; //nowhere to preview
 }
 document.getElementById("postPreview").innerHTML='';
 document.getElementById("postPreview").style.display = "block";
 if (previewInProgress) {
   console.log("preview in progress");
   return;//something else is being queried. Don't compete.
 }
 //retreive from api/messages/xxxxxx
 previewInProgress=true;
 total_count=0;
 console.log("loading preview for " + msgId);
 $.ajax({
         type: "GET",
         url: "./api/messages/"+ msgId,
         success: function(data)
       {
        renderPreview(data);
       }
    });
}

function renderPreview(data)
{
    var html = '<h3>' + data.subject + '</h3> Author: <b>' + data.author.name  +'</b> [' + data.views + ' views] ' + data.created + '<br><hr>' + data.body.html;
    document.getElementById("postPreview").innerHTML = html;
    previewInProgress=false;
}
function clearPreview()
{
  if(document.getElementById("postPreview")==null)return; //nowhere to preview
  document.getElementById("postPreview").style.display = "none";
}
function scroll2Top3() {
  if (parent.scroller2Top) {
    parent.scroller2Top();
    return false;
  }
  
  $([document.documentElement, document.body]).animate({
        scrollTop: $("#up").offset().top
    }, 200);
    
  return false;
}

var updateInProgress = false;

// new stuff
function loadPage(iframe) {
  if (updateInProgress) return;
  var name = "_" + iframe.id;
  console.log("target div " + name);
  var div = document.getElementById(name);
  if (div == null) return; //nowhere to load
  console.log("loading content of " + iframe.id + " to " + div.id);
  var html = iframe.contentWindow.document.body.innerHTML;
  if (html) {
    div.innerHTML = html;
    div.scrollTop = 0;
    iframe.contentWindow.document.body.innerHTML = "";
    // hack for arrow position
    if (document.getElementById("scroll2top")) {
      document.getElementById("scroll2top").style.top='4.6em !important';
    }
  }
}

function updateFrame(name, callback) {
  var iframe = document.getElementById(name);
  var name = "_" + iframe.id;
  console.log("update from div " + name);
  var div = document.getElementById(name);
  if (div == null) return; //nowhere to load
  console.log("loading content of " + iframe.id + " from " + div.id);
  var html = div.innerHTML;
  updateInProgress = true;
  iframe.contentWindow.document.body.innerHTML = html;  
  setTimeout( function() {
    updateInProgress = false;
    callback();
  }, 200);
}

// fast loading:
// 1) declare onbeforeunload for iframe's 
// 2) in handler, 
function beforeUnload(frame, event) {
  console.log("beforeunload " + frame.name + " <- " + document.activeElement.name);
  var href = document.activeElement.href;
  var target = document.activeElement.target;
  console.log("beforeunload " + frame.name + " <- " + href + " (" + target + ")");
  
  // prevent navigation
  event.preventDefault();
  event.stopPropagation();
  //event.returnValue = "";
  
  return false;
 }

function attachUnloadListener(id) {
  var iframe = document.getElementById(id);
  if (iframe) {
      iframe.contentWindow.onbeforeunload = function (event) {
        beforeUnload(document.getElementById(id), event);
      };
      console.log("attached onbeforeunload to " + id);
  }  
}
</script>
<title><?=$title?></title>
</head>
<body id="html_body" style="height:100%;">
<div id="confaFrameContainer" style="position: absolute; height:100%; overflow-y:hidden; width: 100%">
<?php
require('menu_inc.php');
?>
<div id="frame1" style="position: static; height: calc(50% - 74px); background-color: white;display: inline-block;width: 100vw;">
    <iframe style="border: none; display: none" tabindex="-1" width="0" height="0" name="contents" id="contents" onload="loadPage(this)" onbeforeunload="beforeUnload(this)"></iframe>
    <div name="_contents" id="_contents" style="overflow-y:scroll; height:100%; width:100%;">
<?php
 require('threads.php');
?> 
    </div>
</div>
<hr id="hr1"><!--
<div id="slider-area">
    <div id="slider" class="draggable ui-widget-content"><svg viewBox="0 0 24 24" preserveAspectRatio="xMidYMid meet" style="pointer-events: none; display: block; width: 100%; height: 100%;"><g><path id="resizer" fill="grey" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM6.5 9L10 5.5 13.5 9H11v4H9V9H6.5zm11 6L14 18.5 10.5 15H13v-4h2v4h2.5z"></path></g></svg></div>
</div> -->
<div id="frame2" style="position: relative;height: 49%; background-color: white;display: inline-block; width: 100vw;">
    <iframe style="border: none; display: none" tabindex="-1" width="0" height="0" name="bottom" id="bottom" onload="loadPage(this)"></iframe>
    <div name="_bottom" id="_bottom" style="overflow-y: auto; height:100%; margin-left:8px; font-family: Verdana, Arial; font-size: 10pt;">
<?php
 require('welc.php');
?>
    </div>    
</div>
<?php
require('gallery_inc.php');
require('overlay_inc.php');
require_once('tail_inc.php');
?>
</div>
</body>
<script>
  attachUnloadListener("contents");
  attachUnloadListener("bottom");
</script>
</html>
