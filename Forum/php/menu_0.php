<?php require_once('custom_colors_inc.php'); ?>
<script type="text/javascript" src="<?=autoversion('js/threads_autoload2.js')?>"></script>
<script>
//"
$(document).keyup(function(e) {
     if (e.keyCode == 27) { // escape key maps to keycode `27`
         //close various overlays
         if(document.getElementById("NotificationsContainer").style.display=='block')
         {
                document.getElementById("NotificationsContainer").style.display='none';
         }
         if(document.getElementById("gallery").style.display=='block')
         {
                document.getElementById("gallery").style.display='none';
                document.getElementById("menu_cover").style.display='none';
         }
     }
 });

function resetBadges()
{
    document.getElementById('newNotificationsBadge').style.display = 'none';
    document.getElementById('newPostsBadge').style.display = 'none';
    document.getElementById('newAnswersBadge').style.display = 'none';
    document.title = '<?=$title?>';
}
function openLoginForm()
{

     openOverlay("loginForm");
    /*
    if(document.getElementById("loginForm").style.display=='none')
    {
        document.getElementById("loginForm").style.display='block';
        document.getElementById("loginIcon").style.fill='red';
    }
    else
    {
        document.getElementById("loginForm").style.display='none';
        document.getElementById("loginIcon").style.fill='white';    
    }
    */
}

function expandModeratorMenu()
{
    document.getElementById("ModRibbonGroup").style.display='inline-block';
    document.getElementById("OpenModRibbonGroup").style.display='none';
}

function collapseModeratorMenu()
{
    document.getElementById("ModRibbonGroup").style.display='none';
    document.getElementById("OpenModRibbonGroup").style.display='inline-block';
}

function expandFindMenu()
{
    document.getElementById("FindRibbonGroup").style.display='inline-block';
    document.getElementById("OpenFindRibbonGroup").style.display='none';
}

function collapseFindMenu()
{
    document.getElementById("FindRibbonGroup").style.display='none';
    document.getElementById("OpenFindRibbonGroup").style.display='inline-block';
}

function openMenu()
{
    closeNotifications();
    //close menu if already open
    if(document.getElementById("HamburgerContainer").style.display=='none')document.getElementById("HamburgerContainer").style.display='block';
    else document.getElementById("HamburgerContainer").style.display='none';
}
function closeMenu()
{
    document.getElementById("HamburgerContainer").style.display='none';
}

function openNotifications()
{
    closeMenu();
    if(document.getElementById("NotificationsContainer").style.display=='none')
    {
        //restart the timer:
        try{update_bydate_counter();}catch(err){}

	//events API
	checkForEvents();

	document.getElementById("NotificationsContainer").style.display='block';
	//render timestamps
	var rd = new Date();
	render_time = rd.getTime();
	if(render_time>check_time)
	{
	    //opened after the refresh
	    diffTime=render_time-check_time;
	
	    var minutes = Math.floor(diffTime / 60000);
	    var seconds = ((diffTime % 60000) / 1000).toFixed(0);
	    if(minutes==0)minutes='';
	    else minutes= minutes + ' minutes ';
	    seconds = seconds + ' seconds ago.';
	    document.getElementById('newPostsTime').innerHTML =  "Checked "  + minutes + seconds;
	    document.getElementById('newAnswersTime').innerHTML = "Checked "  + minutes + seconds;
	    document.getElementById('newPMTime').innerHTML = "Checked "  + minutes + seconds; 
	    
	    /*
	    //pmail is different, don't ask why...
	    var pmailTime = <?php print(time()); ?> * 1000;
	    diffTime = render_time - pmailTime;
	    minutes = Math.floor(diffTime / 60000);
	    seconds = ((diffTime % 60000) / 1000).toFixed(0);
	    if(minutes==0)minutes='';
	    else minutes= minutes + ' minutes ';
	    seconds = seconds + ' seconds ago.';
	    document.getElementById('newPMTime').innerHTML = "Checked " + minutes + seconds;
	    */

	}
    }
    else
    {
	closeNotifications();
    }
}

