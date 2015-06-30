var mainUrl = "http://kirdyk.radier.ca/";
var titleList;
var currentParentID=0;
var currentLevel=0;
var currentView="threads"; //"message", "byDate"

function onAppReady() 
{
    if( navigator.splashscreen && navigator.splashscreen.hide ) 
    {   // Cordova API detected
        navigator.splashscreen.hide() ;
    }
    loadRootThreadsPlus()
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
    var data = $.parseJSON(payload);
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
        li.innerHTML= badgeHtml+"<a href='#messagePage' onclick='javascript:currentLevel++;displayMessage("+data.threads[i].message.id+")'><b>"+data.threads[i].message.author.name+"</b> wrote:<br /><span style='color:#0088d1'>"+data.threads[i].message.subject;+"</span></a>";
        
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
    var data = $.parseJSON(payload);
    if(data.count>0)
    {
        //if there are replies then we add the list header
        var li;
        li = document.createElement('li');
        li.setAttribute('class','divider');
        replyTitleList.appendChild(li);
        li = document.createElement('li');
        li.setAttribute('class','widget uib_w_7');
        li.setAttribute('data-uib','app_framework/listitem');
        li.innerHTML="<center><b>"+data.count+" REPLIES</b></center>"
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
        li.innerHTML= badgeHtml+ "<a href='#messagePage' onclick='javascript:currentLevel++;displayMessage("+data.messages[i].id+")'><b>"+data.messages[i].author.name+"</b> wrote: <br /><span style='color:#0088d1'>"+subj+"</span><br /></a></br></div>";
                          
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
    var data = $.parseJSON(payload);
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
        li.innerHTML= badgeHtml+ "<a href='#messagePage' onclick='javascript:currentLevel++;displayMessage("+data.messages[i].id+")'><b>"+data.messages[i].author.name+"</b> wrote on "+data.messages[i].created+": <br /><span style='color:#0088d1'>"+subj+"</span><br /></a></br></div>";
                          
         titleList.appendChild(li);       
    }
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
    var data = $.parseJSON(payload);
    var msg = data.body.html;
    ///alert(msg);
    //$.ui.popup(msg);
    var subj = data.subject;
    var name = data.author.name;
    currentParentID = data.parent;
    document.getElementById('msgBody').innerHTML = "<br /><b>"+name+ "</b> wrote: " + data.created + "<br /><br /><span style='color:#0088d1'><b>"+subj+"</b></span><br /><br />"+msg + "<br /> <br /><span style='color:#008800'> Likes: " + data.likes + "</span>&nbsp;&nbsp;&nbsp;<span style='color:#880000'> Dislikes: " + data.dislikes + "</span><br><br />";

    //display replies here if any    
    $("#replyTitleList").empty();    //clear the list
    showReplies(data.id);
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