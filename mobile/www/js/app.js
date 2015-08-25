//TODO: we gotta change this so it's auto-detected
var mainUrl = "http://kirdyk.radier.ca/";

//navigation
var titleList;
var currentParentID=0;
var currentLevel=0;
var currentView="threads"; //"message", "byDate"

//Auth data
var username = null;
var password = null;
var tempvar=null;

//Reply To
var currentMessageId = null;
var replyPrivate = false;


function onAppReady() 
{
    if( navigator.splashscreen && navigator.splashscreen.hide ) 
    {   // Cordova API detected
        navigator.splashscreen.hide();
    }
    checkUserProfile();
    loadRootThreadsPlus();
    //windows phone menu fix
    if(navigator.userAgent.match(/Windows Phone/i) || navigator.userAgent.match(/WPDesktop/i))
    {
        //make menu visible
        $("#menu").css('z-index', 999);
         //remove metro menu!
        var element = document.getElementById("metroMenu");
        element.parentNode.removeChild(element);
    }
   
}
document.addEventListener("app.Ready", onAppReady, false) ;

function loadRootThreadsPlus()
{
    currentLevel=0;
    var url = mainUrl+ "api/threads";
    titleList = document.getElementById("titleList");
    var apiCall = $.get(url, function(data) {loadRootThreadsCallbackPlus(data);}); 
    currentLevel=0; //we are on the top level of the tree
}

function loadRootThreadsCallbackPlus(payload) 
{
    //clear the list
    $("#titleList").empty();
    //var data = $.parseJSON(payload);
    var data = payload;
    for(i=0; i<data.count; i++)
    {
        //check the number of replies. if >0 then render the badge
        var badgeHtml ="";
        if(data.threads[i].counter > 0)
        { 
            badgeHtml="<span class='af-badge tr'>"+data.threads[i].counter+"</span>";
        }
        //Append the title to the list
        var li = document.createElement('li');
        li.setAttribute('class','widget uib_w_7');
        li.setAttribute('data-uib','app_framework/listitem');
        li.innerHTML= badgeHtml+"<a href='#messagePage' onclick='javascript:currentLevel++;displayMessage("+data.threads[i].message.id+", false)'><b>"+data.threads[i].message.author.name+"</b> :<br /><span style='color:#0088d1'>"+data.threads[i].message.subject+"</span></a>";
        
        titleList.appendChild(li);        
    }           
    win8MenuFix();
}

//see the current level replies
function showReplies(messageID)
{   
    var url = mainUrl+"api/messages/" + messageID +"/answers";   
    replyTitleList = document.getElementById("replyTitleList");
    var apiCall = $.get(url, function(data) {showRepliesCallback(data);});     
}

function showRepliesCallback(payload) 
{    
    //clear the list
    $("#replyTitleList").empty();
    //var data = $.parseJSON(payload);
    var data = payload;
    var li;
    if(data.count>0)
    {
        //if there are replies then we add the list header
        
        li = document.createElement('li');
        li.setAttribute('class','divider');
        replyTitleList.appendChild(li);
        li = document.createElement('li');
        li.setAttribute('class','widget uib_w_7');
        li.setAttribute('data-uib','app_framework/listitem');
        li.innerHTML="<center><b>"+data.count+" REPLIES</b></center>";
        replyTitleList.appendChild(li);
    
        li = document.createElement('li');
        li.setAttribute('class','divider');
        replyTitleList.appendChild(li);
    }
    for(i=0; i<data.count; i++)
    {
        var subj = data.messages[i].subject;
        //check the number of replies. if >0 then render the badge
        var badgeHtml ="";
        var replyLinkHtml="";
        var showRepliesHtml="";
        var readMsgButtonHtml ="<div class='button-grouped'>";
        var replyLevel = data.messages[i].level +1;
        if(data.messages[i].answers > 0)
        {
            badgeHtml= "<span class='af-badge tr'>"+data.messages[i].answers+"</span>";          
        }
        //Append the title to the list
        li = document.createElement('li');
        li.setAttribute('class','widget uib_w_7');
        li.setAttribute('data-uib','app_framework/listitem');
        li.innerHTML= badgeHtml+ "<a href='#messagePage' onclick='javascript:currentLevel="+replyLevel+";displayMessage("+data.messages[i].id+", false)'><b>"+data.messages[i].author.name+"</b> : <br /><span style='color:#0088d1'>"+subj+"</span><br /></a></br></div>";
                          
        replyTitleList.appendChild(li);                
    }               
}


