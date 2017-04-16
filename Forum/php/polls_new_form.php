<?php
require_once('head_inc.php');
require_once('html_head_inc.php');
if (!$logged_in)die('Error 403: Not authorized. Login is required to create polls');
?>
<html>
<head>
<style>
.pollText {
    width: 90vw;
    padding: 3px;
    border-color: black;
    border-width: 1px;
    border-style: solid;
    margin-bottom: 5px;
    margin-top: 5px;
}
</style>
<script>
var numberOfAnswers=2;

function addAnswer()
{
    //save all values
    var fieldValues  = [];
    var x = document.getElementsByClassName("pollText");
    var i;
    for (i = 0; i < x.length; i++) 
    {
        fieldValues.push(x[i].value);
    }
    numberOfAnswers++;
    var answerHTML = 'Answer '+numberOfAnswers+':<br><input type="text" class="pollText" id="pollAnswer'+numberOfAnswers+'" name="pollAnswer'+numberOfAnswers+'"><br>';
    document.getElementById('answers').innerHTML +=answerHTML;
    //restore all values
    for (i = 0; i < x.length-1; i++)
    {
        x[i].value = fieldValues[i];
    }
    document.getElementById('numberOfAnswers').value = numberOfAnswers;
}

function savePoll()
{
 //validate
 var x = document.getElementsByClassName("pollText");
     for (i = 0; i < x.length; i++)
     {
         if(x[i].value==''){alert("Empty fields are not allowed"); return;}
     }
 //submit
 document.getElementById("pollForm").submit();
}
</script>
</head>

<body>
 <form id="pollForm" name="pollForm" action="polls_new.php" method="post" autocomplete="off">
 Question:<br>
 <input type="text" class="pollText" name="pollQuestion"><br><br>
 <div id="answers">
   Answer 1:<br>
   <input type="text" class="pollText" id="pollAnswer1" name="pollAnswer1"><br>
   Answer 2:<br>
   <input type="text" class="pollText" id="pollAnswer1" name="pollAnswer2"><br>

 </div>
 <br>
 <a onclick="addAnswer();" style="cursor: pointer;     color: blue;">Add an answer</a>
 <br>
 <br>
 <input type="hidden" id="numberOfAnswers" name="numberOfAnswers" value="2">
 <button onclick="savePoll();" style="width: 180px; height: 45px; cursor: pointer;" type="button">Save</button>
 </form>
 <br>
 <br>
 <br>
 <br>
 <br>
 <br>


</body>
</html>