function checkForEvents()
{
    //this function checks the notifications api periodically
    document.getElementById('events').innerHTML="";
    var url1 = "./notifications_api.php?userid=<?=$user_id?>&number=20";
    var me = "<?=$user?>";

    //console.log("calling events api: ("+url1+")");    
    $.ajax({
             type: "GET",
             url: url1,
             success: function(events) {
             var eventsArray = $.map(events, function(value, index) {
                 return [value];
                 });
             var count = eventsArray.length;
             //alert (count);
             //add the last 20 events to the notifications div
             if(count < 1)return; //no events

             for(i=0; i<count; i++)
             {
               //check if this was me. no need to nofify myself lol
               if(eventsArray[i][3] != me)
               {
                 var templateLi = document.getElementById('eventTemplateLi');
                 var newEventLi = templateLi.cloneNode(true); // true means clone all childNodes and all event handlers
                 newEventLi.id = "event_" + i;
                 //calculate time diff
                 var eventDate = new Date(eventsArray[i][1]);
                 var now = new Date();
                 var nowOffsetMs = now.getTimezoneOffset();
                 nowOffsetMs = nowOffsetMs * 60 * 1000;
                 var eventDateMs = eventDate.getTime();
                 //correcting the time zone
                 eventDateMs = eventDateMs - nowOffsetMs;
                 var nowMs = now.getTime();
                 diff= msToTime(nowMs - eventDateMs);

                  if(eventsArray[i][0]=='0')
                 {
                    //reaction event
                     var templateLi = document.getElementById('eventTemplateLi');
                     var newEventLi = templateLi.cloneNode(true); // true means clone all childNodes and all event handlers
                     newEventLi.id = "event_" + i;

                     var title =  eventsArray[i][2];
                     if(title.length >40)title=title.substring(0, 45) + "...";
                     var msg = "<span class='notificationMessage' style='font-weight: bold;'>" + eventsArray[i][3] + "</span><span class='notificationMessage'> reacted to your post </span><span class='notificationMessage' style='font-style: italic;'>" + title + "</span>";
                     newEventLi.querySelector("#notificationMessage").innerHTML = msg;
                     newEventLi.querySelector("#eventTime").innerHTML = "Happened " + diff + " ago.";
                     newEventLi.setAttribute("onclick", "openMessage(" + eventsArray[i][4] + ");");

                     newEventLi.style.display = "block";
                     document.getElementById('events').appendChild(newEventLi);
                 }
                  if(eventsArray[i][0]=='1')
                 {
                    //new thread event
                    var templateLi = document.getElementById('newThreadEventTemplateLi');
                    var newEventLi = templateLi.cloneNode(true); // true means clone all childNodes and all event handlers
                    newEventLi.id = "event_" + i;

                    var title =  eventsArray[i][2];
                    if(title.length >40)title=title.substring(0, 50) + "...";
                    var msg = "<span class='notificationMessage' style='font-weight: bold;'>" + eventsArray[i][3] + "</span><span class='notificationMessage'> started a new thread </span><span class='notificationMessage' style='font-style: italic;'>" + title + "</span>";
                    newEventLi.querySelector("#notificationMessage").innerHTML = msg;
                    newEventLi.querySelector("#eventTime").innerHTML = "Happened " + diff + " ago.";
                    newEventLi.setAttribute("onclick", "openMessage(" + eventsArray[i][4] + ");");

                    newEventLi.style.display = "block";
                    document.getElementById('events').appendChild(newEventLi);
                 }
                  if(eventsArray[i][0]=='2')
                 {
                    //new public Reply event
                    var templateLi = document.getElementById('newReplyEventTemplateLi');
                    var newEventLi = templateLi.cloneNode(true); // true means clone all childNodes and all event handlers
                    newEventLi.id = "event_" + i;

                    var title =  eventsArray[i][2];
                    if(title.length >40)title=title.substring(0, 50) + "...";
                    var msg = "<span class='notificationMessage' style='font-weight: bold;'>" + eventsArray[i][3] + "</span><span class='notificationMessage'> replied to your post </span><span class='notificationMessage' style='font-style: italic;'>" + title + "</span>";
                    newEventLi.querySelector("#notificationMessage").innerHTML = msg;
                    newEventLi.querySelector("#eventTime").innerHTML = "Happened " + diff + " ago.";
                    newEventLi.setAttribute("onclick", "openMessage(" + eventsArray[i][4] + ");");

                    newEventLi.style.display = "block";
                    document.getElementById('events').appendChild(newEventLi);
                 }
                  if(eventsArray[i][0]=='3')
                 {
                    //new public Bookmark event
                    var templateLi = document.getElementById('newBookmarkEventTemplateLi');
                    var newEventLi = templateLi.cloneNode(true); // true means clone all childNodes and all event handlers
                    newEventLi.id = "event_" + i;

                    var title =  eventsArray[i][2];
                    if(title.length >40)title=title.substring(0, 50) + "...";
                    var msg = "<span class='notificationMessage' style='font-weight: bold;'>" + eventsArray[i][3] + "</span><span class='notificationMessage'> bookmarked your post </span><span class='notificationMessage' style='font-style: italic;'>" + title + "</span>";
                    newEventLi.querySelector("#notificationMessage").innerHTML = msg;
                    newEventLi.querySelector("#eventTime").innerHTML = "Happened " + diff + " ago.";
                    newEventLi.setAttribute("onclick", "openMessage(" + eventsArray[i][4] + ");");

                    newEventLi.style.display = "block";
                    document.getElementById('events').appendChild(newEventLi);
                 }
               }
             }
           }
    });
}

function msToTime(duration) {
    var milliseconds = parseInt((duration%1000)/100)
    , seconds = parseInt((duration/1000)%60)
    , minutes = parseInt((duration/(1000*60))%60)
    , hours = parseInt((duration/(1000*60*60))%24)
    , days = parseInt((duration/(1000*60*60*24))%365);

    hours = (hours < 10) ? "0" + hours : hours;
    minutes = (minutes < 10) ? "0" + minutes : minutes;
    seconds = (seconds < 10) ? "0" + seconds : seconds;
    if(hours=='00'){hours=''}else{hours= hours + ' hours ';}
    if(minutes=='00'){minutes='';}else{minutes= minutes + ' minutes ';}
    if(hours!=''){seconds='';}else{seconds=seconds + ' seconds';}
    if(days == 0){days='';}else{ days= days + ' days '; minutes=''; seconds='';}

    return days + hours +  minutes + seconds;
}
function openMessage(id)
{
 window.frames["bottom"].location = "msg.php?id=" + id;
 closeNotifications();
 closeMenu();
}
function openNewMessages()
{
    //open by date
    window.frames["contents"].location = "bydate.php";
    //open answered as well, since it's a subset. There must be a better way to reset the badge, TODO!
    document.getElementById("overley_iframe").src= "answered.php";
    closeNotifications();
    closeMenu();
    resetBadges();
}
function openAnswered()
{
    window.frames["contents"].location = "answered.php";
    closeMenu();
    closeNotifications();
    resetBadges();
}

function openPolls()
{
    window.frames["contents"].location = "polls.php";
    closeMenu();
    closeNotifications();
    resetBadges();
}

function openBooks()
{
    window.frames["contents"].location = "books.php";
    closeMenu();
    closeNotifications();
    resetBadges();
}

function openMovies()
{
    window.frames["contents"].location = "movies.php";
    closeMenu();
    closeNotifications();
    resetBadges();
}

