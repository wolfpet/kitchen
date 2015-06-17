var mainUrl = "http://kirdyk.radier.ca/";
var titleList;
var currentLevel=0;

function onAppReady() {
    if( navigator.splashscreen && navigator.splashscreen.hide ) {   // Cordova API detected
        navigator.splashscreen.hide() ;
    }
    loadRootThreads()
}
document.addEventListener("app.Ready", onAppReady, false) ;



function loadRootThreads()
{
    currentLevel=0;
    
    var url = mainUrl+ "api/threads";
    //if(null != threadId){url=url+"/"+threadId}
    titleList = document.getElementById("titleList");
    var apiCall = $.get(url, function(data) {loadRootThreadsCallback(data);}); 
    currentLevel=0; //we are on the top level of the tree
}

function loadRootThreadsCallback(payload) 
{
    
    //clear the list
    $("#titleList").empty();
    var data = $.parseJSON(payload);
    for(i=0; i<data.count; i++)
    {
        var subj = data.threads[i].message.subject;
        //check the number of replies. if >0 then render the badge
        var badgeHtml ="";
        var repliesLinkHtml="";
        var readMsgButtonHtml ="<div class='button-grouped'>";
        if(data.threads[i].counter > 0)
        {
            repliesLinkHtml="<div class='button' onclick='javascript:showReplies("+data.threads[i].message.id+");'><span class='af-badge tl'>"+data.threads[i].counter+"</span>&nbsp;&nbsp;&nbsp;&nbsp;Show replies </div>"
        }
        readMsgButtonHtml = readMsgButtonHtml + repliesLinkHtml + "</div>";
        //Append the title to the list
        var li = document.createElement('li');
        li.setAttribute('class','widget uib_w_7');
        li.setAttribute('data-uib','app_framework/listitem');
        li.innerHTML= "<a href='#messagePage' onclick='javascript:displayMessage("+data.threads[i].message.id+")'><b>"+data.threads[i].message.author.name+"</b> wrote:<br /><span style='color:#0088d1'>"+subj+"</span><br /></a></br>"+readMsgButtonHtml;                
        titleList.appendChild(li);        
    }           
}

//see the current level replies
function showReplies(messageID)
{
    currentLevel++;
    var url = mainUrl+"api/messages/" + messageID +"/answers";   
    titleList = document.getElementById("titleList");
    var apiCall = $.get(url, function(data) {showRepliesCallback(data);}); 
    
}

function showRepliesCallback(payload) 
{
    //clear the list
    $("#titleList").empty();
    //clear the message div as well
    document.getElementById('msgBody').innerHTML="";
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
            badgeHtml= "<span class='af-badge tl'>"+data.messages[i].answers+"</span>";
            showRepliesHtml="<div class='button' onclick='javascript:showReplies("+data.messages[i].id+");'>"+badgeHtml+">&nbsp;&nbsp;&nbsp;Show Replies</div>";            
        }
        readMsgButtonHtml = readMsgButtonHtml + showRepliesHtml;
        
        var parentHTML ="<div class='button' onclick='javascript:climbLevelUpTheTree("+data.messages[i].parent+");'>Level Up</div>"
        //Append the title to the list
        var li = document.createElement('li');
        li.setAttribute('class','widget uib_w_7');
        li.setAttribute('data-uib','app_framework/listitem');
        li.innerHTML= "<a href='#messagePage' onclick='javascript:displayMessage("+data.messages[i].id+")'><b>"+data.messages[i].author.name+"</b> wrote: <br /><span style='color:#0088d1'>"+subj+"</span><br /></a></br>"+readMsgButtonHtml + parentHTML+"</div>";
                          
        titleList.appendChild(li);                
    }               
}


//climbing one level up the tree requires 2 calls currently in order to retrieve the grand parent ID
function climbLevelUpTheTree(parentID)
{
   // This function must load the parent message. Then read the Grand Parent ID. If 0 then load root threads. If non 0 then load its replies.
   // Perhaps there is a better way but I can't think of any right now. (PW).
    currentLevel--;
    var url = mainUrl+"api/messages/" + parentID;
    var apiCall = $.get(url, function(data) {climbLevelUpTheTreeCallback(data);}); 
}
function climbLevelUpTheTreeCallback(payload)
{
    var data = $.parseJSON(payload);
    var grandparent = data.parent;
    if(grandparent==0){loadRootThreads();}
    else {showReplies(grandparent);}
}

//the user clicks on the title, then we display the message body in the pop-up
function displayMessage (messageId)
{
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
    document.getElementById('msgBody').innerHTML = "<br />"+name+ " wrote: <br /><br /><b>"+subj+"</b><br /><br />"+msg;
    if(msg==""){document.getElementById('msgBody').innerHTML = "<br/><center>EMPTY MESSAGE</center>";}
    
}
    

