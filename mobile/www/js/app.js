var mainUrl = "http://kirdyk.radier.ca/";
var listStuff;

function onAppReady() {
    if( navigator.splashscreen && navigator.splashscreen.hide ) {   // Cordova API detected
        navigator.splashscreen.hide() ;
    }
}
document.addEventListener("app.Ready", onAppReady, false) ;



function callKitchen(threadId)
{
    var url = mainUrl+ "api/threads";
    if(null != threadId){url=url+"/"+threadId}
    listStuff = document.getElementById("stuffs_list");
    var apiCall = $.get(url, function(data) {kitchenCallback(data);}); 
}

function kitchenCallback(payload) 
{
    //clear the list
    $("#stuffs_list").empty();
    var data = $.parseJSON(payload);
    for(i=0; i<data.count; i++)
    {
        var subj = data.threads[i].message.subject;
        //check the number of replies. if >0 then render the badge
        var badgeHtml ="";
        if(data.threads[i].counter > 0)
        {
            badgeHtml= "<span class='af-badge tl'>"+data.threads[i].counter+"</span>";
        }
        //Append the title to the list
        var li = document.createElement('li');
        li.setAttribute('class','widget uib_w_7');
        li.setAttribute('data-uib','app_framework/listitem');
        li.innerHTML= badgeHtml +  "<a href='#' onclick='javascript:displayMessage("+data.threads[i].message.id+")'>"+subj+"<br /><b>"+data.threads[i].message.author.name+"</b></a><br/><div onclick='javascript:showReplies("+data.threads[i].message.id+");'>See replies </div>   ";                
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
    var data = $.parseJSON(payload);
    for(i=0; i<data.count; i++)
    {
        var subj = data.messages[i].subject;
        //check the number of replies. if >0 then render the badge
        var badgeHtml ="";
        var replyLinkHtml="";
        if(data.messages[i].answers > 0)
        {
            badgeHtml= "<span class='af-badge tl'>"+data.messages[i].answers+"</span>";
            replyLinkHtml="<br/><p onclick='javascript:showReplies("+data.messages[i].id+");'>See replies&nbsp;&nbsp;&nbsp;</p>"
        }
        var parentHTML ="<p onclick='javascript:showReplies("+data.messages[i].parent+");'> Level up</p>"
        //Append the title to the list
        var li = document.createElement('li');
        li.setAttribute('class','widget uib_w_7');
        li.setAttribute('data-uib','app_framework/listitem');
        li.innerHTML= badgeHtml +  "<a href='#' onclick='javascript:displayMessage("+data.messages[i].id+")'>"+subj+"<br /><b>"+data.messages[i].author.name+"</b></a>" + replyLinkHtml + parentHTML;                
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
    document.getElementById('openModal').innerHTML = msg;
    
}
    

