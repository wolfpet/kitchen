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

        $query = 'SELECT username,  pban, CONVERT_TZ(created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as created, status, moder, ban, email, CONVERT_TZ(ban_ends, \'-5:00\', \'' . $prop_tz . ':00\') as ban_ends from confa_users where id=' . $moduserid;
        $result = mysql_query($query);
        if (!$result) {
            mysql_log( __FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed');
        }

        $row = mysql_fetch_assoc($result);
        $username = $row['username'];
        $created = $row['created'];
        $status = $row['status'];
        $ban = $row['ban'];
        $email = $row['email'];
        $ban_ends = $row['ban_ends'];
        if (is_null($email) || strlen($email) == 0) {
            $email = '-';
        }
        $banned = false;
        if (!is_null($status)) {
            switch ($status) {
                case 1:
                $status = 'Active';
                if (!is_null($ban_ends) && strcmp($ban_ends, '0000-00-00 00:00:00')) {
                    $banned = true;
                    $status = 'Banned till ' . $ban_ends;
                }
                break;
                case 2:
                $status = 'Disabled';
                break;
            }
        }

?>
<h3><?php print( '<del>' . htmlentities($username,HTML_ENTITIES,'UTF-8') . '</del>' ); ?></h3>
<table>
<tr><td>Account created:</td><td><b><?php print($created); ?></b></td></tr>
<tr><td>Status:</td><td><b><?php print($status); ?></b> <?php if ($banned && $pban  == 0) { print('<a href="' . $root_dir . $page_ban . '?moduserid=' . $moduserid . '&bantime=-1">Remove ban</a>'); }?></td></tr>
<tr><td>Email</td><td><b><?php print($email); ?></b></td></tr>
<tr><td>Total posts</td><td><b><?php print($posts); ?></b></td></tr>
</table>
<P>
Posts | <?php print( $active_posts . $deleted_posts . $censored_posts ); ?>
<P>Ips used for postings: <?php print($ips); ?>
<?php
    if ( $row['status'] != 2 ) {
?>
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
</select>
<?php
        if ( !is_null( $err_ban_reason) ) { print("<font color='red'><b>" . $err_ban_reason . "</b></font> ");}else{
            print( 'Reason: ');
        }
?>
<input type="text" id="ban_reason" size="80" maxlength="127" name="ban_reason" value="<?print( $ban_reason );?>"/>
<input type="Submit" value="Ban this user"/>
</form>
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

