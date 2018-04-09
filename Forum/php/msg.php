<?php

require_once('head_inc.php');
require_once('html_head_inc.php');
require_once('custom_colors_inc.php');

$likes = '';
$dislikes = '';
$reads = '';
$thread_owner = false;

/* Set to false to disallow thread owner 
close/open thread */
$managed = true;

?><link rel="stylesheet" type="text/css" href="<?=autoversion('css/diff.css');?>">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.6/styles/default.min.css">

<!-- overreading the custom ribbon background to light grey for this row of buttons -->
<style>
.ribbonIcon:hover { 
    background-color: #cccccc;
    cursor: pointer;
    }
</style>
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.6/highlight.min.js"></script>
<script language="javascript">
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
    reaction += '<img src="' + data.reactions[r].url + '" alt="' + r + '" valign="middle"/>&nbsp;' + data.reactions[r].names.join(", ");
    index++;
  }
  console.log(reaction);
  $('#reaction').html(reaction); // show response from the php script.
}

function react(msg_id, reaction) {
  document.getElementById("react_div").style.display='none';
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
 if(document.getElementById("react_div").style.display=='none')document.getElementById("react_div").style.display='block';
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
function resizeMe(iframe)
{
    iframe.width  = iframe.contentWindow.document.body.scrollWidth;
    iframe.height = iframe.contentWindow.document.body.scrollHeight;
}

function hashtag(text) {
  console.log("hashtag " + text);
  $('input[name="text"]').val(text);
  $('input[name="searchin"]').val(1);
  document.querySelector('form[name="hashtag"]').submit();
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

</script>
<base target="bottom">
</head>
<body><form name="hashtag" id="hashtag" action="dosearch.php" method="post" target="contents">
  <input type="hidden" id="text" name="text"/>
  <input type="hidden" id="searchin" name="searchin"/>
  <input type="hidden" id="fromyear" name="fromyear" value="0"/>
  <input type="hidden" id="toyear" name="toyear" value="0"/>
</form>
<div id="expandMsg" onclick="toggleExpand();parent.expand();" style="float: right;position: relative;width: 0px;top: -20px;right: -5px;cursor: pointer;">
	<svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="grey" d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"></path></g></svg>
</div>
<div id="restoreMsg" onclick="toggleExpand();parent.restore();" style="display: none; float: right;position: relative;width: 0px;top: -20px;right: -5px;cursor: pointer;">
	<svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="red" d="M5 16h3v3h2v-5H5v2zm3-8H5v2h5V5H8v3zm6 11h2v-3h3v-2h-5v5zm2-11V5h-2v5h5V8h-3z"></path></g></svg>
</div>
<?php
    if (is_null($action)) {
    	$query = 'UPDATE confa_posts set views=views + 1 where id=' . $msg_id;
    	$result = mysql_query($query);
    	if (!$result) {
        	mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        	die('Query failed');
    	}
    } 
    if (!is_null($action)) {
      if (!strcmp($action, "like")) {
        if (like($user_id, $msg_id, 1) === false) {
          die('Query failed');
        }
      } else if (!strcmp($action, "dislike")) {
        if (like($user_id, $msg_id, -1) === false) {
          die('Query failed');
        }
      } else if (!strcmp($action, "bookmark")) {
        if (bookmark($user_id, $msg_id, true) == false) {
          die('Query failed');
        }
      } else if (!strcmp($action, "unbookmark")) {
        if (bookmark($user_id, $msg_id, false) == false) {
          die('Query failed');
        }        
      } else if (!strcmp($action, "closethread") || !strcmp($action, "openthread")) {
        $query = "SELECT t.author as t_author, t.properties as t_properties, t.id as thread_id  from confa_threads t, confa_posts p where p.thread_id = t.id and p.id=" . $msg_id;
        $result = mysql_query($query);
        if (!$result) {
             mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
             die('Query failed');
        }
        $row = mysql_fetch_assoc($result);
        if ($user_id == $row['t_author']) {
          $thread_id = $row['thread_id'];
          $value = 0;
          if (!strcmp($action, "closethread")) {
            $value = 1;
          }
          $query = "UPDATE confa_threads set closed=" . $value . " where id=" . $thread_id; 
            $result = mysql_query($query);
            if (!$result) {
                 mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                 die('Query failed');
            }
        }
      } else if (!strcmp($action, "report")) {
        if (report($user_id, $msg_id, $mode) == false) {
          die('Query failed');
        }
      }
   }
   $msg_bookmark = NULL;
   $bookmarks = '';
   if (!is_null($user_id) && is_numeric($user_id)) { 
     $query = 'SELECT b.id, b.user, u.username from confa_bookmarks b, confa_users u where b.user=u.id and b.post=' . $msg_id; 
     $result = mysql_query($query);
     if (!$result) {
        mysql_log( __FILE__ . ":" . __LINE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed');
     }
     while ($row = mysql_fetch_assoc($result)) {
       if ($row['user'] == $user_id) { 
        $msg_bookmark = $row['id'];
       }
       // if (strlen($bookmarks) > 0) $bookmarks .= ', ';
       // $bookmarks .= $row['username'];
     }
   }
    if (!is_null($user_id))require("msg_inc.php");
    else {print("Please login for full experience"); die();}
?>


<!--  Interactive options - react, reply, etc -->


<div id="FooterRibbonGroup" style="border: lightgrey; border-style: solid; border-width: 1px;" class="ribbonGroupMobile" ;="">
      <div id="FooterRibbonGroupTitle" class="ribbonGroupTitle">Message Actions</div> 
      <div id="reactions-dropdown">

<?php
if (isset($reactions)) {
?>
<div class="reactions-dropdown">
<!-- <span><a target="bottom" href="javascript:;">React</a></span> -->
        <span class="ribbonIcon tooltip greyHover" id="ReactIcon">
    	 <a href="javascript:;" onclick="openReactDiv();">
    	        <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
		    <path id="reactIcon" class="ribbonIcon" fill="#000000"  d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"></path></g>
		</svg>
    	    <span style="padding-left: 20px; " class="tooltiptext">React</span>
    	 </a> 
    	</span> 


<div class="reactions-dropdown-content" id="react_div">
<?php
  $icons = '';
  foreach (array_keys($reactions) as $key) {
    if (strlen($icons) > 0) $icons .= '&nbsp;';
    $icons .= '<a href="javascript:react('.$msg_id.',\''.$key.'\');"><img src="http://'.$host.$root_dir.'images/smiles/'.$key.'.gif" alt="'.$key.'" title="'.$key.'" valign="middle"/></a>';
  }
  print($icons);
?></div></div>

<?php } ?>


<?php if ( !$reply_closed ) { ?>
	<span class="ribbonIcon tooltip" id="ReplyIcon">
	 <a href="<?php print($root_dir . $page_new); ?>?re=<?php print($msg_id); ?>">
    	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet">
    		  <g><path class="ribbonIcon" fill="#000000" d="M7 8V5l-7 7 7 7v-3l-4-4 4-4zm6 1V5l-7 7 7 7v-4.1c5 0 8.5 1.6 11 5.1-1-5-4-10-11-11z"></path></g>
    	    </svg>
    	    <span class="tooltiptext">Reply</span>
    	 </a> 
	</span> 
<?php } else {?>

	<span class="ribbonIcon tooltip" id="ReplyIcon">
    	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet">
    		  <g><path class="ribbonIcon" fill="lightgrey" d="M7 8V5l-7 7 7 7v-3l-4-4 4-4zm6 1V5l-7 7 7 7v-4.1c5 0 8.5 1.6 11 5.1-1-5-4-10-11-11z"></path></g>
    	    </svg>
    	    <span class="tooltiptext">Reply closed</span>
	</span> 

<?php } ?>
	<span id="PrivateIcon" class="ribbonIcon tooltip">
	  <a href="<?php print( $root_dir . $page_pmail_send . '?to=' . $author . '&re=' .  $msg_id); ?>">
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet">
	      <g> <path class="ribbonIcon" fill="#000000"  d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"></path></g>
	    </svg>
	    <span class="tooltiptext">Reply privately</span>
	  </a>
	</span> 
	
	<span id="SyncIcon" class="ribbonIcon tooltip">
	 <a target="contents" name="<?php print($msg_id); ?>" href="<?php print($root_dir . $page_expanded); ?>?page=<?php print($msg_page . '#' .$msg_id);?>">
    	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet">
    		<g> <path class="ribbonIcon" fill="#000000" d="M9.01 14H2v2h7.01v3L13 15l-3.99-4v3zm5.98-1v-3H22V8h-7.01V5L11 9l3.99 4z"></path></g>
	    </svg>
	    <span class="tooltiptext">Synchronize</span>
	</a>	
	</span> 
	
	<span id="msgThread" class="ribbonIcon tooltip">
	    <a target="bottom" href="<?php print($root_dir . $page_thread); ?>?id=<?php print($msg_id); ?>">
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
		    <path class="ribbonIcon" fill="#000000" d="M3 18h6v-2H3v2zM3 6v2h18V6H3zm0 7h12v-2H3v2z"></path>
	    </g></svg>
	    <span class="tooltiptext">Thread</span>
	    </a>
	</span> 

  
	<span id="msgBookmark" class="ribbonIcon tooltip">
<?php   
if (is_null($msg_bookmark)) {
        print('<a target="bottom" href="' . $root_dir . $page_msg . '?id=' . $msg_id . '&action=bookmark">');
} else {
        print('<a target="bottom" href="' . $root_dir . $page_msg . '?id=' . $msg_id . '&action=unbookmark">');
}
?>	    
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
		    <path class="ribbonIcon" fill="<?php if (is_null($msg_bookmark)) { ?>#000000"<?php }else { ?> red" <?php } ?> d="M17 3H7c-1.1 0-1.99.9-1.99 2L5 21l7-3 7 3V5c0-1.1-.9-2-2-2z"></path>
	    </g></svg>
	    <span class="tooltiptext"><?php if (is_null($msg_bookmark)) { ?> Bookmark<?php }else { ?> Delete Bookmark <?php } ?></span>
	    </a>
	</span> 
	
        <span id="msgBooks" class="ribbonIcon tooltip"><a onclick="javascript:addToBooks(<?php print($msg_id); ?>);return false;">
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
		    <path id="addBookIcon" class="ribbonIcon" fill="#000000" d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"></path>
	    </g></svg>
	    <span class="tooltiptext">Add to Books</span></a>
	</span> 


        <span id="msgMovies" class="ribbonIcon tooltip"><a onclick="javascript:addToMovies(<?php print($msg_id); ?>);return false;">
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
		    <path id="addMovieIcon"class="ribbonIcon" fill="#000000" d="M18 3v2h-2V3H8v2H6V3H4v18h2v-2h2v2h8v-2h2v2h2V3h-2zM8 17H6v-2h2v2zm0-4H6v-2h2v2zm0-4H6V7h2v2zm10 8h-2v-2h2v2zm0-4h-2v-2h2v2zm0-4h-2V7h2v2z"></path>
	    </g></svg>
	    <span class="tooltiptext">Add to Movies</span></a>
	</span> 

<?php
   if (!$reply_closed && can_edit_post($auth_id, $created_ts, $user_id, $msg_id)) {
	$ancor="";
     if (!is_null($parent) && $parent != 0) {
         $ancor='<a target="bottom" href="' . $root_dir . $page_new . '?id=' . $msg_id . '?&re=' . $parent . '">';
     } else {
         $ancor='<a target="bottom" href="' . $root_dir . $page_new . '?id=' . $msg_id . '">';
     }
   
?>

        <span id="msgEdit" class="ribbonIcon tooltip"><?php print($ancor); ?>
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
		    <path class="ribbonIcon" fill="#000000" d="M3 15.25V19h3.75L15.5 10.5l-3.75-3.75L3 15.25zM18 8c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"></path>
	    </g></svg>
	    <span class="tooltiptext">Edit</span></a>
	</span> 
<?php } ?>




<?php    if (intval($revisions) > 0) {
?>     

	</span> 
	    <span id="msgRevisions" class="ribbonIcon tooltip"><a href="javascript:revisions_on();">
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
		    <path class="ribbonIcon" fill="#000000" d="M11 18V6l-8.5 6 8.5 6zm.5-6l8.5 6V6l-8.5 6z"></path>
	    </g></svg>
	    <span class="tooltiptext">Revisions</span></a>
	</span> 
<?php } ?>



<?php if ($user_id != $auth_id) { ?>

	</span> 
	    <span id="msgReport" class="ribbonIcon tooltip"><?php print('<a target="bottom" href="' . $root_dir . $page_msg . '?id=' . $msg_id . '&action=report&mode=nsfw">'); ?>
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
		    <path class="ribbonIcon" fill="#000000" d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"></path>
	    </g></svg>
	    <span class="tooltiptext">Report NSFW</span>    
	    </a>
	</span> 

<?php
}
?>


	<!-- Moderator -->
<?php    if ( !is_null( $moder ) && $moder > 0 ) { ?>

        <span id="msgMod" class="ribbonIcon tooltip"><a onclick="javascript:showMod();">
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
		    <path class="ribbonIcon" fill="darkred" d="M1 21h12v2H1zM5.245 8.07l2.83-2.827 14.14 14.142-2.828 2.828zM12.317 1l5.657 5.656-2.83 2.83-5.654-5.66zM3.825 9.485l5.657 5.657-2.828 2.828-5.657-5.657z"></path>
	    </g></svg>
	    <span class="tooltiptext">Moderator</span></a>
	</span> 



<script>
function showMod()
{
    if(document.getElementById("msgModCensor").style.display=='none')
    {
	//enable Mod Tools
	document.getElementById("msgModCensor").style.display='inline-block';
	document.getElementById("msgModDel").style.display='inline-block';
	document.getElementById("msgModCloseThread").style.display='inline-block';
	document.getElementById("msgModCloseMsg").style.display='inline-block';
    }
    else
    {
	document.getElementById("msgModCensor").style.display='none';
	document.getElementById("msgModDel").style.display='none';
	document.getElementById("msgModCloseThread").style.display='none';
	document.getElementById("msgModCloseMsg").style.display='none';
    }

}
</script>


        <span id="msgModCensor" class="ribbonIcon tooltip" style="display: none">
<?php        
        if ( $msg_status == 3 ) {
            print( '<a href="' . $root_dir . 'modcensor.php' . '?action=uncensor&id=' . $msg_id . '">' );
        } else {
            print( '<a href="' . $root_dir . 'modcensor.php' . '?action=censor&id=' . $msg_id . '">' );
        }
?>
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
		    <path class="ribbonIcon" fill="darkred" d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"></path>
	    </g></svg>
	    <span class="tooltiptext">
<?php        
        if ( $msg_status == 3 ) {
            print( 'Uncensor' );
        } else {
            print( 'Censor' );
        }
?>
	    </span>
	    </a>
	</span> 

        <span id="msgModDel" class="ribbonIcon tooltip" style="display: none">
<?php
        if ( $msg_status == 2 ) {
            print( ' <a href="' . $root_dir . 'modcensor.php' . '?action=undelete&id=' . $msg_id . '">' );
        } else {
            print( ' <a href="' . $root_dir . 'modcensor.php' . '?action=delete&id=' . $msg_id . '">' );
        }
?>
        
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
		    <path class="ribbonIcon" fill="darkred" d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path>
	    </g></svg>
	    <span class="tooltiptext">
<?php
        if ( $msg_status == 2 ) {
            print( 'Undelete' );
        } else {
            print( 'Delete Msg' );
        }
?>
	    </span></a>
	</span> 

        <span id="msgModCloseThread" class="ribbonIcon tooltip" style="display: none">
<?php
            if ( $thread_closed ) {
                print( ' <a href="' . $root_dir . 'modcensor.php' . '?action=openthread&id=' . $msg_id . '">' );
            } else {
                print( ' <a href="' . $root_dir . 'modcensor.php' . '?action=closethread&id=' . $msg_id . '">' );
            }

?>        
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
		    <path class="ribbonIcon" fill="darkred" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8 0-1.85.63-3.55 1.69-4.9L16.9 18.31C15.55 19.37 13.85 20 12 20zm6.31-3.1L7.1 5.69C8.45 4.63 10.15 4 12 4c4.42 0 8 3.58 8 8 0 1.85-.63 3.55-1.69 4.9z"></path>
	    </g></svg>
	    <span class="tooltiptext">
<?php
            if ( $thread_closed ) {
                print( 'Open Thread' );
            } else {
                print( 'Close Thread' );
            }
?>        
	    </span>
	    </a>
	</span> 

        <span id="msgModCloseMsg" class="ribbonIcon tooltip" style="display: none">
<?php
            if ( $post_closed ) {
                print( ' <a href="' . $root_dir . 'modcensor.php' . '?action=openpost&id=' . $msg_id . '">' );
            } else {
                print( ' <a href="' . $root_dir . 'modcensor.php' . '?action=closepost&id=' . $msg_id . '">' );
            }
?>
	    <svg class="ribbonIcon greyHover" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
		    <path class="ribbonIcon" fill="darkred" d="M9 16h2V8H9v8zm3-14C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm1-4h2V8h-2v8z"></path>
	    </g></svg>
	    <span class="tooltiptext">
<?php
            if ( $post_closed ) {
                print( 'Open Msg' );
            } else {
                print( 'Close Msg' );
            }
?>
	    </span></a>
	</span> 
<?php } ?>
  </div>
</div>

<BR>

<?php
$footer = '<div class="footer">';
// Ratings
$footer .= '<span id="rating">';
if (strlen($bookmarks) > 0) {
  $footer .= ' <FONT color="darkblue">' . $bookmarks . '</FONT>';
}
if (strlen($likes) > 0) {
  $footer .= ' <FONT color="green">' . $likes . '</FONT>';
}
if (strlen($dislikes) > 0) {
   $footer .= ' <FONT color="red">' . $dislikes . '</FONT>';
}
if (strlen($reads) > 0) {
  $footer .= ' <FONT color="lightgray">' . $reads . '</FONT>';
}
$footer .= '</span> ';
// Reactions
$footer .= '<span id="reaction">';
if (sizeof($reaction) > 0) {
  $index = 0;
  foreach (array_keys($reaction) as $key) {
    if (array_key_exists($key, $reaction)) {
      if ($index > 0) {
        $footer .= '&nbsp;';
      }
      $footer .= '<img width="25px" src="http://'.$host.$root_dir.'images/smiles/'.$key.'.gif" alt="'.$key.'" valign="middle"/>&nbsp;'.$reaction[$key].'';
      $index++;
    }
  }
}
$footer .= '</span>';
if (isset($reports) && $reports['boyan'] != '') {
  $footer .= ' <img border=0 src="' . $root_dir . $boyan_img . '" valign="middle"/>&nbsp;<span style="color:gray">' . $reports['boyan'].'</span>';
}
if (isset($reports) && $reports['nsfw'] != '') {
  $footer .= ' <span class="nsfw">NSFW</span>&nbsp;<span style="color:gray">'.$reports['nsfw'].'</span>';
}
$footer .= '</div>';
print($footer);

require_once('msg_hist_inc.php');
require_once('tail_inc.php');
?>
<br/><center style="color:gray"><?php print(chuck(15));?></center>
&nbsp;
</body>
</html>


