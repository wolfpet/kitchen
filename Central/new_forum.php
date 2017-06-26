<?php require_once('settings.php'); ?>

<html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, minimum-scale=1, initial-scale=1, user-scalable=no, shrink-to-fit=no">
<link rel="stylesheet" type="text/css" href="../css/disc2.css">
<link rel="stylesheet" type="text/css" href="../css/common.css">
<link rel="stylesheet" type="text/css" href="../css/ribbon.css">
<title>K-Central</title>
<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.js"></script>

<script>
function countdown()
{
      var forumUrl = '<?=$baseName;?>';
//'      
      document.getElementById("content").style.display='none';
      document.getElementById("progress").style.display='block';
      document.getElementById("status").innerHTML="New Forum database has been created successfully.<br>DNS Request for <b>" + document.getElementById("title").value + "." + forumUrl + "</b>  has been sent.<br>It may take up to few minutes for the new DNS record to propogate. Please wait...";
      document.getElementById("countdown").style.display = 'block';
      var DnsStatus=0;
      var dnscount = 60;
      var x = setInterval(function() {
       dnscount--;
       document.getElementById("countdown").innerHTML= "<br>Testing new DNS in " + dnscount + " seconds...";
       if(dnscount<1)
       {
        //stop the timer and try the new dns.
        clearInterval(x);
        var DNSstatus = checkDNS();
        if (DNSstatus==200)
        {
          //sucess. Offer the navigation.
          document.getElementById("progress").style.display = 'none';
          document.getElementById("success").style.display = 'block';
        }
        else
        {
           //not there. Restart the timer.
           countdown();
        }
       }
      }, 1000);

}
function CreateForum()
{
 var forumUrl = '<?=$baseName;?>';
 //
 if(validate())
 {
      document.getElementById("content").style.display='none';
      document.getElementById("progress").style.display='block';
      $.post("new_forum_api.php",
      {
          name: document.getElementById("fullname").value,
          email: document.getElementById("email").value,
          forum: document.getElementById("title").value,
          pwd: document.getElementById("adminpwd").value
      },
      function(data, status)
      {
          if(data != 'success')
          {
            document.getElementById("progress").style.display = 'none';
            document.getElementById("content").style.display = 'block';
            document.getElementById("error").style.display = 'block';
            document.getElementById("error").innerHTML=data;
          }
          else
          {
            //success
            countdown();
          }
      });
 }
}

function checkDNS()
{
    var forumUrl = '<?=$baseName;?>';
    forumUrl = 'http://'+ document.getElementById("title").value.toLowerCase() + '.' +forumUrl +'/origin.php';
    var xmlHttp = new XMLHttpRequest();
    try
    {
    xmlHttp.open( "GET", forumUrl, false ); // false for synchronous request
    xmlHttp.send( null );
    return xmlHttp.status;
    }
    catch(err){return 0;}
}

function validate()
{
    //NAME
    var status = true;
    if(document.getElementById("fullname").value=='')
    {
       document.getElementById("fullnamelabel").innerHTML="<span style='color:red'>ERROR: Please enter your first and last name.</span>";
       return false;
    }
    else
    {
       document.getElementById("fullnamelabel").innerHTML="<span style='color:black'>Full Name</span>";
    }

    //EMAIL
    if(document.getElementById("email").value=='')
    {
       document.getElementById("emaillabel").innerHTML="<span style='color:red'>ERROR: Please enter your valid email address.</span>";
       return false;
    }
    else
    {
       document.getElementById("emaillabel").innerHTML="<span style='color:black'>Email Address</span>";
    }

    //TITLE
    if(document.getElementById("title").value=='')
    {
       document.getElementById("forumlabel").innerHTML="<span style='color:red'>ERROR: Please enter the forum title.</span>";
       return false;
    }
    else if(document.getElementById("title").value.length>8)
    {
       document.getElementById("forumlabel").innerHTML="<span style='color:red'>ERROR: Forum title is too long; must be 8 characters maximum.</span>";
       return false;
    }
    //TODO --- must be letter and numbers only! (DNS)
    else if(hasWhiteSpace(document.getElementById("title").value))
    {
       document.getElementById("forumlabel").innerHTML="<span style='color:red'>ERROR: Forum title cannot have spaces. Only letters and numbers, up to 8 characters.</span>";
       return false;
    }
    else
    {
       document.getElementById("forumlabel").innerHTML="<span style='color:black'>Forum Title (Up to 8 characters)</span>";
    }

    //PWD
    if(document.getElementById("adminpwd").value=='')
    {
       document.getElementById("pwdlabel").innerHTML="<span style='color:red'>ERROR: Password is missing.  You really need to protect this account. Please enter the forum admin password.</span>";
       return false;
    }
    else
    {
       document.getElementById("pwdlabel").innerHTML="<span style='color:black'>Forum Administrator's password</span>";
    }

    return true;
}

function hasWhiteSpace(s) {
  return s.indexOf(' ') >= 0;
}

function OpenForum()
{

 var forumUrl = '<?=$baseName;?>';
 //'
 forumUrl = 'http://'+ document.getElementById("title").value.toLowerCase() + '.' +forumUrl;
 window.location.href = forumUrl;
}
</script>
</head>
<body id="html_body" style="overflow: hidden;">
<?php require_once('menu_inc.php'); ?>


<div id="content" style="padding: 10px; overflow-y: scroll; height: calc(100vh - 74px);">
<h3>Please fill in the following fields</h3>
<div id="error" style="display: none; color: red;"></div>

<span id="fullnamelabel">Full Name</span><br>
<input id="fullname" name="fullname" type="text" maxlength="80" value="" autocomplete="off"><br>
<br>
<span id="emaillabel">Email Address</span><br>
<input id="email" name="email" type="text" maxlength="80" value=""><br>
<br>
<span id="forumlabel">Forum Title (Up to 8 characters. Letters, numbers only.)</span><br>
<input id="title" name="title" type="text" maxlength="80" value="" autocomplete="off"><br>
<br>
<span id="pwdlabel">Forum Administrator's password</span><br>
<input id="adminpwd" name="adminpwd" type="password" maxlength="16" autocomplete="off"><br>
<br>
<input onclick="CreateForum();" tabindex="3" value="Create Forum" type="button" style="width: 180px; height: 45px; cursor: pointer;">
<!-- <input onclick="countdown();" tabindex="3" value="Test Countdown" type="button" style="width: 180px; height: 45px; cursor: pointer;"> -->
</div>
<div id="progress" style="display: none"><br><br><br><center><img src='images/wait.gif' /><br>
<span id="status"></span><br>
<span id="countdown" style="color: red"></span>

</div>

<div id="success" style="display: none; padding: 10px; overflow-y: scroll; height: calc(100vh - 74px);">
<h3>Congratulations!</h3>
Your threaded discussion forum is now ready. As an <span style="color: red">administrator</span> you can login now.
Navigate to your forum using the button below and click Login as shown:
<br><br>
<img src="images/login.png" />
<br>
<br><br>
<input onclick="OpenForum();" value="Open My Forum" type="button" style="width: 180px; height: 45px; cursor: pointer;">
</div>
</body>
</html>

