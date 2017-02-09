<?php
 
?>
<style>
div.gallery_container {
    width: 100vw;
    border: 0px solid gray;
    position: absolute;
    top: 53px;
    background-color: black;
    max-height: 100vh;
    height: 100vh;
    display: none;
}
        
header {
    margin-top: 3vh;
    margin-left: 0%;
    padding: 0px;
    color: grey;
    background-color: black;
    clear: left;
    text-align: center;
    font-size: 8pt;
}

footer {
    padding: 0px;
    color: grey;
    background-color: black;
    clear: left;
    text-align: center;
    font-size: 8pt;
    
}

path.svg_controls {
    fill: #444;

}

path.svg_controls:hover {
    fill: #fff;
    }
    
div.gallery_exit_btn_div:hover #gallery_exit_btn { fill: #fff; } 
.column-left{ float: left; width: 6%; background-color: black; height: 100%; margin-top: 20%; cursor: pointer;}
.column-left:hover #gallery_nav_left {fill: white;}
.column-right{ float: right; width: 6%; background-color: black; height: 100%; margin-top: 20%; cursor: pointer;}
.column-right:hover #gallery_nav_right {fill: white;}
.column-center{ display: inline-block; width: 88%; height: 100%; }

img {
    background: black;
    vertical-align: middle;
    max-height: 80vh;
    max-width: 88vw;
}
</style>

<div id="menu_cover" style="width: 100vw;position: absolute;top: 0px;color: white; background-color: black; opacity: 0.6;height: 53px;color: white; display: none"></div>
<div class="gallery_container" id="gallery">
     <header><a id="gallery_title">Photo Gallery: images from all users</a></header>
     <div>
     <div class="column-center">
        <img id="currentPhoto" src="" style="margin: auto; display: block;">
     </div>
	<div class="column-left" onclick="prevPhoto();"><svg viewBox="0 0 24 24" preserveAspectRatio="xMidYMid meet" style="pointer-events: none; display: block; width: 100%; height: 100%;"><g><path id="gallery_nav_left" fill="#444" d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"></path></g></svg></div>
	<div class="column-right" onclick="nextPhoto();";><svg viewBox="0 0 24 24" preserveAspectRatio="xMidYMid meet" style="pointer-events: none; display: block; width: 100%; height: 100%;"><g><path id="gallery_nav_right" fill="#444" d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"></path></g></svg></div>
	</div>
    <footer ><a style="cursor: pointer"id="gallery_msg_title" onclick="openMsg(currentMsg)">title</a> by <a style="cursor: pointer" id="gallery_author_name" onclick="filterByName(currentAuthor);">user</a></footer>

<div id="gallery_menu" style="width: 100vw;position: absolute;top: 0px;color: white; opacity: 1.0;height: 53px;color: white; display: block">
    <div class="gallery_exit_btn_div" onclick="closeGallery();" style="width: 30px; margin-right: 8px; margin-top: 8px; float: right;cursor: pointer;">
	<svg viewBox="0 0 24 24" preserveAspectRatio="xMidYMid meet" style="display: block; width: 100%; height: 100%;">
	    <g><path id="gallery_exit_btn" class="svg_controls" d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path></g>
	</svg></div>
</div>

</div>