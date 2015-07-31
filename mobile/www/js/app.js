var mainUrl = "http://kirdyk.radier.ca/";
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




function onAppReady() 
{
    if( navigator.splashscreen && navigator.splashscreen.hide ) 
    {   // Cordova API detected
        navigator.splashscreen.hide();
    }
    checkUserProfile();
    loadRootThreadsPlus();
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
        li.innerHTML= badgeHtml+"<a href='#messagePage' onclick='javascript:currentLevel++;displayMessage("+data.threads[i].message.id+")'><b>"+data.threads[i].message.author.name+"</b> wrote:<br /><span style='color:#0088d1'>"+data.threads[i].message.subject+"</span></a>";
        
        titleList.appendChild(li);        
    }           
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
        if(data.messages[i].answers > 0)
        {
            badgeHtml= "<span class='af-badge tr'>"+data.messages[i].answers+"</span>";          
        }
        //Append the title to the list
        li = document.createElement('li');
        li.setAttribute('class','widget uib_w_7');
        li.setAttribute('data-uib','app_framework/listitem');
        li.innerHTML= badgeHtml+ "<a href='#messagePage' onclick='javascript:currentLevel="+data.messages[i].level+";displayMessage("+data.messages[i].id+")'><b>"+data.messages[i].author.name+"</b> wrote: <br /><span style='color:#0088d1'>"+subj+"</span><br /></a></br></div>";
                          
        replyTitleList.appendChild(li);                
    }               
}

//By Date
//example: http://kirdyk.radier.ca/api/messages?mode=bydate&id=444842&count=5
//this will change when proper authentication is implemented and it's possible to determine
//which messages were posted since the last check.
//for now we just show last 30.
function byDate(howMany)
{
    currentView="byDate";
    var url = mainUrl+ "api/messages?mode=bydate&count=" + howMany;
    
    
    titleList = document.getElementById("byDateList");
    var apiCall = $.get(url, function(data) {byDateCallback(data);}); 
    currentLevel=0; //we are on the top level of the tree
}
function byDateCallback(payload)
{
    //clear the list
    $("#byDateList").empty();
    //var data = $.parseJSON(payload);
    var data = payload;
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
        //Append the title to the list
        li = document.createElement('li');
        li.setAttribute('class','widget uib_w_7');
        li.setAttribute('data-uib','app_framework/listitem');
        li.innerHTML= badgeHtml+ "<a href='#messagePage' onclick='javascript:currentLevel="+data.messages[i].level+";displayMessage("+data.messages[i].id+")'><b>"+data.messages[i].author.name+"</b> wrote on "+data.messages[i].created+": <br /><span style='color:#0088d1'>"+subj+"</span><br /></a></br></div>";
                          
         titleList.appendChild(li);       
    }
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


function test()
{
    var isLoggedIn = checkUserProfile();
    alert(isLoggedIn);
}

















//the user clicks on the title, then we display the message body in the pop-up
function displayMessage (messageId)
{
    currentView="message";    
    var url = mainUrl+"api/messages/" + messageId;
    var apiCall = $.get(url, function(data) {displayMessageCallback(data);}); 
}

function displayMessageCallback(payload)
{
    //var data = $.parseJSON(payload);
    var data = payload;
    var msg = data.body.html;
    ///alert(msg);
    //$.ui.popup(msg);
    var subj = data.subject;
    var name = data.author.name;
    currentParentID = data.parent;    
    document.getElementById('msgBody').innerHTML = "<br /><b>"+name+ "</b> wrote: " + data.created + "<br /><br /><span style='color:#0088d1'><b>"+subj+"</b></span><br /><br />"+msg + "<br /> <br /><span style='color:#008800'> Likes: " + data.likes + "</span>&nbsp;&nbsp;&nbsp;<span style='color:#880000'> Dislikes: " + data.dislikes + "</span><br><br /><a class='button' href='#replyForm'>Reply</a>";

    //display replies here if any    
    $("#replyTitleList").empty();    //clear the list
    showReplies(data.id);
    currentMessageId= data.id;
    //duplicate the subj in the reply form
    document.getElementById("subjectTextBox").value=subj;
    document.getElementById("messageTextAreaQuote").innerHTML=msg;
    document.getElementById("inResponseTo").innerHTML="In response to "+name +"'s message:";
}

//the user clicks Reply button - navigate to the form (the subj and msg data is preloaded in displayMessage()). Send reply would pick up the form data and submit to the REST method
function sendReply()
{
    //API call: POST http://serverURL/api/messages/$id/answers
    //POST structure: {"subject":"subject goes here", "body":"this is a message body", "ticket":"some unique string to prevent duplicates", "nsfw":true}
   var url = mainUrl+ "api/messages/"+currentMessageId+"/answers";   
    //Time ticks
    var d = new Date();
    var n = d.getTime();
    var message = document.getElementById("messageTextArea").value+"\n\nSent from my phone.";
    var subj = document.getElementById("subjectTextBox").value;
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
            var newMessageId = data.id;
            //open the new message
            currentLevel++;
            displayMessage(newMessageId);
        }
    )
    .fail(function() {
        //alert( "error" );
        //Failed to post. Not logged in most likely    
        //Clear the page. Explain that one has to login.
        msg="You have to login. <br><a href='#login'>Click here to login</a>";
        $.ui.popup(msg);
    });
}

function postCallBack()
{
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
        displayMessage(currentParentID);        
    }
}