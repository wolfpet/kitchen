//top level HTML5 UI controls for frameless user inteface

// - - - - - PHOTO GALLERY - - - - - -

var currentPhotoIndex=0;
var currentAuthor=0;
var currentMsg=0;
var galleryImages = [];

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

function openPicInGallery(img, userId, messageId)
{
    //This function is called on click on an image. It opens the gallery and displace the pic
    //alert(img.currentSrc + '-' + userId + '-' + messageId);
    var currentPic = [img.currentSrc, userId, messageId];
    $.post("gallery_api.php", {'get_imgUrls_and_Posts' : userId}, function(data,status)
    {
                    galleryImages = $.map(data, function(value, index) {
                    return [value];
		    });
		    currentPhotoIndex=0;
        	    document.getElementById("currentPhoto").src = img.currentSrc;
                                                
    });
    currentAuthor = userId;
    currentMsg = messageId;
    //display
    document.getElementById("gallery_title").text = "Photo Gallery: Selected image";
    document.getElementById("gallery_author_name").text ="";
    document.getElementById("gallery_msg_title").text = "Return to the post";
                        
    //display the UI
    document.getElementById('gallery').style.display = 'block';
    document.getElementById('menu_cover').style.display = 'block';
            
}

function closeGallery()
{
    document.getElementById('gallery').style.display = 'none';
    document.getElementById('menu_cover').style.display = 'none';
}

function nextPhoto()
{
    if((currentPhotoIndex + 1) < galleryImages.length)
        {
                currentPhotoIndex++;
                document.getElementById("currentPhoto").src = galleryImages[currentPhotoIndex][0];
                document.getElementById("gallery_author_name").text = galleryImages[currentPhotoIndex][1];
                document.getElementById("gallery_msg_title").text = galleryImages[currentPhotoIndex][2];
                currentMsg = galleryImages[currentPhotoIndex][3];
                currentAuthor = galleryImages[currentPhotoIndex][4];
        }
}

function prevPhoto()
{
    if((currentPhotoIndex - 1) < 0)return;
    currentPhotoIndex--;
    document.getElementById("currentPhoto").src = galleryImages[currentPhotoIndex][0];
    document.getElementById("gallery_author_name").text = galleryImages[currentPhotoIndex][1];
    document.getElementById("gallery_msg_title").text = galleryImages[currentPhotoIndex][2];
    currentMsg = galleryImages[currentPhotoIndex][3];
    currentAuthor = galleryImages[currentPhotoIndex][4];
}

function filterByName(userId)
{
    document.getElementById("gallery_title").text = galleryImages[currentPhotoIndex][1] + "'s Photo Gallery. ";
    $.post("gallery_api.php", {'get_imgUrls_and_Posts' : userId}, function(data,status)
    {
        galleryImages = $.map(data, function(value, index) {
        return [value];
    });
        currentPhotoIndex=0;
        document.getElementById("currentPhoto").src = galleryImages[currentPhotoIndex][0];
    });
}


function initGalleryImages()
{
    currentPhotoIndex=0;
    document.getElementById("gallery_title").text = "Photo Gallery: images from all users";
    $.post("gallery_api.php", {'get_imgUrls_and_Posts' : 'all'}, function(data,status)
    {
        galleryImages = $.map(data, function(value, index) {
        return [value];
        });
        document.getElementById("currentPhoto").src = galleryImages[currentPhotoIndex][0];
        document.getElementById("gallery_author_name").text = galleryImages[currentPhotoIndex][1];
        document.getElementById("gallery_msg_title").text = galleryImages[currentPhotoIndex][2];
        currentMsg = galleryImages[currentPhotoIndex][3];
        currentAuthor = galleryImages[currentPhotoIndex][4];
    });
}

function openMsg(msgId)
{
    closeGallery();
    document.getElementById("bottom").src="msg.php?id=" + currentMsg;
}


// - - - - Overlay - - - - - 

function openOverlay(name)
{

    //display the Overlay UI.
    if(name=='preview')
    {
	//check if subject is specified
	if(bottom.document.getElementById('subj').value=='')
	{
	    bottom.document.getElementById("subj_div").style.color='red';
	    return;
	}
	
	document.getElementById('overlay').style.display = 'block';
	document.getElementById('overlay_menu_cover').style.display = 'block';
	//submit the message form and display the result in the overley iframe
	//bottom.togglePreview();
	//bottom.document.getElementById("msgform").target = document.getElementById("overley_iframe");
	//bottom.document.getElementById("msgform").submit();
	document.getElementById("overlay_title").text="Message Preview";
	document.getElementById("overley_iframe").src="overlay_post_form_clone.php";
    }

}
function closeOverlay()
{
    document.getElementById('overlay').style.display = 'none';
    document.getElementById('overlay_menu_cover').style.display = 'none';
    document.getElementById("overley_iframe").src="welc.php";
}
