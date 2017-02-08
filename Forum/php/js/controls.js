//top level HTML5 UI controls for frameless user inteface

function setVerticaLayout()
{
    document.getElementById('frame1').style.float = 'left';
    document.getElementById('frame1').style.width = '50vw';
    document.getElementById('frame1').style.height = '90vh';
    document.getElementById('frame2').style.float = 'right';
    document.getElementById('frame2').style.width = '48vw';
    document.getElementById('frame2').style.height = '90vh';
    document.getElementById('hr1').style.display = 'none';
}

function openGallery()
{

    //initiate the gallery
    initGalleryImages();
    //display the Gallery UI.
    document.getElementById('gallery').style.display = 'block';
    document.getElementById('menu_cover').style.display = 'block';
    
}

function closeGallery()
{
    document.getElementById('gallery').style.display = 'none';
    document.getElementById('menu_cover').style.display = 'none';
}
    