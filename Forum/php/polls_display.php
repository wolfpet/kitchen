<?php
require_once('dump.php');
require_once('head_inc.php');
require_once('html_head_inc.php');
//if($user_id==null)die('unauthorized');
?>
<html>
<head>
<style>
.pollDiv {
    border-color: #dddfe2;
    border-radius: 3px;
    border-style: solid;
    border-width: 1px;
    overflow: hidden;
    position: relative;
}

.pollAnswer {
    position: relative; 
    width: calc(100% - 26px);
    border-color: #dddfe2;
    border-radius: 3px;
    border-style: solid;
    border-width: 1px;
    margin-top: 4px;
    min-height: 40px;
}
.pollCheckboxDiv {
    height: 100%; 
    position: absolute;
    width: 26px;
    background-color: #fafafa; 
    border-style: none
}
.pollCheckbox {
    top: 5px;
    position: relative;
    left: 2px;
}

.pollResults {
    width: 34px; 
    top: 10px; position: relative; 
    right: 0px;
    background-color: #e9ebee; 
    float: right;
    height: 28px; 
    line-height: 28px; 
    padding-left: 4px;
    z-index: 1;
    cursor: pointer;
}
.partialBackground {
    left: 26px; height: 100%; display: block; background-color: #ecf0f7; position: absolute;
}
.pollAnswerText {
 left:30px; display: block;    color: #4b4f56;  line-height: 18px;  padding: 4px 6px; position: relative;     width: calc(100% - 60px);
}
.whoVoted {
    display: block;
    cursor: pointer;
    position: absolute;
    background-color: lightgreen;
    padding: 5px;
    float: right;
    z-index: 2;
    right: 2px;
    top: 10px;
    overflow-y: scroll;
    max-height: 80%;
    max-width: 80%;
    overflow-x: hidden;
    width: 50%;
    height: 50%;
}
.anon {
    // background-color: #e9ebee;
    background-color: #404040;
    color: white;
    border-radius: 0.4em;    
    padding-left: 5px;
    padding-right: 5px;
}
</style>
</head>
<script>
function vote(checkbox, question, answer)
{
 var me = <?php if($user_id==null){print('null');}else print($user_id);?>;
 if(me==null){alert('Please login to vote'); return;}

 if(checkbox.checked)
 {
    //vote
    $.ajax({
           type: "GET",
           url: 'polls_api.php',
           data: {questionId: question, answerId: answer, action: 'vote'} ,
           success: function(data) {
           }
    });
 }
 else
 {
    //delete the vote
    $.ajax({
           type: "GET",
           url: 'polls_api.php',
           data: {questionId: question, answerId: answer, action: 'unvote'} ,
           success: function(data) {
           }
    });
 }
 var url = window.location.href;
 if (url.indexOf('?') > -1){
    url += '&rnd=' + Math.random();
    }else{
    url += '?param=' + Math.random();
    }
    setTimeout(function() { window.location.href = url; }, 1000);
}

function whoVoted(answer)
{
   $.ajax({
              type: "GET",
              url: 'polls_api.php',
              data: { answerId: answer, action: 'whoVoted'} ,
              success: function(events) {
                
                document.getElementById('whoVoted').innerHTML='<b>Voted for this option</b>:<p>';
                
                var msgArray = $.map(events, function(value, index) {
                  return [value];
                });
                
                for(var i = 0; i < msgArray.length; i++) {
                  document.getElementById('whoVoted').innerHTML += (i > 0 ? ', ' : '') + msgArray[i];
                }
                
                document.getElementById('whoVoted').style.display='block';
              }
          });
}
</script>
<body>
<div class="whoVoted" style="display: none; cursor: pointer" id="whoVoted" onclick="this.style.display='none';">Voted for this option: <hr></div>
<div class="pollContainer">
 <div class="pollAnswerContainer">
<?php 
$pollId = intval($_GET["poll"]);
//question
$query ='select content, anon from confa_polls where type=0 and id=' . $pollId;
$result = mysql_query($query);
if (!$result) { die('Query failed'); }
$row = mysql_fetch_array($result, MYSQL_ASSOC);
$question = $row['content'];
$anon = $row['anon'];
if ($anon) {
  echo '<span class="anon">anonymous</span>&nbsp;';
}
echo $question .'<br>';
//total votes
$query =' select count(id) as total_votes from confa_polls where question_id='. $pollId. ' and type=2';
$result = mysql_query($query);
$row = mysql_fetch_array($result, MYSQL_ASSOC);
$total_votes = $row['total_votes'];
//answer options
$query ='select id, content from confa_polls where question_id=' . $pollId. ' and type=1';
$result = mysql_query($query);
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
{

    $answerOptionId  = $row['id'];
    $answerOptionContent = $row['content'];

    //fetch all votes for the current answer option
    $sub_query = 'select count(id) as votes from confa_polls where answer_id ='. $answerOptionId; 
    $sub_result = mysql_query($sub_query);
    $sub_row = mysql_fetch_array($sub_result, MYSQL_ASSOC);
    $votes = $sub_row['votes'];
    $percentagePoints = $total_votes > 0 ? $votes * 100 / $total_votes : 0;
    //check if I voted
    $sub_query = 'select id from confa_polls where owner_id='.$user_id.' and question_id='.$pollId.' and answer_id='.$answerOptionId;
    $sub_result = mysql_query($sub_query);
    $checked=false;
    if(mysql_num_rows($sub_result)>0)$checked=true;//voted already

?>
  <div class="pollDiv pollResults" 
  <?php if ($anon == 0) { ?>
  onclick="whoVoted(<?=$answerOptionId?>);"
  <?php } ?>
  >+<?=$votes?></div>
  <div class="pollAnswer">
    <div class="pollDiv pollCheckboxDiv"><input type="checkbox" class="pollCheckbox" value="on" onclick="vote(this, <?=$pollId?>, <?=$answerOptionId?>);" <?php if($checked)print('checked');?>></div>
    <div class="partialBackground" style="width: <?=$percentagePoints?>%;"></div>
    <div class="pollAnswerText"><?=$answerOptionContent?></div>
  </div>
<?php
}
?>
 </div>
</div>
</body>
</html>

