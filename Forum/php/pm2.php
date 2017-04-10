<?php
require_once('head_inc.php');
require_once('html_head_inc.php');
//require('pm2_msg_inc.php');


if ($logout === true) {
        header( "Location: http://$host$root_dir$page_expanded" ) ;
        die();
}

?>


<style>
/* Userlist */
.pmUsersLi {
    height: 50px;
    position: relative;
    border-bottom: 1px solid;
    background-color: #fff;
    list-style-type: none;
    overflow: hidden;
    padding: 3px;
}

.pmUsersLi:hover {
    cursor: pointer;
    background: #f0f0f0;
}

.pmUsers {

    float: left;
    height: 97vh;
    width: 30vw;
    border-style: solid;
    border-width: 1px;
    overflow-y: scroll;
    border-color: lightslategrey;
    min-width: 200px;
}



.pmusericon {

    top: 4px;
    left: -6px;
    float: left;
    width: 35px;
    position: relative;
}

.pmTime {
    font-size: xx-small;
    top: 4px;
    position: relative;
    color: grey;
}

.pmmsg {

    display: inline-block;
    border-style: solid;
    border-width: 1px;
    border-radius: 10px;
    background: lightgray;
    border-color: lightgray;
    padding: 5px;
    margin: 5px;
    max-width: 60vw;
}

.mymsg {
    float: right;
    background-color: lightgreen;
}

.PMConvos {
    width: 68vw;
    float: right;
    position: absolute;
    display: inline-block;
    border-style: solid;
    border-width: 1px;
    border-color: grey;
    border-left-style: none;
    height: 90vh;
    overflow-y: scroll;
    overflow-x: hidden;
}

.PMEntry {
    position: absolute;
    bottom: 8px;
    right: 5px;
    border-color: lightgrey;
    border-style: none;
    height: 6vh;
}
.PMEntryText {
    height: 5vh;
    width: 48vw;
    resize: none;
    cursor: pointer;
}
.PMSendBtn{
    width: 11vh;
    background-color: lightgrey;
    border-style: solid;
    position: relative;
    top: -13px;
    height: 6vh;
    cursor: pointer;
}

@media screen and (max-width: 600px) {
.pmUsers {position: absolute; top:1vh; height: 80vh; width: 96vw;}
.PMConvos {height: 15vh; width: 96vw; top: 82vh;    border-left-style: solid;}
.PMEntry {height: 16vh;}
.PMEntryText {height: 10vh;}
}

</style>
<script>
var currentSenderID=0;
var currentSenderName = "";
var timeoutId = 0;
var currentConvoLastMsgID = 0;
var allConvosLastMessageID = 0;

function openPMConvo(senderId, senderName)
{
    currentSenderID = senderId;
    currentSenderName= senderName;
    parent.document.getElementById('overlay_title').innerHTML='Conversation with '+ senderName;
    //highlight the current convo
    clearUserListStatus();
    document.getElementById('p' + senderId).style.fill='red';
    //clear the convo area and load the new conversation
    document.getElementById('Messages').innerHTML='';
    var url1 = "./pm2_api.php?senderid=" + senderId;
    console.log("calling pm api: ("+url1+")");
    var me = <?=$user_id?>;
    $.ajax({
         type: "GET",
         url: url1,
         success: function(events) {
            var msgArray = $.map(events, function(value, index) {
            return [value];
            });
            var count = msgArray.length;
            if(count < 1)return; //no events
            for(i=0; i<count; i++)
            {
              var pmTime = msgArray[i][5];
              var pmTitle = '<b>'+msgArray[i][3] +'</b><br>';
              //remove repetetive subject
              if(i>0 && i<count)
              {
                if((msgArray[i-1][3] == msgArray[i][3]) || (('Re: '+msgArray[i-1][3]) == msgArray[i][3]))pmTitle='';
              }
              if(msgArray[i][3]=='n/a')pmTitle='';

              //check if body exists
              var pmBody =  msgArray[i][4];
              if(pmBody==null)pmBody='';

              var myMsgClass = '';
              if(msgArray[i][1]==me)
              {
                //my message
                myMsgClass =' mymsg';
              }
               var delStr = '&nbps; &nbsp;<a style="color: red;font-size: xx-small; cursor: pointer" onclick="deletePM('+msgArray[i][0]+')">Delete</a>';
               var msgHTML = '<p id="'+ msgArray[i][0] +'" class="pmmsg'+myMsgClass+'">'+ pmTitle + pmBody + '<br><span style="font-size: xx-small; color: blue;">'+pmTime + delStr+'</span></p><br>';
              //add timing
              document.getElementById('Messages').innerHTML += msgHTML;
              currentConvoLastMsgID = msgArray[i][0]; //update the msg ID in the current convo to enable the auto load if the ping returns higher id
            }

            //scroll down to it
            $("#Messages").scrollTop($("#Messages")[0].scrollHeight);

            //enable entry point
            document.getElementById('PMEntry').style.display = 'block';
            document.getElementById('PMEntryText').value = 'Type your message here';
            document.getElementById('PMEntryText').style.cursor='pointer';
            //remove the user list on the phone
            if($(window).width() < 600)
            {
                document.getElementById('userlist').style.display = "none";
                document.getElementById('Messages').style.top = "2vh";
                document.getElementById('Messages').style.height = "78vh";
            }
        }
    });
}
function startNewConvo()
{
    document.getElementById('newConvo').style.display = 'block';
    document.getElementById('newConvoInvite').style.display = "none";
    //remove the user list on the phone
    if($(window).width() < 600)
    {
        document.getElementById('userlist').style.display = "none";
        document.getElementById('Messages').style.top = "2vh";
        document.getElementById('Messages').style.height = "78vh";
    }
}

