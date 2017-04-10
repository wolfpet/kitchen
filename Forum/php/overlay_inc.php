<!-- This overlay control is a placeholder for various dialog boxes rendered on top of legacy frames -->
<?php
 
?>
<style>
div.overlay_container {
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
    
div.overlay_exit_btn_div:hover #overlay_exit_btn { fill: #fff; } 
.column-left{ float: left; width: 6%; background-color: black; height: 100%; margin-top: 20%; cursor: pointer;}
.column-left:hover #overlay_nav_left {fill: white;}
.column-right{ float: right; width: 6%; background-color: black; height: 100%; margin-top: 20%; cursor: pointer;}
.column-right:hover #overlay_nav_right {fill: white;}
.column-center{ display: inline-block; width: 88%; height: 100%; }

img {
    background: black;
    vertical-align: middle;
    max-height: 80vh;
    max-width: 88vw;
}
</style>


<div id="overlay_menu_cover" style="width: 100vw;position: absolute;top: 0px;color: white; background-color: black; opacity: 0.6;height: 53px;color: white; display: none"></div>
<div class="overlay_container" id="overlay">
 <header><a style="top: -13px; position: relative;" id="overlay_title">TEST TITLE</a></header>
 <div>
	<div class="overlay_column_center">
	<!-- overlay content goes here -->
	<iframe id="overley_iframe" style="margin: auto;display: block;src=;width: 98%;height: 84vh;background-color: white;border: 1px;" src=""></iframe>
	</div>
 </div>
 <footer id="overlay_footer"></footer>
 <div id="overlay_menu" style="width: 100vw;position: absolute;top: 0px;color: white; opacity: 1.0;height: 53px;color: white; display: block">
    <div class="overlay_exit_btn_div" onclick="closeOverlay();" style="position: relative; top: -5px;width: 30px; margin-right: 8px; margin-top: 8px; float: right;cursor: pointer;">
	<svg viewBox="0 0 24 24" preserveAspectRatio="xMidYMid meet" style="display: block; width: 100%; height: 100%;">
	    <g><path id="overlay_exit_btn" class="svg_controls" d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path></g>
	</svg>
    </div>
 </div>
</div>