function closeNotifications()
{
    if(document.getElementById('newNotificationsBadge')==null)return; //not logged in
    if(document.getElementById('NotificationsContainer').style.display!='none')document.getElementById("NotificationsContainer").style.display='none';
    //clear badges
    document.getElementById('newAnswersBadge').style.display = 'none';
    document.getElementById('newNotificationsBadge').style.display = 'none';
    document.getElementById('newPostsBadge2').innerHTML= 'no';
    document.getElementById('newAnswersBadge2').innerHTML ='no';

    //clear notifications TODO:
    if(document.getElementById('newPostsBadge').style.display != 'none')
    {
     window.setTimeout( function() 
     {
        console.log("calling clearBydate api");
        $.ajax({
             type: 'GET',
             url: './api/clearBydate',
             success: function(events) {}
       });

     }, 1000 );
    document.getElementById('newPostsBadge').style.display = 'none';
    }
}

function openPM()
{
 pmCounter=0; //thread autoload won't highlight the pm badge again when the time comes (unless there are new pm between now and the next clock tic)
 openOverlay('pm');
 document.getElementById('newPMBadge').innerHTML = 0;
 document.getElementById('pmNotificationMessage').innerHTML =  'No new PMs since you last checked';
 document.getElementById('newNotificationsBadge').style.display = 'none';          
 document.getElementById('newPMBadge').style.display = 'none';
 document.getElementById('newPMBadge').innerHTML='0';
 closeMenu();
}

function openProfile()
{
 openOverlay('profile');
 closeMenu();
}
</script>
<div id="Ribbon" class="ribbon" style="background-color: <?=$ribbonBackground?>; color:<?=$ribbonColor?>;">
<?php if (isset($title) && $title != null) { ?>
	<div id="ForumTitle"  onclick="window.location='top.php';" class="ribbonGroup"; style="width: 120px;height: 37px;padding-top: 4px;text-align: center;vertical-align: top; font-size: x-large;padding-top: 11px; border: <?=$groupBorder?>; border-style: solid; border-width: 1px; cursor: pointer">
<?php if (isset($banner) && !is_null($banner)) { ?>
<img src="<?=$banner?>" alt="<?='Welcome'?>"/>
<?php } else { print($title); }?>
</div>
<?php }?>



<?php if ($logged_in) { ?>


	<div id="NotificationsRibbonGroup" style="border: <?=$groupBorder?>; border-style: solid; border-width: 1px;" class="ribbonGroupMobile">
		<div id="NotificationsRibbonGroupTitle" class="ribbonGroupTitle">Forum</div>
		<div id="NotificationsRibbonGroupIconContainer">

			<span id="NotificationsIcon" class="ribbonIcon tooltip mobileOnly"> <a onclick="openMenu();">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
				<path fill="<?=$ribbonColor ?>" d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"></path>
				</g></svg>
				<span class="tooltiptext">Menu</span></a>
			</span> 

			<span id="NotificationsIcon" class="ribbonIcon tooltip"><a onclick="openNotifications();">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
				<path fill="<?=$ribbonColor ?>" d="M7.58 4.08L6.15 2.65C3.75 4.48 2.17 7.3 2.03 10.5h2c.15-2.65 1.51-4.97 3.55-6.42zm12.39 6.42h2c-.15-3.2-1.73-6.02-4.12-7.85l-1.42 1.43c2.02 1.45 3.39 3.77 3.54 6.42zM18 11c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2v-5zm-6 11c.14 0 .27-.01.4-.04.65-.14 1.18-.58 1.44-1.18.1-.24.15-.5.15-.78h-4c.01 1.1.9 2 2.01 2z" class="style-scope iron-icon"></path>
				</g></svg>
				<span class="tooltiptext">Notifications</span>
				<!-- This is a badge sample that indicates that there are new notifications -->
				<span id="newNotificationsBadge" class="button__badge" style="display:none;">4</span></a>
			</span> 
			<span id="NewThreadIcon" class="ribbonIcon tooltip"><a target="bottom" onclick="closeNotifications();" href="<?=$root_dir.$page_new?>">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M3 15.25V19h3.75L15.5 10.5l-3.75-3.75L3 15.25zM18 8c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"></path></g></svg>
				<span class="tooltiptext">New thread</span></a>
			</span> 

		</div>
	</div>

<?php }?>
	<div id="ViewRibbonGroup" style="border: <?=$groupBorder?>; border-style: solid; border-width: 1px;" class="ribbonGroup";>
		<div id="ViewRibbonGroupTitle1" class="ribbonGroupTitle">View</div>
		<div id="ViewRibbonGroupIconContainer">
			<span id="Refresh" class="ribbonIcon tooltip"><a target="contents" onclick="closeNotifications();" href="<?=$root_dir.$page_expanded?>">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
				<path fill="<?=$ribbonColor ?>" d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"></path>
				</g></svg>
				<span class="tooltiptext">Refresh</span></a>
			</span> 
			<span id="Collapse" class="ribbonIcon tooltip"><a target="contents" onclick="closeNotifications();contents.toggleAll();">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M4 10.5c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5 1.5-.67 1.5-1.5-.67-1.5-1.5-1.5zm0-6c-.83 0-1.5.67-1.5 1.5S3.17 7.5 4 7.5 5.5 6.83 5.5 6 4.83 4.5 4 4.5zm0 12.17c-.74 0-1.33.6-1.33 1.33s.6 1.33 1.33 1.33 1.33-.6 1.33-1.33-.59-1.33-1.33-1.33zM7 19h14v-2H7v2zm0-6h14v-2H7v2zm0-8v2h14V5H7z"></path></g></svg>
				<span id="toggle" class="tooltiptext">Collapse</span></a>
			</span> 

			<span id="Horizontal" class="ribbonIcon tooltip hideOnMobile" style="display: none"><a onclick="closeNotifications();setHorizontalLayout();">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M4 18h17v-6H4v6zM4 5v6h17V5H4z"></path></g></svg>
				<span class="tooltiptext">Horizontal</span></a>
			</span> 
			<span id="Vertical" class="ribbonIcon tooltip hideOnMobile"><a  onclick="closeNotifications();setVerticaLayout();">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M6 5H3c-.55 0-1 .45-1 1v12c0 .55.45 1 1 1h3c.55 0 1-.45 1-1V6c0-.55-.45-1-1-1zm14 0h-3c-.55 0-1 .45-1 1v12c0 .55.45 1 1 1h3c.55 0 1-.45 1-1V6c0-.55-.45-1-1-1zm-7 0h-3c-.55 0-1 .45-1 1v12c0 .55.45 1 1 1h3c.55 0 1-.45 1-1V6c0-.55-.45-1-1-1z"></path></g></svg>
				<span class="tooltiptext">Vertical</span></a>
			</span> 
