<?php
/*$Id: moduser.php 803 2012-10-14 19:35:33Z dmitriy $*/

require_once('head_inc.php');
require_once('get_params_inc.php');
require_once('html_head_inc.php');

?>

<base target="bottom">
</head>
<body>


<?php
    if ( !is_null( $moder ) && $moder > 0 ) {
        $query = 'select count(*) as counter, status from confa_posts where author=' . $moduserid . ' group by status';
        $result = mysql_query($query);
        if (!$result) {
            mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed');
        }
        $actives = 0;
        $deleted = 0;
        $censored = 0;

        while($row = mysql_fetch_assoc($result)) {
            if ($row['status'] == 1) {
                $actives = $row['counter'];
            }
            if ($row['status'] == 2) {
                $deleted = $row['counter'];
            }
            if ($row['status'] == 3) {
                $censored = $row['counter'];
            }
        }
        $posts = $actives + $deleted + $censored;
        if ($actives > 0 ) {
            $active_posts = '<a href="' . $root_dir . $page_byuser . '?author_id=' . $moduserid . '" target="contents">Active posts [' . $actives . ']</a> | '; 
        } else {
            $active_posts = 'Active posts [0]</a> | '; 
        }
        if ( $deleted > 0 ) {
            $deleted_posts = '<a href="' . $root_dir . $page_m_delposts . '?author_id=' . $moduserid . '" target="contents">Deleted posts [' . $deleted . ']</a> | ';
        } else {
            $deleted_posts = 'Deleted posts [0]</a> | '; 

        }
        if ($censored > 0 ) {
            $censored_posts = '<a href="' . $root_dir . $page_m_censposts . '?author_id=' . $moduserid . '" target="contents">Censored posts [' . $censored . ']</a> ';
        } else {
            $censored_posts = 'Censored posts [0]</a> '; 
        }

        $query = 'SELECT distinct(IP) from confa_posts where author=' . $moduserid;
        $result = mysql_query($query);
        $ips = '';
        if ($result === false) {
            mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed');
        }
        while ($row = mysql_fetch_row($result)) {
            $ips .= '<a target="contents" href="' . $root_dir . $page_m_users . '?byip=' . $row[0] . '">'. $row[0] . '</a> ';
        }

        $query = 'SELECT u.username,  u.pban, u.moder, CONVERT_TZ(u.created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as created, u.status, u.moder, u.ban, u.email, CONVERT_TZ(u.ban_ends, \'' 
          . $server_tz . '\', \'' . $prop_tz . ':00\') as ban_ends, u2.username as banned_by, h.ban_reason from confa_users u '
          . 'left join confa_ban_history h on h.victim=u.id and h.expires=u.ban_ends left join confa_users u2 on h.moder=u2.id where u.id=' . $moduserid;
        $result = mysql_query($query);
        if (!$result) {
            mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed');
        }

        $row = mysql_fetch_assoc($result);
        $mod_user = $row['moder'];
        if($mod_user!=null)$mod_user = ' - Moderator';
        $username = $row['username'];
        $created = $row['created'];
        $status = $row['status'];
        $ban = $row['ban'];
        $email = $row['email'];
        $ban_ends = $row['ban_ends'];
        $pban = $row['pban'];
        $banned_reason = $row['ban_reason'];
        $banned_by = $row['banned_by'];
        if (is_null($email) || strlen($email) == 0) {
            $email = '-';
        }
        $banned = false;
        if (!is_null($status)) {
            switch ($status) {
                case 1:
                $status = '<b>Active</b>';
                if (!is_null($ban_ends) && strcmp($ban_ends, '0000-00-00 00:00:00')) {
                    $banned = true;
                    $status = '<font color="red"><b>Banned</b></font>'.(is_null($banned_by) ? '' : ' by <b>'.$banned_by.'</b>').' until <u>' . $ban_ends . '</u>';
                    if (!is_null($banned_reason)) {
                      $status .= '. <b>Reason</b>: ' . $banned_reason;
                    }
                }
                break;
                case 2:
                $status = '<b><font color="gray">Disabled</font></b>';
                break;
            }
        }

?>
<h3><?php print( htmlentities($username,HTML_ENTITIES,'UTF-8') ); print($mod_user);?></h3>
<table>
<tr><td>Account created:</td><td><b><?php print($created); ?></b></td></tr>
<tr><td>Status:</td><td><?php print($status); ?> <?php if ($banned && $pban  == 0) { print('<a href="' . $root_dir . $page_ban . '?moduserid=' . $moduserid . '&bantime=-1"><b>Remove ban</b</a>'); }?></td></tr>
<tr><td>Email</td><td><b><?php print($email); ?></b></td></tr>
<tr><td>Total posts</td><td><b><?php print($posts); ?></b></td></tr>
</table>
<P>
Posts | <?php print( $active_posts . $deleted_posts . $censored_posts ); ?>
<P>Ips used for postings: <?php print($ips); ?>

<div style="padding: 8px; background:lightgray">
| <a href="modrole.php?grant=yes&userid=<?=$moduserid?>">Grant moderator role|</a> | <a href="modrole.php?revoke=yes&userid=<?=$moduserid?>">Revoke moderator role</a> |
</div>



<?php
    if ( $row['status'] != 2 ) {
?>
<div style="padding: 8px; background:#fae1e1">
<Form action="<?php print( $root_dir . $page_ban); ?>" target="bottom" method="post">
<input type="hidden" name="moduserid" value="<?php print( $moduserid ); ?>"/>
Ban this user for 
<select name="bantime">
  <option value="1">1 hour</option>
  <option value="2">2 hours</option>
  <option value="4">4 hours</option>
  <option value="8">8 hours</option>
  <option value="24">1 day</option>
  <option value="48">2 days</option>
  <option value="96">4 days</option>
  <option value="168">1 week</option>
  <option value="336">2 weeks</option>
  <option value="504">3 weeks</option>
  <option value="1m">1 month</option>
  <option value="2m">2 months</option>
  <option value="3m">3 months</option>
  <option value="6m">6 months</option>
  <option value="1y">1 year</option>
  <option value="3y">3 years</option>
  <option value="5y">5 years</option>
  <option value="10y">10 years</option>
  <option value="p">*eternity*</option>
</select>
<?php
        if ( !is_null( $err_ban_reason) ) { print("<font color='red'><b>" . $err_ban_reason . "</b></font> ");}else{
            print( 'Reason: ');
        }
?>
<input type="text" id="ban_reason" style="width:100%"  maxlength="127" name="ban_reason" value=""/>
<input type="Submit" value="Ban this user"/>
</form>
</div>
<?php
}
?>

<?php
    } else {
        print( "You have no access to this page." );
    }
?>
</body>
</html>

