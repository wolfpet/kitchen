<?php

require_once('head_inc.php');

function boldmoder($text, $rec, $list) {
  if ($rec['moder']) 
    if (isset($list) && $list)
      return $text . '<span style="color:green;">*</span>';
    else 
      return '<b>' . $text . '</b>';
  
  return $text;
}

if ($logged_in) {
	//WHO IS ONLINE? 
	$query ="SELECT user_id, updated, username, moder FROM confa_sessions, confa_users WHERE confa_sessions.user_id=confa_users.ID AND updated >= NOW() - INTERVAL 60 MINUTE Group by username;";
	//die($query);
        $users_online = array();
        $result = mysql_query($query);
        if (!$result) {
            mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed ' );
        }
        while ($row = mysql_fetch_assoc($result)) {
          $users_online[] = boldmoder($row['username'], $row, true);
        }

	//Visited today 
	$query ="SELECT user_id, updated, username, moder FROM confa_sessions, confa_users WHERE confa_sessions.user_id=confa_users.ID AND updated >= NOW() - INTERVAL 1440 MINUTE Group by username;";
	//die($query);
        $users_today = array();
        $result = mysql_query($query);        
        if (!$result) {
            mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed ' );
        }
        while ($row = mysql_fetch_assoc($result)) {
            $users_today[] = boldmoder($row['username'], $row, true);
        }
}

if ( !is_null( $moder ) && $moder > 0 ) {
            
  //REGISTRATIONS
        $how_many = 50;
        $max_id = 1;
        $last_id = 0;
        $limit = '';

        $query = 'SELECT username, email, actkey, CONVERT_TZ(created, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as created from confa_regs order by username'; 
        $result = mysql_query($query);
        if (!$result) {
            mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed ' );
        }

        $num = 1;  
        $out = '';
        while ($row = mysql_fetch_assoc($result)) {
            $created = $row['created'];
            $enc_user = htmlentities($row['username'], HTML_ENTITIES,'UTF-8');
            $enc_mail = htmlentities($row['email'], HTML_ENTITIES,'UTF-8');
            $md5 = $row['actkey'];
            $line = '<tr><td align="center">'. $enc_user . '</td><td align="center">'. $enc_mail . '</td><td align="center">' . $created . '</td><td width="25%" align="center" nowrap>'
              .'<form method="get" action="//'. $host . $root_dir . $page_activate .'" target="bottom">'
              .'<input type="hidden" name="act_link" value="'. $md5.'"/><input name="action" type="submit" value="Confirm"/><input name="action" type="submit" value="Decline"/></form>'
              .'</td></tr>';
            $out .= $line;
            $num++;
        }
        $registrations = $out;
        
	//REGESTERED USERS
        $cur_page = $page_m_users;
        $how_many = 50;
        $max_id = 1;

        $last_id = 0;

        $limit = '';

        $query = 'SELECT count(*) from confa_users';
        if ( !is_null($byip) && strlen($byip) > 0) {
            #Subquery with 'in ()' or 'any' are very slow - easier to write 2 queries
            $subquery = 'select distinct(author) from confa_posts where IP=\'' . $byip . '\''; 
            $result = mysql_query($subquery);
            if (!$result) {
                mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
                die('Query failed ' );
            }   
            $in = '';
            while( $row = mysql_fetch_row( $result ) ) {
                if ( strlen($in) > 0 ) {
                    $in .=',';
                }
                $in .= $row[0];
            }
            $limit  = ' where id in (' . $in . ') ';
            $query .= $limit;
            $result = mysql_query($query);
        } else {
            $result = mysql_query($query);
        }

        if (!$result) {
            mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed ' );
        }
        $row = mysql_fetch_row($result);
        $count = $row[0]; 

        #update banned users
        $query = 'update confa_users set ban=NULL, ban_ends=\'0000-00-00 00:00:00\' where ban_ends > \'0000-00-00 00:00:00\' and ban_ends < current_timestamp()';
        $result = mysql_query($query);
        if (!$result) {
            mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed ' );
        }

        $query = 'SELECT username, status, moder, ban, CONVERT_TZ(ban_ends, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as ban_ends, CONVERT_TZ(created, \'' 
          . $server_tz . '\', \'' . $prop_tz . ':00\') as created, id, CONVERT_TZ((select max(updated) from confa_sessions s where s.user_id=u.id), \'' . $server_tz . '\', \'' . $prop_tz . ':00\') last_seen from confa_users u order by username';
        
        $result = mysql_query($query);
        if (!$result) {
            mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
            die('Query failed ' );
        }

        $out = '';
        if (mysql_num_rows($result) == 0) {
            $max_id = $last_id;
        }
        $num = 1;
        while ($row = mysql_fetch_assoc($result)) {
            $id = $row['id'];
            $created = $row['created'];
            $status = 'Active';
            $last_seen = $row['last_seen'];
            if (!is_null($row['ban_ends']) && strcmp($row['ban_ends'], '0000-00-00 00:00:00')) {
                $status = 'Banned till ' . $row['ban_ends'];
            }
            if ( $row['status'] == 2 ) {
                $status = 'Disabled';
            }

            $enc_user = htmlentities($row['username'], HTML_ENTITIES,'UTF-8');
            if ( $row['status'] == 2 ) {
                $enc_user= '<del>' . $enc_user . '</del>';
            }
            
            if (!$last_seen && $row['status'] != 2) {
              $last_seen = '<form method="get" action="//'. $host . $root_dir . $page_activate .'" target="bottom">'
              .'<input type="hidden" name="moduserid" value="'. $id.'"/><input name="action" type="submit" value="Invite again"/></form>';            
            }
            
            $enc_user= boldmoder($enc_user, $row, false);
            $line = '<tr><td>' . $num . ' <a target="bottom" href="' . $root_dir . $page_m_user . '?moduserid=' . $id . '"> ' . $enc_user . ' </a>' . '</td><td align="center">' . $id . '</td><td align="center">' . $status . '</td><td align="center">' . $created . '</td><td align="center">' . $last_seen . '</td></tr>';
            $out .= $line;
            $num++;
        }
    }

natcasesort($users_online);
natcasesort($users_today);

$users_today = array_diff($users_today, $users_online);

require_once('html_head_inc.php');
require_once('custom_colors_inc.php'); 
?>
<base target="bottom">
</head>
<body id="html_body">
<div class="content">
<?php if (isset($registrations) && $registrations) { ?>
  <table width="95%">
  <tr><th>Username</th><th>Email</th><th>Requested</th><th>Action</th></tr>
  <?php print($registrations); ?>
  </table>
<?php } ?>
<div>
<?php if ($logged_in) { ?>
  <h3>Now online (<?=sizeof($users_online)?>)</h3>
  <?=implode(", ", $users_online)?><p/>
  <h3>Visited today (<?=sizeof($users_today)?>)</h3>
  <?=implode(", ", $users_today)?><p/>
  </div>
<?php } ?>
<?php
    if ( !is_null( $moder ) && $moder > 0 ) {

        if (!is_null($err) && strlen($err) > 0) {
            print('<BR><font color="red"><b>' . $err . '</b></font>');
        }
        if (!is_null($byip) && strlen($byip) > 0) {
            print('<P>Posting from IP: <B>' . $byip . '</B>');
        }
?>

<!--<ol>-->
<table width="95%">
<tr><th>Username</th><th>Id</th><th>Status</th><th>Created</th><th>Last seen</th></tr>
<?php print($out); ?>
</table>
<!--</ol>-->
<?php
    } else if (!$logged_in) {
        print( "Access denied." );
    }
?>
</div>
</body>
</html>
<?php

require('tail_inc.php');

?>