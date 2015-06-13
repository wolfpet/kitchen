var mainUrl = "http://kirdyk.radier.ca/";
var listStuff;

function onAppReady() {
    if( navigator.splashscreen && navigator.splashscreen.hide ) {   // Cordova API detected
        navigator.splashscreen.hide() ;
    }
    loadRootThreads()
}
document.addEventListener("app.Ready", onAppReady, false) ;



function loadRootThreads()
{
    var url = mainUrl+ "api/threads";
    //if(null != threadId){url=url+"/"+threadId}
    listStuff = document.getElementById("stuffs_list");
    var apiCall = $.get(url, function(data) {loadRootThreadsCallback(data);}); 
}

function loadRootThreadsCallback(payload) 
{
    //clear the list
    $("#stuffs_list").empty();
    var data = $.parseJSON(payload);
    for(i=0; i<data.count; i++)
    {
        var subj = data.threads[i].message.subject;
        //check the number of replies. if >0 then render the badge
        var badgeHtml ="";
        var repliesLinkHtml="";
        var readMsgButtonHtml ="<div class='button-grouped'><a class='button' onclick='javascript:displayMessage("+data.threads[i].message.id+")'>Read Message</a>";
        if(data.threads[i].counter > 0)
        {
            repliesLinkHtml="<div class='button' onclick='javascript:showReplies("+data.threads[i].message.id+");'><span class='af-badge tl'>"+data.threads[i].counter+"</span>&nbsp;&nbsp;&nbsp;&nbsp;Show replies </div>"
        }
        readMsgButtonHtml = readMsgButtonHtml + repliesLinkHtml + "</div>";
        //Append the title to the list
        var li = document.createElement('li');
        li.setAttribute('class','widget uib_w_7');
        li.setAttribute('data-uib','app_framework/listitem');
        li.innerHTML= "<p onclick='javascript:displayMessage("+data.threads[i].message.id+")'><b>"+data.threads[i].message.author.name+"</b> wrote:<br /><span style='color:#0088d1'>"+subj+"</span><br /></p>"+readMsgButtonHtml;                
        listStuff.appendChild(li);        
    }           
}

//see the current level replies
function showReplies(messageID)
{
    var url = mainUrl+"api/messages/" + messageID +"/answers";   
    listStuff = document.getElementById("stuffs_list");
    var apiCall = $.get(url, function(data) {showRepliesCallback(data);}); 
}

function showRepliesCallback(payload) 
{
    //clear the list
    $("#stuffs_list").empty();
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
        var readMsgButtonHtml ="<div class='button-grouped'><div class='button' onclick='javascript:displayMessage("+data.messages[i].id+")'>Read Message</div>";
        if(data.messages[i].answers > 0)
        {
            badgeHtml= "<span class='af-badge tl'>"+data.messages[i].answers+"</span>";
            showRepliesHtml="<div class='button' onclick='javascript:showReplies("+data.messages[i].id+");'>"+badgeHtml+">&nbsp;&nbsp;&nbsp;&nbsp;Show Replies</div>";            
        }
        readMsgButtonHtml = readMsgButtonHtml + showRepliesHtml;
        
        var parentHTML ="<div class='button' onclick='javascript:showReplies("+data.messages[i].parent+");'>Back</div>"
        //Append the title to the list
        var li = document.createElement('li');
        li.setAttribute('class','widget uib_w_7');
        li.setAttribute('data-uib','app_framework/listitem');
        li.innerHTML= "<p onclick='javascript:displayMessage("+data.messages[i].id+")'><b>"+data.messages[i].author.name+"</b> wrote: <br /><span style='color:#0088d1'>"+subj+"</span><br /></p>"+readMsgButtonHtml + parentHTML+"</div>";
                          
        listStuff.appendChild(li);                
    }           
    
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
    document.getElementById('msgBody').innerHTML = msg;
    
}
    