//By Date view
function byDate2(count)
{
    loadFilteredMessages("api/messages?mode=bydate&count="+count, "byDateList", "byDate", false);
}

//Answered view
function answered(count)
{
    loadFilteredMessages("api/messages?mode=answered&count=" + count, "answeredList", "answered",false);
}

//my messages view
function myMessages(count)
{
     loadFilteredMessages("api/messages?mode=mymessages&count=" + count, "myMessagesList", "myMessages",false);
}

//Inbox view
function getInbox()
{
    //loadinbox
    loadFilteredMessages("api/inbox", "myInboxList", "inboxContent", true);
}

//Sent view
function getSent()
{
    //loadinbox
    loadFilteredMessages("api/sent", "myInboxList", "inboxContent", true);
}


//universal function that loads the list from given REST call to given html list.
function loadFilteredMessages(restSubUrl, listName, viewName, private)
{
    currentView = viewName;
    var url = mainUrl+ restSubUrl;    
    titleList = document.getElementById(listName); //e.g listName=byDateList
    var apiCall = $.get(url, function(data) {loadFilteredMessagesCallback(data, listName, private);}); 
}
function loadFilteredMessagesCallback(payload, titleListname, private)
{
    //clear the list
    titleList = document.getElementById(titleListname); 
    $("#" + titleListname).empty();
    var data = payload;
    var li;
    //inbox Sent for PMail
    if(private)
    {
       li = document.createElement('li');
       li.innerHTML= "<center><a class='button' onclick='getInbox();'>Inbox</a>&nbsp;&nbsp;&nbsp;<a class='button' onclick='getSent();'>Sent</a>&nbsp;&nbsp;&nbsp;<a class='button' onclick='prepForNewPM();'  href='#replyForm'>New PM</a></center>";
        titleList.appendChild(li);
        //make the new PM recipient field visible:
        $("#recipient").css('display', 'block');
    }
    //load content
    for(i=0; i<data.count; i++)
    {
        var subj = data.messages[i].subject;
        //check the number of replies. if >0 then render the badge
        var badgeHtml ="";
        var replyLinkHtml="";
        var showRepliesHtml="";
        var readMsgButtonHtml ="<div class='button-grouped'>";
        if(data.messages[i].answers > 0)
        {
            badgeHtml= "<span class='af-badge tr'>"+data.messages[i].answers+"</span>";          
        }
        
        var author;
        try{
            author = "<b>" + data.messages[i].author.name + "</b> wrote on ";
        }catch(err){author=undefined;}
        if(author == undefined)
        {
            //probably Sent folder in PM.
            author = "I wrote to <b>" + data.messages[i].recipient.name + "</b> on ";
        }
        
        //Append the title to the list
        li = document.createElement('li');
        li.setAttribute('class','widget uib_w_7');
        li.setAttribute('data-uib','app_framework/listitem');
        li.innerHTML= badgeHtml+ "<a href='#messagePage' onclick='javascript:currentLevel="+data.messages[i].level+";displayMessage("+data.messages[i].id+", "+private+")'>"+ author +" "+data.messages[i].created+": <br /><span style='color:#0088d1'>"+subj+"</span><br /></a></br></div>";
                          
         titleList.appendChild(li);       
    }
    win8MenuFix();
}

function prepForNewPM()
{
    //clear the reply form just in case
    document.getElementById("subjectTextBox").value="";
    document.getElementById("messageTextAreaQuote").innerHTML="";
    document.getElementById("inResponseTo").innerHTML="";
    document.getElementById("recipient").value = "";
}

function checkUserProfile()
{
    checkUserProfileCall(username, password, function(data){ username = data.name;});
    if(username !== null)return true;
    else return false;
}

