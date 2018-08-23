//top level HTML5 UI controls for frameless user inteface

// - - - - - PHOTO GALLERY - - - - - -

var verticalLayout=false;
var currentPhotoIndex=0;
var currentAuthor=0;
var currentMsg=0;
var galleryImages = [];

//control the layout
window.onload = function(e){
//check the layout cookie
verticalLayout = getCookie("verticalLayout");
if(verticalLayout=='true')setVerticaLayout();
}

function getCookie(cname) 
{
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) 
    {
        var c = ca[i];
        while (c.charAt(0) == ' ') 
        {
                    c = c.substring(1);
        }
        if (c.indexOf(name) == 0) 
        {
           return c.substring(name.length, c.length);
        }
    }
    return "";
}
function setVerticaLayout()
{
    verticalLayout=true;
    document.getElementById('frame1').style.float = 'left';
    document.getElementById('frame1').style.width = '50vw';
    document.getElementById('frame1').style.height = '90vh';
    document.getElementById('frame1').style.position = 'static';
    document.getElementById('frame2').style.float = 'right';
    document.getElementById('frame2').style.width = '48vw';
    document.getElementById('frame2').style.height = '90vh';
    document.getElementById('hr1').style.display = 'none';
    document.getElementById('slider').style.display = 'none';    
    document.getElementById('slider-area').style.display = 'none';
    //disable vertical layout button. It's already vertical.
    document.getElementById('Vertical').style.display = 'none';
    document.getElementById('Horizontal').style.display = 'inline-block';
    //save a cookie
    document.cookie = "verticalLayout=true; expires=01 Jan 2040 12:00:00 UTC; path=/";
}
function setHorizontalLayout()
{
    document.getElementById('frame1').style.float = null;
    document.getElementById('frame1').style.width  = '100vw';
    document.getElementById('frame1').style.height = '40vh';
    document.getElementById('frame2').style.float = null;
    document.getElementById('frame2').style.width = '100vw';
    document.getElementById('frame2').style.height = '48vh';
    document.getElementById('hr1').style.display = 'block';
    document.getElementById('slider').style.display = 'block';    
    document.getElementById('slider-area').style.display = 'block';
    //disable horizontal  layout button. It's already vertical.
    document.getElementById('Vertical').style.display = 'inline-block';
    document.getElementById('Horizontal').style.display = 'none';
    //save a cookie
    document.cookie = "verticalLayout=false; expires=01 Jan 2040 12:00:00 UTC; path=/";
}

function openGallery()
{

    //initiate the gallery
    initGalleryImages();
    //display the Gallery UI.
    document.getElementById('gallery').style.display = 'block';
    document.getElementById('menu_cover').style.display = 'block';
    document.getElementById('slider').style.display = 'none';
    document.getElementById('slider-area').style.display = 'none';
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

    //turn off the resizer
    document.getElementById('slider').style.display = 'none';    
    document.getElementById('slider-area').style.display = 'none';
            
}

function closeGallery()
{
    document.getElementById('gallery').style.display = 'none';
    document.getElementById('menu_cover').style.display = 'none';
    document.getElementById('slider').style.display = 'block';
    document.getElementById('slider-area').style.display = 'block';
            
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


function openReply(replyUrl)
{
    openOverlay("replyForm");
    document.getElementById("overley_iframe").src=replyUrl;
}

// - - - - Overlay - - - - - 

function openOverlay(name)
{

    //message preview
    if(name=='preview')
    {
	//check if subject is specified
	if(bottom.document.getElementById('subj').value=='')
	{
	    bottom.document.getElementById("subj_div").style.color='red';
	    return;
	}
	renderOverlayPanels("Message Preview")
	document.getElementById("overley_iframe").src="overlay_post_form_clone.php";
    }
    //Private Mail 2.0
    if(name=='pm')
    {
        renderOverlayPanels("Private Chat")
	document.getElementById("overley_iframe").src="pm2.php";
    }
    //Poll
    if(name=='newPoll')
    {
	renderOverlayPanels("Add New Poll")
	document.getElementById("overley_iframe").src="polls_new_form.php";
    }
    //Profile settings
    if(name=='profile')
    {
	renderOverlayPanels("Profile Settings")
	document.getElementById("overley_iframe").src="profile.php";
    }
    //Login Form
    if(name=='loginForm')
    {
        renderOverlayPanels("Forum Login")
        document.getElementById("overley_iframe").src="login_form.php";
    }
    //New thread Form
    if(name=='newThreadForm')
    {
        renderOverlayPanels("New Thread")
        document.getElementById("overley_iframe").src="new.php";
    }
    //New thread Form
    if(name=='replyForm')
    {
        renderOverlayPanels("Reply")
    }
    
}
function closeOverlay()
{
    document.getElementById('overlay').style.display = 'none';
    document.getElementById('overlay_menu_cover').style.display = 'none';
    document.getElementById("overley_iframe").src="blank.php";
    document.getElementById('slider').style.display = 'block';
    document.getElementById('slider-area').style.display = 'block';
}
function renderOverlayPanels(title)
{

        closeNotifications();
        document.getElementById('overlay').style.display = 'block';
        document.getElementById('overlay_menu_cover').style.display = 'block';
        document.getElementById('slider').style.display = 'none';
        document.getElementById('slider-area').style.display = 'none';
        document.getElementById("overlay_title").text=title;
}