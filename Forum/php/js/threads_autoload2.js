//---legacy vars ----------
var bydate_timer = -1;
var bydate_count = 0;

//-------------------------
var pmId=0;
var byDateCounter = 0;
var answeredCounter = 0;
var pmCounter = 0;
var regCounter = 0;
var total_count = 0;
var min_bydate_id =0; //we'll query "answered" api so this will be a limit.
var check_time = 0; //time the script checked the DB
var render_time = 0; //time the user opened the notification center

$( document ).ready(function() 
{
    //check if logged in. exit if not.
    if(document.getElementById('newNotificationsBadge') == null) return;
    
    //call byDate API periodically
    window.setTimeout(byDateCaller, 1000);
    bydate_timer = window.setInterval(byDateCaller, 60000);
    
    pmCounter = Number(document.getElementById('newPMBadge').innerHTML);
    window.setInterval(pmCaller, 60000);
    
    // only update registrations counter if moderator
    var modBadge = document.getElementById('newModBadge');
    if(modBadge != null) {
      regCounter = Number(modBadge.innerHTML);
      window.setInterval(regsCaller, 5 * 6000);
    }
});

function byDateCaller()
{
      total_count=0;
      $.ajax({
              type: "GET",
              url: "./api/messages?mode=bydate",
              success: function(data)
              {
                 byDate(data);
              }
      });
}

function answeredCaller()
{
      $.ajax({
              type: "GET",
              url: "./api/messages?mode=answeredmin&min_bydate_id=" + min_bydate_id,
              success: function(data)
              {
                 answered(data);
              }
      });
}

function pmCaller()
{
      total_count=0;
      $.ajax({
              type: "GET",
              url: "./api/inboxlastid",
              success: function(data)
              {
                 pm(data);
              }
      });
}

function regsCaller()
{
      $.ajax({
              type: "GET",
              url: "./api/registrations",
              success: function(data)
              {
                 regs(data);
              }
      });
}

function byDate(data)
{
    byDateCounter=0;
    answeredCounter=0;
    min_bydate_id=0;
    var user_id = data.user_id;
    for(i=0; i<data.count; i++)
    {
     if(data.messages[i].author.id != user_id)
     {
        //not my message. increment the counter
        byDateCounter++;
        //update the  min_bydate_id to pass to answered min API
        min_bydate_id =data.messages[i].id;
     }
    }
    if(byDateCounter>0)
    {
      //looks like there are new messages. check if there are answers to my posts
      answeredCaller();
    }
    updateBadges();
    //render the new titles inline 
    if(data.count>0)updateThreads(data);
}

function answered(data)
{
    var user_id = data.user_id;
    for(var i=0; i<data.count; i++)
    {
     if(data.messages[i].author.id != user_id)
     {
        //not my message. increment the counter
        answeredCounter++;
     }
    }
    updateBadges();
}
function pm(data)
{
    pmCounter = 0;
    if(document.getElementById('newPMBadge').style.display != 'none')
    {
       //there were PMs already. increment.
       pmCounter = Number(document.getElementById('newPMBadge').innerHTML);
    }
    if(pmId == 0)
    {
      //first run. init 
      pmId = data.id;
      return;
    }
    var newPMId = data.id;
    if(pmId < newPMId)
    {
      pmCounter++;
      updateBadges();
      pmId = newPMId;
    }
}

function regs(data)
{
    var oldCounter = regCounter;    
    regCounter = Number(data);
    
    if (oldCounter != regCounter) {
      updateBadges();
    }
}

