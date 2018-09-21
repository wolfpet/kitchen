<?php
/*$Id: menu_inc.php 875 2013-01-30 17:10:57Z dmitriy $*/
?>
<script type="text/javascript" src="<?=autoversion('js/threads_autoload.js')?>"></script>
<table width="100%">
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
<?php if (isset($banner) && !is_null($banner)) { ?>
<img src="<?=$banner?>" alt="<?=$title?>"/>
<?php } 
if (isset($title) && $title != null) { ?>
<h3 style="padding:0px;margin:0px;" class="emboss"><?php print($title);?></h3>
<?php 
}
?>
<!--<b>Public announcements go here</b>-->
</td>
<?php
    if ($logged_in == false) {
?>
<td align="right" nowrap>
<form method="post" target="contents" action="<?php print($root_dir . $page_login); ?>">
<input type="hidden" name="lastpage" id="lastpage" value="<?php print( $cur_page );?>"/>
<?php
        if (!is_null($author_id)) {
            print('<input type="hidden" name="author_id" id="author_id" value="' . $author_id . '"/>');
        }
?>
Username: <input type="text" id="user" name="user" maxlength="64" size="16" value="<?php htmlentities($user, HTML_ENTITIES,'UTF-8');?>"/> Password: <input type="password" id="password" name="password" size="8" maxlength="16" autocomplete="off"/> <input type="Submit" value="Login"/>
</form></td>
<?php
    } else {
?>
<td align="right" valign="bottom">
<!--<table><tr><td align="right">
</td></tr><tr><td align="right">-->
<?=isset($safe_mode) && $safe_mode != 0 ? "<img src='images/small_green_dot.png' valign='center' style='margin-right:5px; background:transparent;' title='Safe Mode'/>" : ""?>
[ <a href="<?php 
    $url = $root_dir . $cur_page . '?logout=true';
    if (!is_null( $author_id ) ) { 
        $url .= '&author_id=' . $author_id;
    }   
    print($url); ?>" target="contents">Logout</a> | <?php
    if (strcmp($cur_page, $page_profile) == 0) {
?>
<font color="gray"><b><?php print($user); ?></b></font> |
<?php
    } else {
?>
<a target="bottom" class="menu"  href="<?php print($root_dir . $page_profile); ?>"><b><?php print($user); ?></b></a></span>
<?php
    }   
?>
]
</td>

<?php
    }
?>
</tr>

<tr><td>[

<?php
    if (strcmp($cur_page, $page_bydate) == 0) {
?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_bydate); ?>?mode=bydate"><I id="bydate">By date</I></a> |
<?php
    } else {
?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_bydate); ?>?mode=bydate" id="bydate">By date</a> |
<?php
    }
    if (strcmp($cur_page, $page_expanded) == 0) {
?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_expanded); ?>"><I>Expanded</I></a> |
<?php
    } else {
?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_expanded); ?>">Expanded</a> |

<?php
    }
    if (strcmp($cur_page, $page_collapsed) == 0) {
?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_collapsed); ?>"><I>Collapsed</I></a> |
<?php } else { ?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_collapsed); ?>">Collapsed</a> |
<?php } ?>

<?php if (isset($orientation) && $orientation == "cols") { ?>
<a target="_top" class="menu" href=".">Rows</a> |
<?php } else { ?>
<a class="menu" onclick="setVerticaLayout();">Columns</a> |
<?php } ?>

<a target="_top" class="menu" href="m.php" title="Flat">Flat</a> |
<a target="_top" class="menu" href="mobile/index.html">Phone</a> |

<?php
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

<a target="_blank" class="menu" href="https://github.com/wolfpet/kitchen">Code</a> <!--|

<a target="contents" class="menu" href="<?php print($root_dir . $cur_page); if (!strcmp($cur_page, $page_byuser)) {print('?author_id=' . $author_id); }else {if (/*!strcmp($cur_page, $page_expanded) && */!is_null($page)){ print('?page=' . $page); } } ?>">Refresh</a> 
-->
]&nbsp;&nbsp&nbsp;[ <?php if (strcmp($cur_page, $page_search) == 0) {?>Search |<?php } else { ?><a href="<?php print($root_dir . $page_search); 
  if (strcmp($cur_page, $page_my_bookmarks) == 0) print("?mode=bookmarks");
?>" class="menu"  target="bottom">Search</A> |<?php } ?>  <a href="<?php print($root_dir . $page_new); ?>" class="menu" target="bottom">New thread</a> |
<a target="contents" class="menu" href="<?php print($root_dir . $cur_page); if (!strcmp($cur_page, $page_byuser)) {print('?author_id=' . $author_id); }else {if (/*!strcmp($cur_page, $page_expanded) && */!is_null($page)){ print('?page=' . $page); } } ?>">Refresh</a>  ]
</td><td align="right" nowrap><!--
[ <?php if (strcmp($cur_page, $page_search) == 0) {?>Search |<?php } else { ?><a href="<?php print($root_dir . $page_search); 
  if (strcmp($cur_page, $page_my_bookmarks) == 0) print("?mode=bookmarks");
?>" class="menu"  target="bottom">Search</A> |<?php } ?>  <a href="<?php print($root_dir . $page_new); ?>" class="menu" target="bottom">New thread</a>
 ]
 -->
</td></tr>
<?php
    if ($logged_in) {
?>
<tr><td align="left">
 <SPAN STYLE="background-color: #E0E0E0">
[ Pmail<?php if (!is_null($new_pm) && $new_pm > 0){ print('(<font color="red"><b>' . $new_pm . '</b></font>)');}?> | 

<?php
if (strcmp($cur_page, $page_pmail) == 0) {?>
  <a target="contents" class="menu" href="<?php print($root_dir . $page_pmail); ?>"><i>Inbox</i></a> |
<?php } else { ?>
  <a target="contents" class="menu" href="<?php print($root_dir . $page_pmail); ?>">Inbox</a> |
<?php } ?>

<?php
if (strcmp($cur_page, $page_pmail_sent) == 0) {?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_pmail_sent); ?>"><i>Sent</i></a> |
<?php } else { ?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_pmail_sent); ?>">Sent</a> |
<?php } ?>
 
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
<a target="contents" class="menu" href="<?php print($root_dir . $page_answered); ?>"><I id="answered">Answered</I></a> |
<?php
    } else {
?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_answered); ?>" id="answered">Answered</a> |
<?php
    }
?>


<?php
    if (strcmp($cur_page, $page_my_bookmarks) == 0) {
?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_my_bookmarks); ?>"><I>Bookmarks</I></a> ]</span>
<?php
} else {
?><a target="contents" class="menu" href="<?php print($root_dir . $page_my_bookmarks); ?>">Bookmarks</a> ]</span>
<?php
}
    if (!is_null($moder) && $moder > 0) {
      $regs = get_regs_count();
?>&nbsp;&nbsp;<SPAN STYLE="background-color: #FFE0E0">[
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
    if ($regs > 0)
        if (strcmp($cur_page, $page_registrations) == 0) {
?>
Registrations<?=$regs > 0 ? '(<font color="red"><b>'.$regs."</b></font>)":""?> |
<?php
        } else {
?>
<a target="contents" class="menu" href="<?php print($root_dir . $page_registrations); ?>"><font color="green">Registrations<?=$regs > 0 ? '(<b><font color="red">'.$regs."</font></b>)":""?></font></a> |
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
<td align="right"><!-- Move pages here -->
<?php
if (function_exists('pages_function')) {
  pages_function();
}
?>
</td>
</tr>

<?php
}
?>
</table>