<!--
			<span id="Phone" class="ribbonIcon tooltip"><a target="_top" href="mobile/index.html">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M20.1 7.7l-1 1c1.8 1.8 1.8 4.6 0 6.5l1 1c2.5-2.3 2.5-6.1 0-8.5zM18 9.8l-1 1c.5.7.5 1.6 0 2.3l1 1c1.2-1.2 1.2-3 0-4.3zM14 1H4c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-2-2-2zm0 19H4V4h10v16z"></path></g></svg>
				<span class="tooltiptext">Phone</span></a>
			</span>
			<span id="Refresh" class="ribbonIcon tooltip"><a target="_top" class="menu" href="<?php print($root_dir . $cur_page); if (!strcmp($cur_page, $page_byuser)) {print('?author_id=' . $author_id); }else {if (/*!strcmp($cur_page, $page_expanded) && */!is_null($page)){ print('?page=' . $page); } } ?>">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"></path></g></svg>
				<span class="tooltiptext">Refresh</span></a>
			</span>
-->
		</div>
	</div>  

<?php if ($logged_in) { ?>

	<div id="OpenFindRibbonGroup" style="border: <?=$groupBorder?>; border-style: solid; border-width: 1px;" class="ribbonGroup">
		<div id="OpenFindRibbonGroupTitle" class="ribbonGroupTitle">Stuff</div>
		<div id="OpenFindRibbonGroupIconContainer">
			<span id="OpenFindIcon" class="ribbonIcon tooltip"><a target="bottom" onclick="expandFindMenu();">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet">
				<g><path fill="<?=$ribbonColor ?>" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path></g>
				</svg>
				<span class="tooltiptext">Stuff</span></a>
			</span>
		</div>
	</div>

	<div id="FindRibbonGroup" style="display: none; border: <?=$groupBorder?>; border-style: solid; border-width: 1px;" class="ribbonGroup">
		<div id="FindRibbonGroupTitle" class="ribbonGroupTitle">Stuff</div>
		<div id="FindRibbonGroupIconContainer">
			<span id="ModCollapse" class="ribbonIcon tooltip"><a onclick="collapseFindMenu();">
				<svg class="ribbonIcon" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="#ffffff" d="M15.41 16.09l-4.58-4.59 4.58-4.59L14 5.5l-6 6 6 6z" class="style-scope iron-icon"></path></g></svg>
				<span class="tooltiptext">Hide</span></a>
			</span>
			<span id="MyMessages" class="ribbonIcon tooltip"><a target="contents" onclick="closeNotifications();" href="<?=$root_dir.$page_my_messages?>">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M20 0H4v2h16V0zM4 24h16v-2H4v2zM20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-8 2.75c1.24 0 2.25 1.01 2.25 2.25s-1.01 2.25-2.25 2.25S9.75 10.24 9.75 9 10.76 6.75 12 6.75zM17 17H7v-1.5c0-1.67 3.33-2.5 5-2.5s5 .83 5 2.5V17z"></path></g></svg>
				<span class="tooltiptext">My messages</span></a>
			</span>
<!--
			<span id="Answered" class="ribbonIcon tooltip"><a target="contents" href="<?=$root_dir.$page_answered?>" onclick="closeNotifications();resetBadges();">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M7 8V5l-7 7 7 7v-3l-4-4 4-4zm6 1V5l-7 7 7 7v-4.1c5 0 8.5 1.6 11 5.1-1-5-4-10-11-11z"></path></g></svg>
				<span class="tooltiptext">Answered</span></a>
			</span>
-->
			<span id="Bookmark" class="ribbonIcon tooltip"><a target="contents" onclick="closeNotifications();" href="<?=$root_dir.$page_my_bookmarks?>">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M17 3H7c-1.1 0-1.99.9-1.99 2L5 21l7-3 7 3V5c0-1.1-.9-2-2-2z"></path></g></svg>
				<span class="tooltiptext">My bookmarks</span></a>
			</span> 
			<span id="Gallery" class="ribbonIcon tooltip"><a onclick="closeNotifications();closeMenu();openGallery();">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M22 16V4c0-1.1-.9-2-2-2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2zm-11-4l2.03 2.71L16 11l4 5H8l3-4zM2 6v14c0 1.1.9 2 2 2h14v-2H4V6H2z" class="style-scope iron-icon"></path>	</g></svg>
				<span class="tooltiptext">Photo Gallery</span></a>
			</span> 
			<span id="Books" class="ribbonIcon tooltip"><a onclick="closeNotifications();openBooks();">
				<svg class="ribbonIcon" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g> 
				<path fill="<?=$ribbonColor ?>" d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"></path></g></svg>
				<span class="tooltiptext">Books</span></a>
			</span> 
			<span id="Movies" class="ribbonIcon tooltip"><a onclick="closeNotifications();openMovies();">
				<svg class="ribbonIcon" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet">
				<g><path fill="<?=$ribbonColor ?>" d="M18 3v2h-2V3H8v2H6V3H4v18h2v-2h2v2h8v-2h2v2h2V3h-2zM8 17H6v-2h2v2zm0-4H6v-2h2v2zm0-4H6V7h2v2zm10 8h-2v-2h2v2zm0-4h-2v-2h2v2zm0-4h-2V7h2v2z"></path></g>
				</svg>
				<span class="tooltiptext">Movies</span></a>
			</span> 
			<span id="Polls" class="ribbonIcon tooltip"><a onclick="closeNotifications();openPolls();">
				<svg class="ribbonIcon" viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g> <path fill="<?=$ribbonColor ?>" d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"></path></g></svg>
				<span class="tooltiptext">Polls</span></a>
			</span> 

			<span id="Search" class="ribbonIcon tooltip"><a target="bottom" onclick="closeNotifications();" href="<?=$root_dir.$page_search . (strcmp($cur_page, $page_my_bookmarks) == 0 ? "?mode=bookmarks" : "")?>">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"></path></g></svg>
				<span class="tooltiptext">Search</span></a>
			</span> 
		</div>
	</div>
<?php } ?>
<?php if ($logged_in && !is_null($moder) && $moder > 0) {
  $regs = get_regs_count();
?>  

	<div id="OpenModRibbonGroup" style="border: <?=$groupBorder?>; border-style: solid; border-width: 1px;" class="ribbonGroup">
		<div id="OpenModRibbonGroupTitle" class="ribbonGroupTitle">Mod</div>
		<div id="OpenModRibbonGroupIconContainer">
			<span id="OpenModIcon" class="ribbonIcon tooltip"><a target="bottom" onclick="expandModeratorMenu();">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M1 21h12v2H1zM5.245 8.07l2.83-2.827 14.14 14.142-2.828 2.828zM12.317 1l5.657 5.656-2.83 2.83-5.654-5.66zM3.825 9.485l5.657 5.657-2.828 2.828-5.657-5.657z" class="style-scope iron-icon"></path></g></svg>
				<span class="tooltiptext">Moderator</span></a>
				<?php if ($regs > 0) { ?>
				<span id="newPMBadge" class="button__badge"><?=$regs ?></span></a>
				<?php } ?>
				
			</span> 
		</div>
	</div>

	<div id="ModRibbonGroup" style="border: <?=$groupBorder?>; border-style: solid; border-width: 1px;display: none" class="ribbonGroup";>
		<div id="ModRibbonGroupTitle1" class="ribbonGroupTitle">Moderator</div>
		<div id="ModRibbonGroupIconContainer">
			<span id="ModCollapse" class="ribbonIcon tooltip"><a onclick="collapseModeratorMenu();">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
				<path fill="<?=$ribbonColor ?>" d="M15.41 16.09l-4.58-4.59 4.58-4.59L14 5.5l-6 6 6 6z" class="style-scope iron-icon"></path>
				</g></svg>
				<span class="tooltiptext">Hide</span></a>
			</span> 

<?php if ($regs > 0) { ?>
			<span id="Registrations" class="ribbonIcon tooltip"><a target="contents" href="<?=$root_dir.$page_registrations?>">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></g></svg>
				<span class="tooltiptext">Registrations</span>
				<span id="newPMBadge" class="button__badge"><?=$regs ?></span></a>
			</span>
<?php } ?>
			<span id="Users" class="ribbonIcon tooltip"><a target="contents" href="<?=$root_dir . $page_m_users?>">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" class="style-scope iron-icon"></path></g></svg>
				<span class="tooltiptext">Users</span></a>
			</span> 
			<span id="IPs" class="ribbonIcon tooltip"><a target="contents" href="<?=$root_dir . $page_m_ips?>">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M21 3H3c-1.1 0-2 .9-2 2v3h2V5h18v14h-7v2h7c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM1 18v3h3c0-1.66-1.34-3-3-3zm0-4v2c2.76 0 5 2.24 5 5h2c0-3.87-3.13-7-7-7zm0-4v2c4.97 0 9 4.03 9 9h2c0-6.08-4.93-11-11-11z" class="style-scope iron-icon"></path></path></g></svg>
				<span class="tooltiptext">IPs</span></a>
			</span> 
			<span id="DeletedPosts" class="ribbonIcon tooltip"><a target="contents" href="<?=$root_dir . $page_m_delposts?>">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" class="style-scope iron-icon"></path></path></g></svg>
				<span class="tooltiptext">Deleted</span></a>
			</span> 
			<span id="CensoredPosts" class="ribbonIcon tooltip"><a target="contents" href="<?=$root_dir . $page_m_censposts?>">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z" class="style-scope iron-icon"></path></path></g></svg>
				<span class="tooltiptext">Censored</span></a>
			</span> 
		</div>
	</div>
<?php } ?>

	<div id="SearchRibbonGroup" style="border: <?=$groupBorder?>; border-style: solid; border-width: 1px; width: 120px; padding: 4px" class="ribbonGroup">
		<div id="NotificationsRibbonGroupTitle" class="ribbonGroupTitle">Quick Search</div>
		    <span id="QuickSearch" class="ribbonIcon tooltip">
			    <input id="quickSearch" onkeyup="doSearch(this.value);" onfocus="javascript:RegisterField(this, true, false);" onkeypress="javascript:translate2(event);" onkeydown="javascript:text_OnKeydown(event);" type="text" style="width:115px; height:16px; border-width:0px; position:absolute; top:5px;    font-size: smaller;"></input> 
		    </span>
	</div>
	<script language="JavaScript" src="<?=autoversion('js/translit.js')?>"></script>
	<script>
	var delayTimer;
	function doSearch(text) {
	    clearTimeout(delayTimer);
	    delayTimer = setTimeout(function() {
	    console.log("search:");
	    document.getElementById("text").value= text;
	    console.log(document.getElementById("text").value);
	    document.getElementById("quickSearchForm").submit();
	    }, 2000); // Will do the ajax stuff after 1000 ms, or 1 s
	}
	</script>
	
	<div style="display: none">
	 <form action="/dosearch.php" method="post" id="quickSearchForm">
	 <input type="hidden" name="mode" id="mode" value="">
	 <input type="text" id="author" name="author" value="" size="32" maxlength="64">
	 <input type="text" id="text" name="text">
	 <select name="searchin">
	  <option value="1">Body and Subject</option>
	  <option value="2">Body</option>
	  <option value="3">Subject</option>
	</select>
        </form>
	</div>
	