function checkUserProfileCall(username, password, success_function)
{
    var url = mainUrl+ "api/profile";
    $.ajax
    ({
      type: "GET",
      url: url,
      async: true,
      beforeSend: function (xhr) {
        if (username !== null) {
          xhr.setRequestHeader ("Authorization", "Basic " + btoa(username + ":" + password));
        }
      },    
      success: success_function        
  });
    
}

function userPageInit()
{
    //if the user is already logged in say something nice
    var hiStr = "<br>Hello <b>" +username + "</b>,<br>You are logged in.<br>Welcome to the forum!";
    if(username !=null)
    {
        document.getElementById("userPageContent").innerHTML=hiStr;
        //already logged in. Login form is not needed.
        $("#login_form").empty();  
        //no need to login so alter the header title
        document.getElementById("loginHeaderTitle").innerHTML="<h1>Welcome</h1>";
    }
    win8MenuFix();
}

function login()
{
    
    //submit the form to ../login.php
    var actionUrl = mainUrl+ "login.php";
    //post the message
    $.post
    (   actionUrl,
        {
            "user":document.getElementById("usernameTextBox").value, 
            "password":document.getElementById("pwdTextBox").value,
            "lastpage":"welc.php"            
        },
        function(data, status)
        {
            //reload threads
            //loadRootThreadsPlus();         
        }
    );   
    //check if successful.
    var url = mainUrl+ "api/profile";
    $.get( url, function( data ) {
    if(data.status == "ERROR")
    {
        $.ui.popup("Sorry, login was unsuccessful. Please try again.");
    }
    })
    .fail(function(){$.ui.popup("Sorry, login was unsuccessful. Please try again.");});    
}

function like(messageID)
{
    var url = mainUrl+ "api/messages/"+messageID+"/like";
    $.ajax({
            url: url,
            type: 'PUT'}
        );
    $.ui.popup("Liked the message "+ messageID);
}

function dislike(messageID)
{
    var url = mainUrl+ "api/messages/"+messageID+"/like";
    $.ajax({
            url: url,
            type: 'DELETE'}
        );
    $.ui.popup("Disliked the message "+ messageID);
}

//the user clicks on the title, then we display the message body in the pop-up
function displayMessage (messageId, private)
{
    replyPrivate = private; //the reply form will know which api to use.
    currentView="message";    
    var url="";
    if(private)
    {
        url=mainUrl+"api/inbox/" + messageId;
        //show the recipient field
        $("#recipient").css('display', 'block');
    }
    else 
    {
        url=mainUrl+"api/messages/" + messageId;
        //hide the recipient field
        $("#recipient").css('display', 'none');
    }    
    
    var apiCall = $.get(url, function(data) {displayMessageCallback(data);}); 
}

function displayMessageCallback(payload)
{
    //var data = $.parseJSON(payload);
    var data = payload;
    try{
        var test = data.body.html;
    }
    catch(err){$.ui.popup("Ooops! Something went wrong. Sorry about that!");return;}
    var msg = data.body.html;
    ///alert(msg);
    //$.ui.popup(msg);
    var subj = data.subject;
    var name = data.author.name;
    currentParentID = data.parent;
    //likes/dislikes html if not private msg
    var likesDislikesHTML="";     
    if(data.likes != undefined){ likesDislikesHTML="<br /><span style='color:#008800'><a onclick='like("+data.id+");'> Likes: " + data.likes + "</a></span>&nbsp;&nbsp;&nbsp;<span style='color:#880000'><a onclick='dislike("+data.id+");'> Dislikes: " + data.dislikes + "</a></span><br><br>";}
    
    //render the message
    document.getElementById('msgBody').innerHTML = "<br /><b>"+name+ "</b> wrote: " + data.created + "<br /><br /><span style='color:#0088d1'><b>"+subj+"</b></span><br /><br />"+msg + "<br /><br />" +likesDislikesHTML+"<a class='button' href='#replyForm'>Reply</a>";

    //display replies here if any    
    $("#replyTitleList").empty();    //clear the list
    showReplies(data.id);
    currentMessageId= data.id;
    //duplicate the subj in the reply form
    document.getElementById("subjectTextBox").value=subj;
    document.getElementById("messageTextAreaQuote").innerHTML=msg;
    document.getElementById("inResponseTo").innerHTML="In response to "+name +"'s message:";
    //update message level label
    if(currentLevel == undefined)currentLevel=1;
    document.getElementById("levelLabel").innerHTML = "Message (L:" + currentLevel + ")";
    document.getElementById("recipient").value = name;
    //cleanup footer
    win8MenuFix();
}

