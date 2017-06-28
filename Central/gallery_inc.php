<?php
 
?>
<script>
currentPhotoIndex=0;

var galleryImages = ["./images/forum.png", 
"https://s3.amazonaws.com/vipvip.ca/7rsgBEfBNo66LIVofWNsScreenshot_20170628-123922.png",
"https://s3.amazonaws.com/vipvip.ca/ffTtgW5yV4Gallery.png", 
"https://s3.amazonaws.com/vipvip.ca/DMkHp1hdFwpm.png", 
"https://s3.amazonaws.com/vipvip.ca/HZndZAtSp7noti.png",
"https://s3.amazonaws.com/vipvip.ca/Jb7xmjnm16colors.png",
"https://s3.amazonaws.com/vipvip.ca/b7e6S5V8tUimage.png",
"https://s3.amazonaws.com/vipvip.ca/FUzO5EclWGpolls.png"

];
function nextPhoto()
{
    if((currentPhotoIndex + 1) < galleryImages.length)
    {
        currentPhotoIndex++;
        document.getElementById("currentPhoto").src = galleryImages[currentPhotoIndex];
    }
}
function prevPhoto()
{
    if((currentPhotoIndex - 1) < 0)return;
    currentPhotoIndex--;
    document.getElementById("currentPhoto").src = galleryImages[currentPhotoIndex];
}
                            
</script>
<style>
div.gallery_container {
    border: 0px solid gray;
    background-color: black;
}

header {
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

.column-left{ float: left; width: 10%; background-color: black; cursor: pointer;max-height: 60%;}
.column-left:hover #gallery_nav_left {fill: white;}
.column-right{ float: right; width: 10%; background-color: black; cursor: pointer;max-height: 60%;}
.column-right:hover #gallery_nav_right {fill: white;}
.column-center{ display: inline-block; width: 80%;}

img {
    background: black;
    vertical-align: middle;
    max-height: 50vh;
    max-width: 50vw;
}
</style>


<div class="gallery_container" id="gallery">
     <header><a id="gallery_title">Photo Gallery: Threaded discussion UI</a></header>
     <div>
	<div class="column-center">
	    <img id="currentPhoto" src="./images/forum.png"
		style="
		margin: auto;
		display: block;
	    ">
	</div>
	<div class="column-left" onclick="prevPhoto();"><svg viewBox="0 0 24 24" preserveAspectRatio="xMidYMid meet" style="pointer-events: none; display: block; width: 100%; height: 100%;"><g><path id="gallery_nav_left" fill="#444" d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"></path></g></svg></div>
	<div class="column-right" onclick="nextPhoto();";><svg viewBox="0 0 24 24" preserveAspectRatio="xMidYMid meet" style="pointer-events: none; display: block; width: 100%; height: 100%;"><g><path id="gallery_nav_right" fill="#444" d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"></path></g></svg></div>
	</div>
<footer></footer>


</div>