function newConvoUserSelected()
{
    var elt = document.getElementById('newConvoUsers');

    currentSenderName = elt.options[elt.selectedIndex].text;
    currentSenderID = elt.options[elt.selectedIndex].value;
    parent.document.getElementById('overlay_title').innerHTML='Conversation with '+ currentSenderName;
    //hide non-convo stuff
    document.getElementById('newConvoUsers').style.display = "none";
    
    document.getElementById('PMEntry').style.display = 'block';
    document.getElementById('PMEntryText').value = 'Type your message here';
    document.getElementById('PMEntryText').style.cursor='pointer';

}

function startTyping(field)
{
    if(field.value== 'Type your message here'){field.value=''}; 
    field.style.cursor='text';
    //move the field on the top on a phone
    if($(window).width() < 600)
    {
       document.getElementById('PMEntry').style.bottom='120px';
       document.getElementById('PMEntry').style.right='20px';
       document.getElementById('PMEntryText').style.width='60vw';
       document.getElementById('PMSendBtn').style.top='-22px';
       document.getElementById('PMSendBtn').style.height='9vh';
    }
}

function sendMessage()
{
 //alert("sending to" + currentSenderName);
 var PMBody = document.getElementById('PMEntryText').value;
 if(PMBody == '')return;
 //add the msg to view
  var msgHTML = '<br><br><p id="newpm' +'" class="pmmsg mymsg">' + PMBody  + '</p><br>';
  document.getElementById('Messages').innerHTML += (msgHTML);
  //send the message
  $.ajax({
           type: "POST",
           url: "pm2_api.php",
           data: {body: PMBody, receiver:currentSenderID} ,
           success: function(data) {
           //$("#Messages").html(data); 
        }
  });

 //the end. Clear the field
 document.getElementById('PMEntryText').value ="";
 //scroll down to it
 $("#Messages").scrollTop($("#Messages")[0].scrollHeight);
    //move the field back on the phone
    if($(window).width() < 600)
    {
       document.getElementById('PMEntry').style.bottom='8px';
       document.getElementById('PMEntry').style.right='5px';
       document.getElementById('PMEntryText').style.width='48vw';
       document.getElementById('PMSendBtn').style.top='-13px';
       document.getElementById('PMSendBtn').style.height='6vh';
    }
}

function deletePM(id)
{
document.getElementById(id).style.display='none';
 //send the message
   $.ajax({
             type: "GET",
             url: "pm2_api.php",
             data: {action:'del', id:id} ,
             success: function(data) {
          }
    });
}

function ping()
{
   //check for new messages and update the convo if exist
   console.log("PM Ping!");
   var me = <?=$user_id?>;
   //return;
   $.ajax({
             type: "GET",
             url: "pm2_api.php",
             data: {action:'ping', senderid:currentSenderID, lastMsgId:currentConvoLastMsgID} ,
            success: function(events) {
            var msgArray = $.map(events, function(value, index) {
            return [value];
            });
            var count = msgArray.length;
            if(count < 1)return; //no events
            if(currentSenderID==0)
            {
              //we may have new messages but we are not in any convo yet.
              //1. exclude the first round
              if(allConvosLastMessageID == 0){allConvosLastMessageID=msgArray[0]; return;}
              else if(allConvosLastMessageID != msgArray[0])
              {
                //new messages have arrived
                msgHTML = '<br><br><center>You got new private messages since you opened PM. Reopen to check them. </center><br>';
                document.getElementById('Messages').innerHTML += msgHTML;
                $("#Messages").scrollTop($("#Messages")[0].scrollHeight);
                //update the global var so this doesn't happen until the next one arrives,
                allConvosLastMessageID = msgArray[0];
              }
              return;
            }
            for(i=0; i<count; i++)
            {
              var pmTime = msgArray[i][5];
              var pmTitle = '<b>'+msgArray[i][3] +'</b><br>';
              //remove repetetive subject
              if(i>0 && i<count)
              {
                if((msgArray[i-1][3] == msgArray[i][3]) || (('Re: '+msgArray[i-1][3]) == msgArray[i][3]))pmTitle='';
              }
              if(msgArray[i][3]=='n/a')pmTitle='';

              //check if body exists
              var pmBody =  msgArray[i][4];
              if(pmBody==null)pmBody='';

              var myMsgClass = '';
              if(msgArray[i][1]==me)
              {
                //my message
                myMsgClass =' mymsg';
              }
               var delStr = '&nbps; &nbsp;<a style="color: red;font-size: xx-small; cursor: pointer" onclick="deletePM('+msgArray[i][0]+')">Delete</a>';
               var msgHTML = '<p id="'+ msgArray[i][0] +'" class="pmmsg'+myMsgClass+'">'+ pmTitle + pmBody + '<br><span style="font-size: xx-small; color: blue;">'+pmTime + delStr+'</span></p><br>';
              //add timing
              document.getElementById('Messages').innerHTML += msgHTML;
              currentConvoLastMsgID = msgArray[i][0]; //update the msg ID in the current convo to enable the auto load if the ping returns higher id
            }

            //scroll down to it
            $("#Messages").scrollTop($("#Messages")[0].scrollHeight);
          }
    });
}
window.setInterval(ping, 10000); //ping for new msg in the current convo every 10 sec.