//the user clicks Reply button - navigate to the form (the subj and msg data is preloaded in displayMessage()). Send reply would pick up the form data and submit to the REST method
function sendReply()
{
    //API call: POST http://serverURL/api/messages/$id/answers
    //POST structure: {"subject":"subject goes here", "body":"this is a message body", "ticket":"some unique string to prevent duplicates", "nsfw":true}
    
    //api url
    var url = ""
    //Time ticks
    var d = new Date();
    var n = d.getTime();
    var message = document.getElementById("messageTextArea").value+"\n\nSent from my phone.";
    var subj = document.getElementById("subjectTextBox").value;
    var recipient = document.getElementById("recipient").value;
    if(subj=="")subj="No Subject";
    var responsePackage;
    if(replyPrivate)
    {
        url=mainUrl+"api/sent";
        responsePackage= JSON.stringify({
            "subject":subj, 
            "body":message,
            "ticket":n, 
            "nsfw":false,
            "recipient":recipient
        });
    }
    else
    {
        url=mainUrl+ "api/messages/"+currentMessageId+"/answers";
        responsePackage= JSON.stringify({
            "subject":subj, 
            "body":message,
            "ticket":n, 
            "nsfw":false,
        });
    }
    
    
    //post the private message
    $.post
    (url,
        responsePackage,
        function(data, status)
        {
            var newMessageId = data.id;
            //open the new message
            currentLevel++;
            displayMessage(newMessageId,replyPrivate);
        }
    )
    .fail(function() {
        //alert( "error" );
        //Failed to post. Not logged in most likely    
        //Clear the page. Explain that one has to login.
        msg="You have to login. <br><a href='#login'>Click here to login</a><br><br>";
        $.ui.popup(msg);
    });
}

//let's start the new thread!
function sendNewThread()
{
    var url = mainUrl+ "api/threads";   
    //Time ticks
    var d = new Date();
    var n = d.getTime();
    var message = document.getElementById("newThreadTextArea").value+"\n\nSent from my phone.";
    var subj = document.getElementById("newThreadSubjectTextBox").value;
    if(subj=="")subj="No Subject";
    //post the message
    $.post
    (url,
        JSON.stringify({
            "subject":subj, 
            "body":message,
            "ticket":n, 
            "nsfw":false
        }),
        function(data, status)
        {
            //reload threads
           loadRootThreadsPlus();         
        }
    )
    .fail(function() {
        //alert( "error" );
        //Failed to post. Not logged in most likely    
        //Clear the page. Explain that one has to login.
        msg="You have to login. <br><a href='#login'>Click here to login</a><br><br>";
        $.ui.popup(msg);
    });
}

//this function loads the root threads or goes one level up depending on where we are.
function onBackButton()
{
    
    
    //kill the message body content. Especially all sorts of players 
    //so they don't bother in the background.
    if(currentLevel==0)return; //already on top
    document.getElementById('msgBody').innerHTML="";
    if(currentLevel==1)
    {   
        //go to the root threads
        currentLevel--;
        $.ui.goBack();
    }
    else
    {   
        //level up
        currentLevel--;
        displayMessage(currentParentID,false);        
    }
    win8MenuFix();
}

function win8MenuFix()
{
        if(navigator.userAgent.match(/Windows Phone/i) || navigator.userAgent.match(/WPDesktop/i))
    {
         //remove metro menu!
        var element = document.getElementById("metroMenu");
        if(element != null)element.parentNode.removeChild(element);
    }
    
        var xxx = $("#metroMenu").parent(0);
        var yyy=0;
}