<?php if ($logged_in == false) { ?>
	<div id="WelcomeRibbonGroup" style="float: right;border: <?=$groupBorder?>; border-style: solid; border-width: 1px;" class="ribbonGroupMobile";>
		<div id="ViewRibbonGroupTitle1" class="ribbonGroupTitle">Welcome!</div>
		<div id="ViewRibbonGroupIconContainer">
			<span id="Login" class="ribbonIcon tooltip">
			    <a onclick="openLoginForm();">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path id="loginIcon" fill="<?=$ribbonColor ?>"d="M12.65 10C11.83 7.67 9.61 6 7 6c-3.31 0-6 2.69-6 6s2.69 6 6 6c2.61 0 4.83-1.67 5.65-4H17v4h4v-4h2v-4H12.65zM7 14c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"></path></g></svg>
				<span class="tooltiptext">Login</span>
			    </a>
			</span> 
			<span id="Register" class="ribbonIcon tooltip"><a target="bottom" href="<?=$root_dir . $page_new_user?>">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z" class="style-scope iron-icon"></path></g></svg>
				<span class="tooltiptext">Register</span></a>
			</span> 
			<span id="ForgotPwd" class="ribbonIcon tooltip"><a target="bottom" href="<?=$root_dir . $page_forgot?>">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M17.01 14h-.8l-.27-.27c.98-1.14 1.57-2.61 1.57-4.23 0-3.59-2.91-6.5-6.5-6.5s-6.5 3-6.5 6.5H2l3.84 4 4.16-4H6.51C6.51 7 8.53 5 11.01 5s4.5 2.01 4.5 4.5c0 2.48-2.02 4.5-4.5 4.5-.65 0-1.26-.14-1.82-.38L7.71 15.1c.97.57 2.09.9 3.3.9 1.61 0 3.08-.59 4.22-1.57l.27.27v.79l5.01 4.99L22 19l-4.99-5z" class="style-scope iron-icon"></path></g></svg>
				<span class="tooltiptext">Forgot Password?</span></a>
			</span> 
		</div>
	</div>


<?php } ?>

