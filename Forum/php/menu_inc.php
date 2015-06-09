<?php
/*$Id: menu_inc.php 875 2013-01-30 17:10:57Z dmitriy $*/
?>
<table width="95%">
<?php
    if (!is_null($err_login) && strlen($err_login) > 0) {
?>
<tr>
<td colspan="2" align="right">
<font color="red"><b><?php print($err_login); ?></b></font>
</td>
</tr>
<?php
    }
?>

<tr>
<td align="left">
<img src="images/ukrainian-flag2.png" alt=""/>
<!--<h3><?php print($title);?></h3>-->
<!--<b>Кто хочет помочь ВСУ, обращайтесь в приват к Picasso.</b>-->
</td>
<?php
    if ($logged_in == false) {
?>

<td align="right">
<form method="post" target="contents" action="<?php print($root_dir . $page_login); ?>">
<input type="hidden" name="lastpage" id="lastpage" value="<?php print( $cur_page );?>"/>
<?php
        if (!is_null($author_id)) {
            print('<input type="hidden" name="author_id" id="author_id" value="' . $author_id . '"/>');
        }
?>
<table><tr>
<td>Username: <input type="text" id="user" name="user" maxlength="64" size="16" value="<?php htmlentities($user, HTML_ENTITIES,'UTF-8');?>"/></td>
<td>Password: <input type="password" id="password" name="password" size="8" maxlength="16"/></td>
<td><input type="Submit" value="Login"/></td>
</tr></table>
</form>
</td>

<?php
    } else {
?>
<td align="right" valign="bottom">
<!--<table><tr><td align="right">
</td></tr><tr><td align="right">-->[ <a href="<?php 
    $url = $root_dir . $cur_page . '?logout=true';
    if (!is_null( $author_id ) ) { 
        $url .= '&author_id=' . $author_id;
    }   
    print($url); ?>" target="contents">Logout</a> | <?php
    if (strcmp($cur_page, $page_profile) == 0) {
?>
Profile |
<?php
    } else {
?>
<a target="bottom" class="menu"  href="<?php print($root_dir . $page_profile); ?>">Profile</a></span>
<?php
    }   
?>
| <font color="gray"><b><?php print($user); ?></b></font> ]<!--</td></tr></table>-->
</td>

<?php
    }
?>
</tr>

<tr><td>[

<?php
    if (strcmp($cur_page, $page_bydate) == 0) {
?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_bydate); ?>?mode=bydate"><I>By date</I></a> |
<?php
    } else {
?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_bydate); ?>?mode=bydate">By date</a> |
<?php
    }
    if (strcmp($cur_page, $page_expanded) == 0) {
?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_expanded); ?>"><I>Expanded threads</I></a> |
<?php
    } else {
?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_expanded); ?>">Expanded threads</a> |

<?php
    }
    if (strcmp($cur_page, $page_collapsed) == 0) {
?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_collapsed); ?>"><I>Collapsed threads</I></a> |
<?php
    } else {
?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_collapsed); ?>">Collapsed threads</a> |


<?php
    }
  if ($logged_in == false) {
    if (strcmp($cur_page, $page_reg) == 0) {
?>
Register |
<?php
    } else {
?>
<a target="bottom" class="menu" href="<?php print($root_dir . $page_new_user); ?>">Register</a> |


<?php
    }
    if (strcmp($cur_page, $page_forgot) == 0) {
?>
Forgot password? |
<?php
    } else {
?>
<a target="bottom" class="menu" href="<?php print($root_dir . $page_forgot); ?>">Forgot password?</a> |
<?php
    }
  }
?>

<a target="_blank" class="menu" href="https://github.com/wolfpet/kitchen">GitHub</a> |

<a target="contents" class="menu" href="<?php print($root_dir . $cur_page); if (!strcmp($cur_page, $page_byuser)) {print('?author_id=' . $author_id); }else {if (/*!strcmp($cur_page, $page_expanded) && */!is_null($page)){ print('?page=' . $page); } } ?>">Refresh</a> 

]
</td><td align="right">
[ <?php if (strcmp($cur_page, $page_search) == 0) {?>Search |<?php } else { ?><a href="<?php print($root_dir . $page_search); ?>" class="menu"  target="bottom">Search</A> |<?php } ?>  <a href="<?php print($root_dir . $page_new); ?>" class="menu" target="bottom">New message</a>
 ]
</td></tr>
<?php
    if ($logged_in) {
?>
<tr><td align="left">
 <SPAN STYLE="background-color: #E0E0E0">
[ Pmail<?php if (!is_null($new_pm) && $new_pm > 0){ print('(<font color="red"><b>' . $new_pm . '</b></font>)');}?> | 
<a target="contents" class="menu" href="<?php print($root_dir . $page_pmail); ?>">In</a> |
<a target="contents" class="menu" href="<?php print($root_dir . $page_pmail_sent); ?>">Sent</a> |
 
<?php
    if (strcmp($cur_page, $page_pmail_send) == 0) {
?>
New ]<?php
    } else {
?>
<a target="bottom" class="menu" href="<?php print($root_dir . $page_pmail_send); ?>">New</a> ]<?php
    }
?></span>
&nbsp;
<SPAN STYLE="background-color: #E0E0E0">
[ 

<?php
    if (strcmp($cur_page, $page_my_messages) == 0) {
?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_my_messages); ?>"><I>My messages</I></a> |
<?php
    } else {
?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_my_messages); ?>">My messages</a> |
<?php
    }
?>

<?php
    if (strcmp($cur_page, $page_answered) == 0) {
?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_answered); ?>"><I>Answered</I></a> |
<?php
    } else {
?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_answered); ?>">Answered</a> |
<?php
    }
?>


<?php
    if (strcmp($cur_page, $page_my_bookmarks) == 0) {
?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_my_bookmarks); ?>"><I>Bookmarks</I></a> ]
<?php
    } else {
?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_my_bookmarks); ?>">Bookmarks</a> ]
<?php
    }
?>

</span>

<?php
    if (!is_null($moder) && $moder > 0) {
?>
&nbsp;
<SPAN STYLE="background-color: #FFE0E0">
[
<?php
        if (strcmp($cur_page, $page_m_users) == 0) {
?>
Users |
<?php
        } else {
?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_m_users); ?>"><font color="green">Users</font></a> |
<?php
        }
?>


<?php
        if (strcmp($cur_page, $page_m_ips) == 0) {
?>
IPs |
<?php
        } else {
?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_m_ips); ?>"><font color="green">IPs</font></a> |
<?php
        }
?>

<?php
        if (strcmp($cur_page, $page_m_delposts) == 0) {
?>
Deleted posts |
<?php
        } else {
?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_m_delposts); ?>"><font color="green">Deleted posts</font></a> |
<?php
        }
?>

<?php
        if (strcmp($cur_page, $page_m_censposts) == 0) {
?>
Censored posts ]
<?php
        } else {
?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_m_censposts); ?>"><font color="green">Censored posts</font></a> ]
<?php
        }
?>


</span>
  

<?php
    }
?>

</td>
<td align="right">
<!--<SPAN STYLE="background-color: #E0E0E0">
[ <a href="<?php print($root_dir . $cur_page . '?logout=true'); ?>" target="contents">Logout</a> ]
</span>
-->
</td>
</tr>

<?php
}
?>

</table>