function updateBadges()
{
    //this function is called when there are new notifications
    total_count=0;
    var newPostsBadge = document.getElementById('newPostsBadge');
    var newAnswersBadge = document.getElementById('newAnswersBadge');
    var totalCountBadge = document.getElementById('newNotificationsBadge');
    if(byDateCounter >0)
    {
       //there are new messages. reflect in the notification center
       if(newPostsBadge!=null)
       {
        //new UI. Update the bydate badge.
        newPostsBadge.innerHTML = byDateCounter;
        newPostsBadge.style.display = 'block';
        document.getElementById('newPostsBadge2').innerHTML = byDateCounter;
        //update total notification counter
        total_count++;
       }
    }
    else
    {
        //no new messages. Hide badges.
        newPostsBadge.style.display = 'none';
        document.getElementById('newPostsBadge2').innerHTML= 'no';
    }
    //answered badge
    if(answeredCounter >0)
    {
       if(newAnswersBadge!=null)
       {
        //new UI. Update the answered badge.
        newAnswersBadge.innerHTML =answeredCounter;
        newAnswersBadge.style.display = 'block';
        document.getElementById('newAnswersBadge2').innerHTML = answeredCounter;
        total_count++;
       }
    }
    else
    {
        newAnswersBadge.style.display= 'none';
        document.getElementById('newAnswersBadge2').innerHTML ='no';
    }
    //pm badge
    if (pmCounter > 0)
    {
      //update the PM badge.
      $('#newPMBadge').css('display', 'block');
      $('#newPMBadge').html(pmCounter);
      // document.getElementById('pmNotificationMessage').innerHTML =  'At least ' + pmCounter + ' new PMs waiting in your inbox!';     
      $('#newPMBadge2').html(pmCounter);
      total_count++;
    } else {
      $('#newPMBadge2').html('no');
    }
    //regs badge
    if (regCounter > 0) {      
      $('#newRegs').toggle(true);
      $('#newRegBadge').css('display', 'block');
      $('#newRegBadge').html(regCounter);
      $('#newModBadge').css('display', 'block');
      $('#newModBadge').html(regCounter);
      $('#newUserBadge').css('display', 'block');
      $('#newUserBadge').html(regCounter);
      $('#newRegBadge2').html(regCounter);
      total_count++;
    } else {
      $('#newRegs').toggle(false);
      $('#newRegBadge').css('display', 'none');
      $('#newModBadge').css('display', 'none');
      $('#newUserBadge').css('display', 'none');
      $('#newRegBadge2').html('no');
    }
    
    //update timestamps
    if(document.getElementById('newPostsTime')!==null)
    {
          var d = new Date();
          check_time = d.getTime(); //the notification center will use this time when rendering. Check menu_0.php  openNotifications() for details
          //reset the timers if the div is visible _next_ time this runs.
          var timeDiff = check_time - render_time;
          var diffSeconds =  ((timeDiff % 60000) / 1000).toFixed(0);
          if (diffSeconds > 5)
          {
             $('#newPostsTime').html('Just now');
             $('#newAnswersTime').html('Just now');
             $('#newPMTime').html('Just now');
             $('#newRegTime').html('Just now');
          }
    }
    //update total notifications badge, page title
    //if(document.getElementById('newPMBadge').style.display == 'block')total_count++; //no new PM this time round but did'n check PM yet.
    if(total_count > 0)
    {
     totalCountBadge.style.display = 'block';
     totalCountBadge.innerHTML = total_count;
    }
    else
    {
      totalCountBadge.style.display = 'none';
    }
    // update title
    window.parent.document.title = 
      addCounter(window.parent.document.title, total_count, false, true);
}

function addCounter(text, count, bold, pad) 
{
    var prefix = pad ? " (" : "(";
    var braket = text.indexOf(prefix);
    if (braket >= 0) text = text.substring(0, braket);
    if (count > 0) text += prefix + (bold?"<b>":"") + count + (bold?"</b>":"") +")";
    return text;
}

function divMode() {
   return document.getElementById("_contents");
}

function updateThreads(data)
{
  console.log("updateThreads(" + data.count + ")");
  
  for(i=data.count-1; i>=0; i--)
    {
      var id = data.messages[i].id;
      //check if such title has already been rendered
      if (divMode()) {
        if(document.getElementById(id)!=null) {
          console.log("thread " + id + " already rendered");
          continue;
        }
      } else {
        if(contents.document.getElementById(id)!=null) {
          console.log("thread " + id + " already rendered");
          continue;
        }
      }
      //doesn't exist. Render!
      var parentId = data.messages[i].parent;
      var thread = divMode() ? document.getElementById('sp_' + parentId) : contents.document.getElementById('sp_' + parentId);
      if(thread == null) { 
        console.log("parent thread " + parentId + " not part of the document");
        continue; //parent is outside the loaded threads, Skip.
      }
      var authorName = data.messages[i].author.name;
      var authorId= data.messages[i].author.id;
      var subj = data.messages[i].subject;
      var views = data.messages[i].views;
      var created = data.messages[i].created;
      var dl = document.createElement('dl');
      var newHtml =    '<span id="sp_'+id+'"><img border="0" src="images/dn.gif" width="16" height="16" alt="*" align="top" style="padding:0px 0px 3px 0px;"> <a id="'+id+'" name="'+id+'" target="bottom" onclick="selectMsg(\''+id+'\');" href="/msg.php?id='+id+'">'+subj+'</a> <b><a class="user_link" href="/byuser.php?author_id='+authorId+'" target="contents">'+authorName+'</a></b> ['+views+' views] '+created+'</span>';
      dl.innerHTML='<dd>' + newHtml + '</dd>';
      if(parentId==0)
      {
        //new thread
        var threadsDiv = divMode() ? document.getElementById('threads') : contents.document.getElementById('threads');
        threadsDiv.insertBefore(dl, threadsDiv.firstChild);
      }
      else
      {
         // find next br & attach after it
         var br = thread.nextSibling;
         if (br) {
             if (br.tagName == "BR") {
               thread = br;
             }
         }
         thread.outerHTML  += '<dl><dd>' + newHtml + '</dl></dd>';
         
      }
    }
}

// the code below is to support shift+click selection of checkboxes used e.g. in old PM UI
var focused = null;
// shift - select
$(document).ready(function(){
 // add click function to checkboxes
 $(document).find(':checkbox').each(function() {
    $(this).click(function(e) {
      if (e.shiftKey) {
        if (focused != null) {
          var checked = this.checked;
          var current = this;
          // make all checkboxes between 'focused' and 'current' same as 'current'
          var inside = false;
          $(document).find(':checkbox').each(function() {
            if (this.value == current.value || this.value == focused.value) {
              this.checked = checked;
              inside = !inside;
              if (!inside) return;
            } else if (inside) {
              this.checked = checked;
            }
          });
        }
      } else {
        focused = this;
      }
    });
  });
});