<?php if ($logged_in) { ?>
	<div id="ProfileRibbonGroup" class="ribbonGroupMobile"; style="float: right; border: <?=$groupBorder?>; border-style: solid; border-width: 1px;">
		<div id="ProfileRibbonGroupTitle1" class="ribbonGroupTitle">&nbsp;<?=$logged_in ? $user : "Not logged in"?>&nbsp;<?=isset($safe_mode) && $safe_mode != 0 ? "<img src='images/small_green_dot.png' valign='center' style='margin-right:5px;' title='Safe Mode'/>" : ""?></div>
		<div id="ProfileRibbonGroupIconContainer">
			<span id="Pmail" class="ribbonIcon tooltip">
			    <a onclick="openPM();">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"></path></g></svg>
				<span class="tooltiptext">PMail</span>
			    </a>
			</span> 
			<span id="Settings" class="ribbonIcon tooltip"><a onclick="closeNotifications(); openProfile();">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></g></svg>
				<span class="tooltiptext">Settings</span></a>
			</span> 
      <?php
      $url = $root_dir . $cur_page . '?logout=true';
      if (!is_null( $author_id ) ) { 
        $url .= '&author_id=' . $author_id;
      }   
      ?>      
			<span id="Logout" class="ribbonIcon tooltip"><a target="_top" href="<?=$url?>">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M10.09 15.59L11.5 17l5-5-5-5-1.41 1.41L12.67 11H3v2h9.67l-2.58 2.59zM19 3H5c-1.11 0-2 .9-2 2v4h2V5h14v14H5v-4H3v4c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z" class="style-scope iron-icon"></path></g></svg></a>
				<span class="tooltiptext">Logout</span>
			</span> 
		</div>
	</div>
	
	

	
	
<?php } ?>	
<?php if (function_exists('pages_function')) { ?>
	<div id="PagesRibbonGroup" class="ribbonGroupMobile"; style="float: right;">
		<div id="PagesRibbonGroupTitle1" class="ribbonGroupTitle">&nbsp;</div>
		<div id="PagesRibbonGroupIconContainer">
    <?php
      pages_function();
    ?>
		</div>
	</div>
<?php } ?>
</div>

<?php if ($logged_in == false) { ?>

<div id="loginForm" style="display: none;     margin: 10px;">
 <form method="post" target="_top" action="<?php print($root_dir . $page_login); ?>">
 <input type="hidden" name="lastpage" id="lastpage" value="<?php print( $cur_page );?>"/>
 <?php if (!is_null($err_login) && strlen($err_login) > 0) { ?>
 <font color="red"><b><?php print($err_login); ?></b> </font>
 <?php }      
    if (!is_null($author_id)) {
        print('<input type="hidden" name="author_id" id="author_id" value="' . $author_id . '"/>');
    }
 ?>
 Username: <input type="text" id="user" name="user" maxlength="64" size="16" value="<?php htmlentities($user, HTML_ENTITIES,'UTF-8');?>"/> 
 Password: <input type="password" id="password" name="password" size="8" maxlength="16" autocomplete="off"/> <input type="Submit" value="Login"/>
 </form>
</div>
<?php
}
?>

<!-- NOTIFICATIONS LISTBOX -->

