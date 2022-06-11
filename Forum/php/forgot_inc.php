<?php
/*$Id: forgot_inc.php 381 2009-11-02 20:25:46Z dmitriy $*/
?>
<H3>Please enter username and email used in your profile</H3>
<P> The new password will be sent to your email
<form action="<?php print( $root_dir . $page_forgot_action ); ?>" method="post">

<table>
<tr>
<td>Username</td>
<td><input id="user" name="user" type="text" maxlength="32" value="<?php print($user); ?>"/></td>
</tr>
<tr>
<td>Email</td>
<td><input id="email" name="email" type="text" maxlength="80" value="<?php print($email); ?>"/></td>
</tr>
<tr>
<td colspan="2"><br/><input type="submit" value="Regenerate password"/></td>
</tr>
</table>
</form>
</body>
</html>

