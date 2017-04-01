<?php

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

  $color_title = $colorrow['color_titles'];

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
  
  $color_title='#0080c0';
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

h3 {
    font-family: Verdana,Arial;
        font-size: 14pt;
            color: <?=$color_title?>;
}
</style>

