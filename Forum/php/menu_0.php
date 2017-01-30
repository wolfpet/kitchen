<?php

//print($user_id);


if (!is_null($user_id) && $user_id != null) {

  $colorquery = "SELECT color_ribbon, color_ribbon_background, color_icon_hover, color_group_border, color_topics_unread, color_topics_hover, color_topics_visited, color_titles from confa_users where id = " . $user_id;
  $colorresult = mysql_query($colorquery);
  if ($colorresult) {
  $colorrow = mysql_fetch_assoc($colorresult);
  $ribbonColor=$colorrow['color_ribbon'];
  $ribbonBackground=$colorrow['color_ribbon_background'];  
  $iconHover=$colorrow['color_icon_hover'];
  $groupBorder=$colorrow['color_group_border'];
  
  $color_topics_unread= $colorrow['color_topics_unread'];
  $color_topics_hover=$colorrow['color_topics_hover'];
  $color_topics_visited=$colorrow['color_topics_visited'];
 }
}
else{
  $ribbonColor='white';
  $ribbonBackground='#0080c0';  
  $iconHover='#0090c0';
  $groupBorder='#0090c0';
  
  $color_topics_unread= '#0000FF';
  $color_topics_hover= '#FF0000';
  $color_topics_visited='#0080c0';
}

?>