<div id="NotificationsContainer" style="display: none;" class="notificationContainer">
    <!-- templates. These tags are not displayed. Instead they are cloned by the notifications script and populated dynamically  -->
    <li class="notificationLi" style="display: none;" id="eventTemplateLi">
	<div style="padding: 6px 30px 5px 12px;">
	    <div class="notificationIcon"><svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="grey" d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"></path></g></svg></div>
	    <div id="notificationMessage" class="notificationMessage"></div>
	    <div id="eventTime" class="notificationTime">Just now</div>
	</div>
    </li>
    <li class="notificationLi" style="display: none;" id="newThreadEventTemplateLi">
	<div style="padding: 6px 30px 5px 12px;">
	    <div class="notificationIcon"><svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="grey"  d="M3 15.25V19h3.75L15.5 10.5l-3.75-3.75L3 15.25zM18 8c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"></path></g></svg></div>
	    <div id="notificationMessage" class="notificationMessage"></div>
	    <div id="eventTime" class="notificationTime">Just now</div>
	</div>
    </li>
    <li class="notificationLi" style="display: none;" id="newReplyEventTemplateLi">
	<div style="padding: 6px 30px 5px 12px;">
	    <div class="notificationIcon"><svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="grey" d="M7 8V5l-7 7 7 7v-3l-4-4 4-4zm6 1V5l-7 7 7 7v-4.1c5 0 8.5 1.6 11 5.1-1-5-4-10-11-11z"></path></g></svg></div>
	    <div id="notificationMessage" class="notificationMessage"></div>
	    <div id="eventTime" class="notificationTime">Just now</div>
	</div>
    </li>
    <li class="notificationLi" style="display: none;" id="newBookmarkEventTemplateLi">
	<div style="padding: 6px 30px 5px 12px;">
	    <div class="notificationIcon"><svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="grey" d="M17 3H7c-1.1 0-1.99.9-1.99 2L5 21l7-3 7 3V5c0-1.1-.9-2-2-2z"></path></g></svg></div>
	    <div id="notificationMessage" class="notificationMessage"></div>
	    <div id="eventTime" class="notificationTime">Just now</div>
	</div>
    </li>
    <!-- end of templates -->

    <li class="notificationLi" onclick="openNewMessages();">
	<div style="padding: 6px 30px 5px 12px;">
	    <div class="notificationIcon"><svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path class="ribbonIcon" fill="grey" d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"></path></g></svg>
		<span id="newPostsBadge" class="button__badge" style="display:none;">4</span></a>
	    </div>
	    <div class="notificationMessage">There have been <span id="newPostsBadge2">0</span> new messages sent to the  the forum since you checked last time.</div>
	    <div id="newPostsTime" class="notificationTime">just now</div>
	</div>
    </li>
    <li class="notificationLi" onclick="openAnswered();">
	<div style="padding: 6px 30px 5px 12px;">
	    <div class="notificationIcon"><svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="grey" d="M7 8V5l-7 7 7 7v-3l-4-4 4-4zm6 1V5l-7 7 7 7v-4.1c5 0 8.5 1.6 11 5.1-1-5-4-10-11-11z"></path></g></svg>
		<span id="newAnswersBadge" class="button__badge" style="display:none;">4</span></a>
	    </div>
	    <div class="notificationMessage">You have received <span id="newAnswersBadge2">0</span>  public replies since you last checked.</div>
	    <div id="newAnswersTime" class="notificationTime">just now</div>
	</div>
    </li>
    <li class="notificationLi"  onclick="openPM();">
	<div style="padding: 6px 30px 5px 12px;">
	    <div class="notificationIcon"><svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="grey" d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"></path></g></svg>
        	    <?php if (!is_null($new_pm) && $new_pm > 0) { $styleStr = 'display:block;';} else {$styleStr = 'display:none;';} ?>
        	    <span id="newPMBadge" class="button__badge" style="<?=$styleStr?>"><?=$new_pm?></span>
	    </div>
	    <div class="notificationMessage" id="pmNotificationMessage">You have received <?=$new_pm?>  new private messages since you last checked. 
	    <?php if($new_pm >0){ ?>
	    Check your pmail!
	    <?php } ?>
	    </div>
	    <div id="newPMTime" class="notificationTime">long ago eh</div>
	</div>
    </li>
    <div id="events">
    </div>
</div>
<!-- Hamburger -->

