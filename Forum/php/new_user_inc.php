<?php
/*$Id: new_user_inc.php 381 2009-11-02 20:25:46Z dmitriy $*/
if ( isset($reg_type) && 
   ( $reg_type == REG_TYPE_OPEN ||
     $reg_type == REG_TYPE_EMAIL ||
     $reg_type == REG_TYPE_CONFIRM)) { 
?>
<br>
<form action="<?php print( $root_dir . $page_reg ); ?>" method="post">

<table>
<tr>
<td>Username <font color="grey">(32 characters maximum)</font></td>
<td><input id="user" name="user" type="text" maxlength="32" value="<?php print($user); ?>"/></td>
</tr>
<tr>
<td>Password <font color="grey">(16 characters maximum, 4 minimum)</font></td>
<td><input id="password" name="password" type="password" maxlength="16"/></td>
</tr>
<tr>
<td>Retype password: </td>
<td><input id="password2" name="password2" type="password" maxlength="16"/></td>
</tr>
<tr>
<td>Email</td>
<td><input id="email" name="email" type="text" maxlength="80" value="<?php print($email); ?>"/></td>
</tr>
<tr>
<td>Retype email: </td>
<td><input id="email2" name="email2" type="text" maxlength="80" value="<?php print($email2); ?>"/></td>
</tr>
<?php
if (isset($recaptcha_site_key) && isset($recaptcha_secret_key)) {
?>
<tr>
<td colspan="2"><div class="g-recaptcha" data-sitekey="<?=$recaptcha_site_key?>"></div></td>
</tr>
<?php 
}
?>
<tr>
<td colspan="2"> <input type="submit" value="Register"/></td>
</tr>
</table>
</form>
</body>
</html>
<?php
} else if ( isset($reg_type) && $reg_type == REG_TYPE_CLOSED){
  if ( isset($closed_reg_message) && $closed_reg_message != "")
    print $closed_reg_message;
  else{
    // Closed registration message is not defined, print the default one
?>
This forum isn't open to the public.
<br>To register, simply send email with request and desired username to <B>XXX@VVV.COM</B>
you will get response with the password shortly.
Thank you.
<?php
  }
}
?>
