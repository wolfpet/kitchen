<?php
require_once('dump.php');
require_once('head_inc.php');
require_once('html_head_inc.php');

?>


<html>
<script>
    //parent.document.getElementById('bottom').contentWindow.insertBodyText(''); 
    //parent.closeOverlay();
</script>
<body>
<h3>Please Login</h3>
<div id="loginForm" style="display: block; margin: 10px;">
 <form method="post" target="_top" action="/login.php">
  <input type="hidden" name="lastpage" id="lastpage" value="">
    <div style="color: gray;">Username: </div><input type="text" id="user" name="user" maxlength="64" size="16" value=""> <br>
    <div style="color: grey;">Password: </div><input type="password" id="password" name="password" size="16" maxlength="16" autocomplete="off"> 
    <br><br>
    <input style="width: 100px;hight: 50px;height:  50px;background:  lightgray;" type="Submit" value="Login">
 </form>
</div>
</body>

</html>