<style>
.ribbonIcon:hover { 
    background-color: <?=$iconHover?>;
    cursor: pointer;
}
    a:link    {color:<?=$color_topics_unread?>; font-family:Verdana,Arial; text-decoration:none;}
    a:visited {color:<?=$color_topics_visited?>; font-family:Verdana,Arial; text-decoration:none;}
    a:active  {color:<?=$color_topics_hover?>; font-family:Verdana,Arial; text-decoration:none;}
    a:hover   {color:<?=$color_topics_hover?>; font-family:Verdana,Arial; text-decoration:underline;}
    a.user_link {color:black;}
    .selected  {background:#E0F1FF; /* Yellow = #FFFF99 */}
    h1<---->{font-family:Verdana,Arial; font-size:36pt;}
    h2<--->{font-family:Verdana,Arial; font-size:24pt; color:#0080c0;}
    h3<--->{font-family:Verdana,Arial; font-size:14pt; color:#0080c0;}

.pmdropdown {
    position: relative;
    display: inline-block;
}
.pmdropdown-content {
    display: none;
    position: fixed;
    right: 0;
    background-color: <?=$ribbonBackground?>;
    min-width: 120px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
}
.pmdropdown-content a {
    color: <?=$ribbonColor?>;
    padding: 10px 14px;
    text-decoration: none;
    display: block;
}
.pmdropdown-content a:hover {background-color: <?=$iconHover;?>}
.pmdropdown:hover .pmdropdown-content {
    display: block;
}
/* Pager */

.pagination li.active span {
    background-color: <?=$ribbonBackground?>;
    border-color: <?=$ribbonBackground?> ;
    color: <?=$ribbonColor?>;
}

.pagination li a:hover, .pagination .dropdown-visible a.dropdown-trigger, .nojs .pagination .dropdown-container:hover a.dropdown-trigger {
    background-color: <?=$iconHover?>;
    border-color: <?=$ribbonBackground?>;
    color:  <?=$ribbonColor?>;
}
</style>

<div id="Ribbon" class="ribbon" style="background-color: <?=$ribbonBackground?>; color:<?=$ribbonColor?>;">
<?php if (isset($title) && $title != null) { ?>
	<div id="ForumTitle" class="ribbonGroup"; style="width: 100px;height: 37px;padding-top: 4px;text-align: center;vertical-align: top; font-size: x-large;padding-top: 11px; border: <?=$groupBorder?>; border-style: solid; border-width: 1px;">
<?php if (isset($banner) && !is_null($banner)) { ?>
<img src="<?=$banner?>" alt="<?='Welcome'?>"/>
<?php } else { print($title); }?>
</div>
<?php }?>

	<div id="SortRibbonGroup" style="border: <?=$groupBorder?>; border-style: solid; border-width: 1px;" class="ribbonGroupMobile";">
		<div id="SortRibbonGroupTitle" class="ribbonGroupTitle">Sort</div>
		<div id="SortRibbonGroupIconContainer">
			<span class="ribbonIcon tooltip" id="ByDateIcon"><a target="contents" href="<?=$root_dir.$page_bydate?>?mode=bydate">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path class="ribbonIcon" fill="<?=$ribbonColor ?>" d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"></path></g></svg>
				<span class="tooltiptext">By Date</span>
				<!-- This is a badge sample that indicates that there are 4 new posts -->
				<span id="newPostsBadge" class="button__badge" style="display:none;">4</span></a>
			</span> 
			<span id="Expanded" class="ribbonIcon tooltip"><a target="contents" href="<?=$root_dir.$page_expanded?>">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M6 7h2.5L5 3.5 1.5 7H4v10H1.5L5 20.5 8.5 17H6V7zm4-2v2h12V5H10zm0 14h12v-2H10v2zm0-6h12v-2H10v2z"></path></g></svg>
				<span class="tooltiptext">Expanded</span></a>
			</span> 
			<span id="Collapsed" class="ribbonIcon tooltip"><a target="contents" href="<?=$root_dir.$page_collapsed?>">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M4 10.5c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5 1.5-.67 1.5-1.5-.67-1.5-1.5-1.5zm0-6c-.83 0-1.5.67-1.5 1.5S3.17 7.5 4 7.5 5.5 6.83 5.5 6 4.83 4.5 4 4.5zm0 12.17c-.74 0-1.33.6-1.33 1.33s.6 1.33 1.33 1.33 1.33-.6 1.33-1.33-.59-1.33-1.33-1.33zM7 19h14v-2H7v2zm0-6h14v-2H7v2zm0-8v2h14V5H7z"></path></g></svg>
				<span class="tooltiptext">Collapsed</span></a>
			</span> 
		</div>
	</div>
	<div id="WriteRibbonGroup" style="border: <?=$groupBorder?>; border-style: solid; border-width: 1px;" class="ribbonGroupMobile">
		<div id="WriteRibbonGroupTitle" class="ribbonGroupTitle">Write</div>
		<div id="WriteRibbonGroupIconContainer">
			<span id="NewThreadIcon" class="ribbonIcon tooltip"><a target="bottom" href="<?=$root_dir.$page_new?>">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M3 15.25V19h3.75L15.5 10.5l-3.75-3.75L3 15.25zM18 8c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"></path></g></svg>
				<span class="tooltiptext">New thread</span></a>
			</span> 
		</div>
	</div>

	<div id="FindRibbonGroup" style="border: <?=$groupBorder?>; border-style: solid; border-width: 1px;" class="ribbonGroup">
		<div id="FindRibbonGroupTitle" class="ribbonGroupTitle">Find</div>
		<div id="FindRibbonGroupIconContainer">
			<span id="MyMessages" class="ribbonIcon tooltip"><a target="contents" href="<?=$root_dir.$page_my_messages?>">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M20 0H4v2h16V0zM4 24h16v-2H4v2zM20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-8 2.75c1.24 0 2.25 1.01 2.25 2.25s-1.01 2.25-2.25 2.25S9.75 10.24 9.75 9 10.76 6.75 12 6.75zM17 17H7v-1.5c0-1.67 3.33-2.5 5-2.5s5 .83 5 2.5V17z"></path></g></svg>
				<span class="tooltiptext">My messages</span></a>
			</span> 
			<span id="Answered" class="ribbonIcon tooltip"><a target="contents" href="<?=$root_dir.$page_answered?>">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M7 8V5l-7 7 7 7v-3l-4-4 4-4zm6 1V5l-7 7 7 7v-4.1c5 0 8.5 1.6 11 5.1-1-5-4-10-11-11z"></path></g></svg>
				<span class="tooltiptext">Answered</span></a>
				<!-- This is a badge sample that indicates that there are 4 new answers -->
				<span id="newAnswersBadge" class="button__badge" style="display:none;">4</span></a>
			</span> 
			<span id="Bookmark" class="ribbonIcon tooltip"><a target="contents" href="<?=$root_dir.$page_my_bookmarks?>">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M17 3H7c-1.1 0-1.99.9-1.99 2L5 21l7-3 7 3V5c0-1.1-.9-2-2-2z"></path></g></svg>
				<span class="tooltiptext">My bookmarks</span></a>
			</span> 
			<span id="Search" class="ribbonIcon tooltip"><a target="bottom" href="<?=$root_dir.$page_search . (strcmp($cur_page, $page_my_bookmarks) == 0 ? "?mode=bookmarks" : "")?>">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"></path></g></svg>
				<span class="tooltiptext">Search</span></a>
			</span> 
		</div>
	</div>

<?php if ($logged_in && !is_null($moder) && $moder > 0) {
  $regs = get_regs_count();
?>  
	<div id="ModRibbonGroup" style="border: <?=$groupBorder?>; border-style: solid; border-width: 1px;" class="ribbonGroup";>
		<div id="ViewRibbonGroupTitle1" class="ribbonGroupTitle">Moderator</div>
		<div id="ViewRibbonGroupIconContainer">
<?php if ($regs > 0) { ?>
			<span id="Registrations" class="ribbonIcon tooltip"><a target="contents" href="<?=$root_dir.$page_registrations?>">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></g></svg>
				<span class="tooltiptext">Registrations</span>
				<span id="newPMBadge" class="button__badge">6</span></a>
        
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

	<div id="ViewRibbonGroup" style="border: <?=$groupBorder?>; border-style: solid; border-width: 1px;" class="ribbonGroup";>
		<div id="ViewRibbonGroupTitle1" class="ribbonGroupTitle">View</div>
		<div id="ViewRibbonGroupIconContainer">
			<span id="Horizontal" class="ribbonIcon tooltip"><a target="_top" href=".">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M4 18h17v-6H4v6zM4 5v6h17V5H4z"></path></g></svg>
				<span class="tooltiptext">Horizontal</span></a>
			</span> 
			<span id="Vertical" class="ribbonIcon tooltip"><a  onclick="setVerticaLayout();">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M6 5H3c-.55 0-1 .45-1 1v12c0 .55.45 1 1 1h3c.55 0 1-.45 1-1V6c0-.55-.45-1-1-1zm14 0h-3c-.55 0-1 .45-1 1v12c0 .55.45 1 1 1h3c.55 0 1-.45 1-1V6c0-.55-.45-1-1-1zm-7 0h-3c-.55 0-1 .45-1 1v12c0 .55.45 1 1 1h3c.55 0 1-.45 1-1V6c0-.55-.45-1-1-1z"></path></g></svg>
				<span class="tooltiptext">Vertical</span></a>
			</span> 
			<span id="Phone" class="ribbonIcon tooltip"><a target="_top" href="mobile/index.html">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M20.1 7.7l-1 1c1.8 1.8 1.8 4.6 0 6.5l1 1c2.5-2.3 2.5-6.1 0-8.5zM18 9.8l-1 1c.5.7.5 1.6 0 2.3l1 1c1.2-1.2 1.2-3 0-4.3zM14 1H4c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-2-2-2zm0 19H4V4h10v16z"></path></g></svg>
				<span class="tooltiptext">Phone</span></a>
			</span> 
			<span id="Refresh" class="ribbonIcon tooltip"><a target="_top" class="menu" href="<?php print($root_dir . $cur_page); if (!strcmp($cur_page, $page_byuser)) {print('?author_id=' . $author_id); }else {if (/*!strcmp($cur_page, $page_expanded) && */!is_null($page)){ print('?page=' . $page); } } ?>">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"></path></g></svg>
				<span class="tooltiptext">Refresh</span></a>
			</span> 
		</div>
	</div>  

  <?php if ($logged_in == false) { ?>
	<div id="WelcomeRibbonGroup" style="border: <?=$groupBorder?>; border-style: solid; border-width: 1px;" class="ribbonGroup";>
		<div id="ViewRibbonGroupTitle1" class="ribbonGroupTitle">Welcome!</div>
		<div id="ViewRibbonGroupIconContainer">
			<span id="Login" class="ribbonIcon tooltip">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>"d="M12.65 10C11.83 7.67 9.61 6 7 6c-3.31 0-6 2.69-6 6s2.69 6 6 6c2.61 0 4.83-1.67 5.65-4H17v4h4v-4h2v-4H12.65zM7 14c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"></path></g></svg>
				<span class="tooltiptext">Login</span>
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
	<div id="ProfileRibbonGroup" class="ribbonGroupMobile"; style="float: right; border: <?=$groupBorder?>; border-style: solid; border-width: 1px;">
		<div id="ProfileRibbonGroupTitle1" class="ribbonGroupTitle">&nbsp;<?=$logged_in ? $user : "Not logged in"?>&nbsp;<?=isset($safe_mode) && $safe_mode != 0 ? "<img src='images/small_green_dot.png' valign='center' style='margin-right:5px;' title='Safe Mode'/>" : ""?></div>
<?php if ($logged_in) { ?>
		<div id="ProfileRibbonGroupIconContainer">
			<span id="Pmail" class="pmdropdown ribbonIcon tooltip">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"></path></g></svg>
				<span class="tooltiptext">Pmail</span>
        <?php if (!is_null($new_pm) && $new_pm > 0) { ?>
				<span id="newPMBadge" class="button__badge"><?=$new_pm?></span>
        <?php } ?>
    				<style>pmdropdown-content.a:hover {color: green;}</style>
				<div class="pmdropdown-content" style="background-color: <?=$ribbonBackground?>">
					<a target="contents" style="color: <?=$ribbonColor?>" href="<?=$root_dir.$page_pmail?>">Inbox</a>
					<a target="contents" href="<?=$root_dir.$page_pmail_sent?>">Sent</a>
					<a target="bottom" href="<?=$root_dir.$page_pmail_send?>">New</a>
				</div>
			</span> 
			<span id="Settings" class="ribbonIcon tooltip"><a target="bottom" href="<?=$root_dir.$page_profile?>">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></g></svg>
				<span class="tooltiptext">Settings</span></a>
			</span> 
      <?php
      $url = $root_dir . $cur_page . '?logout=true';
      if (!is_null( $author_id ) ) { 
        $url .= '&author_id=' . $author_id;
      }   
      ?>      
			<span id="Logout" class="ribbonIcon tooltip"><a target="contents" href="<?=$url?>">
				<svg class="ribbonIcon"  viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path fill="<?=$ribbonColor ?>" d="M10.09 15.59L11.5 17l5-5-5-5-1.41 1.41L12.67 11H3v2h9.67l-2.58 2.59zM19 3H5c-1.11 0-2 .9-2 2v4h2V5h14v14H5v-4H3v4c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z" class="style-scope iron-icon"></path></g></svg></a>
				<span class="tooltiptext">Logout</span>
			</span> 
		</div>
<?php } ?>
	</div>
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

<table width="100%"><tr>
<td align="right" nowrap>
<form method="post" target="contents" action="<?php print($root_dir . $page_login); ?>">
<input type="hidden" name="lastpage" id="lastpage" value="<?php print( $cur_page );?>"/>
<?php if (!is_null($err_login) && strlen($err_login) > 0) { ?>
<font color="red"><b><?php print($err_login); ?></b> </font>
<?php }      
    if (!is_null($author_id)) {
        print('<input type="hidden" name="author_id" id="author_id" value="' . $author_id . '"/>');
    }
?>
Username: <input type="text" id="user" name="user" maxlength="64" size="16" value="<?php htmlentities($user, HTML_ENTITIES,'UTF-8');?>"/> Password: <input type="password" id="password" name="password" size="8" maxlength="16" autocomplete="off"/> <input type="Submit" value="Login"/>
</form></td>
</tr></table>
<?php 
} 
?>