function clearUserListStatus()
{
    var x = document.getElementsByClassName("pm_path");
    for(i=0; i<x.length; i++)
    {
      x[i].style.fill='grey';
    }
}


</script>

</head>
<body style="overflow: hidden;">
<div id="userlist" class="pmUsers">

<?php
    $search_condition = '((p.sender=s.id and receiver=' . $user_id . ') or (receiver=s.id and p.sender=' . $user_id . '))';
    $search_condition .= ' and (p.status <>30)';
    $search_condition .= ' and !(p.sender='.$user_id.' AND p.status=22)';
    $search_condition .= ' and !(receiver='.$user_id.' AND p.status=28)';

    $out = '';
    $new_pm=0;
    update_new_pm_count($user_id);

    //Userlist
    $query = 'SELECT s.username as sender_name, p.id as id, p.sender as sender, p.receiver as receiver, count(p.subject) as msg_count,  max(CONVERT_TZ(p.created, \'' . $server_tz . '\', \''.$prop_tz.':00\')) as created,'
    . '  p.status,  p.chars  from confa_pm p, confa_users s where '
    . $search_condition . '  group by sender_name order by created desc';
    //die($query);
    $result = mysql_query($query);
    if (!$result) {
        mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed ' );
    }
    $totalConvos=0;
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $sender = $row['sender_name'];
    $created = $row['created'];
    $msg_count = $row['msg_count'];
    $senderId = $row['sender'];
    if($senderId==$user_id)$senderId = $row['receiver'];

    $userEntryHTML = '
    <li class="pmUsersLi" style="display: block;" id="event_8" onclick="openPMConvo('.$senderId.',\''.$sender.'\');">
     <div>
        <div class="pmusericon"><svg viewBox="-3 0 30 25" preserveAspectRatio="xMidYMid meet"><g><path id="p'.$senderId.'" class="pm_path" fill="grey" d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"></path></g></svg></div>
        <div><span style="font-weight: bold;">'.$sender.'</span><br><span> '.$msg_count.' messages</span></div>
        <div class="pmTime">Most recent from '.$created.'</div>
      </div>
    </li>
    ';
    
    print($userEntryHTML);
    $totalConvos++;    
}
$welcomeMsg='';
if($totalConvos >0)$welcomeMsg= "Select existing or"
?>

</div>

<div id="Messages" class="PMConvos">
  <blockquote id="newConvoInvite"><center><?=$welcomeMsg?> <a onclick="startNewConvo();" style="color: red; cursor: pointer"> start a new conversation.</a></center></blockquote>
  <div id="newConvo" style="display: none;">
  <br>
  <center>
    <select id="newConvoUsers" onchange="newConvoUserSelected();">
    <option value="0">Select the user from this list</option>
    <?php
	$query = 'select  ID, username from confa_users order by username';
	$result = mysql_query($query);
        if (!$result) {
        mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed ' );
    }
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$id = $row['ID'];
	$username = $row['username'];
        print('<option value="'.$id.'">'.$username.'</option>');
    }
    ?>
    </select>
    </center>
  </div>
</div>
<div id="PMEntry" class="PMEntry" style="display: none">
    <textarea id="PMEntryText" class="PMEntryText" onfocus="startTyping(this);">Type your message here</textarea>
    <button type="button" class="PMSendBtn" id="PMSendBtn"  onclick="sendMessage();">Send</button>
</div>
</body>
</html>
<?php
require_once('tail_inc.php');
?>