<div id="HamburgerContainer" style="display: none;" class="notificationContainer">
    <li class="notificationLi" style="display: block; height: 30px; cursor:default; background: lightgrey" id="eventTemplateLi">
	<div style="padding: 6px 30px 5px 12px;">
	    <div id="notificationMessage" class="notificationMessage">View Options</div>
	</div>
    </li>
    <li class="notificationLi" style="display: block;" id="refreshLi" onclick="closeMenu();window.open('/threads.php','contents');">
	<div style="padding: 6px 30px 5px 12px;">
	    <div class="notificationIcon"><svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path fill="grey"  d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"></path>
	    </g></svg></div>
	    <div id="hamburgerRefresh" class="hamburgerItem">Refresh Threads</div>
	</div>
    </li>
    <li class="notificationLi" style="display: block;" id="refreshLi" onclick="closeMenu();contents.toggleAll();">
	<div style="padding: 6px 30px 5px 12px;">
	    <div class="notificationIcon"><svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path fill="grey" d="M4 10.5c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5 1.5-.67 1.5-1.5-.67-1.5-1.5-1.5zm0-6c-.83 0-1.5.67-1.5 1.5S3.17 7.5 4 7.5 5.5 6.83 5.5 6 4.83 4.5 4 4.5zm0 12.17c-.74 0-1.33.6-1.33 1.33s.6 1.33 1.33 1.33 1.33-.6 1.33-1.33-.59-1.33-1.33-1.33zM7 19h14v-2H7v2zm0-6h14v-2H7v2zm0-8v2h14V5H7z"></path>
	    </g></svg></div>
	    <div id="hamburgerRefresh" class="hamburgerItem">Collapse Threads</div>
	</div>
    </li>
    
    <!--stuff -->
    
    <li class="notificationLi" style="display: block; height: 30px; cursor:default; background: lightgrey" id="eventTemplateLi">
	<div style="padding: 6px 30px 5px 12px;">
	    <div id="notificationMessage" class="notificationMessage">Various Stuff</div>
	</div>
    </li>

    <li class="notificationLi" style="display: block;" id="refreshLi" onclick="closeMenu();window.open('/search.php','contents');">
	<div style="padding: 6px 30px 5px 12px;">
	    <div class="notificationIcon"><svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path fill="grey" d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"></path>
	    </g></svg></div>
	    <div id="hamburgerRefresh" class="hamburgerItem">Advanced Search</div>
	</div>
    </li>

    <li class="notificationLi" style="display: block;" id="refreshLi" onclick="closeMenu();window.open('/mymessages.php','contents');">
	<div style="padding: 6px 30px 5px 12px;">
	    <div class="notificationIcon"><svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path fill="grey" d="M20 0H4v2h16V0zM4 24h16v-2H4v2zM20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-8 2.75c1.24 0 2.25 1.01 2.25 2.25s-1.01 2.25-2.25 2.25S9.75 10.24 9.75 9 10.76 6.75 12 6.75zM17 17H7v-1.5c0-1.67 3.33-2.5 5-2.5s5 .83 5 2.5V17z"></path>
	    </g></svg></div>
	    <div id="hamburgerRefresh" class="hamburgerItem">My Messages</div>
	</div>
    </li>
    <li class="notificationLi" style="display: block;" id="refreshLi" onclick="closeMenu();window.open('/mybookmarks.php','contents');">
	<div style="padding: 6px 30px 5px 12px;">
	    <div class="notificationIcon"><svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path fill="grey" d="M17 3H7c-1.1 0-1.99.9-1.99 2L5 21l7-3 7 3V5c0-1.1-.9-2-2-2z"></path>
	    </g></svg></div>
	    <div id="hamburgerRefresh" class="hamburgerItem">My Bookmarks</div>
	</div>
    </li>
    <li class="notificationLi" style="display: block;" id="refreshLi" onclick="closeMenu();openGallery();">
	<div style="padding: 6px 30px 5px 12px;">
	    <div class="notificationIcon"><svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path fill="grey" d="M22 16V4c0-1.1-.9-2-2-2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2zm-11-4l2.03 2.71L16 11l4 5H8l3-4zM2 6v14c0 1.1.9 2 2 2h14v-2H4V6H2z" class="style-scope iron-icon"></path>
	    </g></svg></div>
	    <div id="hamburgerRefresh" class="hamburgerItem">Photo Gallery</div>
	</div>
    </li>
    <li class="notificationLi" style="display: block;" id="refreshLi" onclick="closeMenu();openBooks();">
	<div style="padding: 6px 30px 5px 12px;">
	    <div class="notificationIcon"><svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path fill="grey" d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"></path>	    
	    </g></svg></div>
	    <div id="hamburgerRefresh" class="hamburgerItem">Book Library</div>
	</div>
    </li>
    <li class="notificationLi" style="display: block;" id="refreshLi" onclick="closeMenu();openMovies();">
	<div style="padding: 6px 30px 5px 12px;">
	    <div class="notificationIcon"><svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path fill="grey" d="M18 3v2h-2V3H8v2H6V3H4v18h2v-2h2v2h8v-2h2v2h2V3h-2zM8 17H6v-2h2v2zm0-4H6v-2h2v2zm0-4H6V7h2v2zm10 8h-2v-2h2v2zm0-4h-2v-2h2v2zm0-4h-2V7h2v2z"></path>
	    </g></svg></div>
	    <div id="hamburgerRefresh" class="hamburgerItem">Movie Library</div>
	</div>
    </li>
    <li class="notificationLi" style="display: block;" id="refreshLi" onclick="closeMenu();openPolls();">
	<div style="padding: 6px 30px 5px 12px;">
	    <div class="notificationIcon"><svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path fill="grey" d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"></path>
	    </g></svg></div>
	    <div id="hamburgerRefresh" class="hamburgerItem">Polls</div>
	</div>
    </li>

<?php if ($logged_in && !is_null($moder) && $moder > 0) { ?>
    <!-- Moderator UI -->
    <li class="notificationLi" style="display: block; height: 30px; cursor:default; background: lightgrey" id="eventTemplateLi">
	<div style="padding: 6px 30px 5px 12px;">
	    <div id="notificationMessage" class="notificationMessage">Moderator</div>
	</div>
    </li>
    <li class="notificationLi" style="display: block;" id="refreshLi" onclick="closeMenu();window.open('/modusers.php','contents');">
	<div style="padding: 6px 30px 5px 12px;">
	    <div class="notificationIcon"><svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path fill="grey" d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"></path>
	    </g></svg></div>
	    <div id="hamburgerRefresh" class="hamburgerItem">Users</div>
	</div>
    </li>
    <li class="notificationLi" style="display: block;" id="refreshLi" onclick="closeMenu();window.open('/modips.php','contents');">
	<div style="padding: 6px 30px 5px 12px;">
	    <div class="notificationIcon"><svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path fill="grey" d="M21 3H3c-1.1 0-2 .9-2 2v3h2V5h18v14h-7v2h7c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM1 18v3h3c0-1.66-1.34-3-3-3zm0-4v2c2.76 0 5 2.24 5 5h2c0-3.87-3.13-7-7-7zm0-4v2c4.97 0 9 4.03 9 9h2c0-6.08-4.93-11-11-11z"></path>	    
	    </g></svg></div>
	    <div id="hamburgerRefresh" class="hamburgerItem">IP Addresses</div>
	</div>
    </li>
    <li class="notificationLi" style="display: block;" id="refreshLi" onclick="closeMenu();window.open('/moddelposts.php','contents');">
	<div style="padding: 6px 30px 5px 12px;">
	    <div class="notificationIcon"><svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path fill="grey" d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path>	    
	    </g></svg></div>
	    <div id="hamburgerRefresh" class="hamburgerItem">Deleted Messages</div>
	</div>
    </li>
    <li class="notificationLi" style="display: block;" id="refreshLi" onclick="closeMenu();window.open('/modcensposts.php','contents');">
	<div style="padding: 6px 30px 5px 12px;">
	    <div class="notificationIcon"><svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g>
	    <path fill="grey" d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"></path>
	    </g></svg></div>
	    <div id="hamburgerRefresh" class="hamburgerItem">Censored Messages</div>
	</div>
    </li>

<?php } ?>

</div>
<!-- --------------------- -->