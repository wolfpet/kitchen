<?php

require_once('head_inc.php');

    if ( !is_null( $moder ) && $moder > 0 ) {
    
        function boldmoder($text, $rec, $list) {
          if ($rec['moder']) 
            if (isset($list) && $list)
              return $text . '<span class="edited">*</span>';
            else 
              return '<b>' . $text . '</b>';
          
          return $text;
        }
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

        $num = 1;  

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
            
            $enc_user= boldmoder($enc_user, $row, false);
            $line = '<tr><td>' . $num . ' <a target="bottom" href="' . $root_dir . $page_m_user . '?moduserid=' . $id . '"> ' . $enc_user . ' </a>' . '</td><td align="center">' . $id . '</td><td align="center">' . $status . '</td><td align="center">' . $created . '</td><td align="center">' . $last_seen . '</td></tr>';
            $out .= $line;
            $num++;
        }
    }

sort($users_online);
sort($users_today);

require_once('html_head_inc.php');
require_once('custom_colors_inc.php'); 
?>
<base target="bottom">
</head>
<body id="html_body">
<div class="content">
<div>
<h3>Now online (<?=sizeof($users_online)?>):</h3>
<?=implode(", ", $users_online)?><hr>
<h3>Visited today (<?=sizeof($users_today)?>):</h3>
<?=implode(", ", $users_today)?><hr>
</div>
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
    } else {
        print( "Access denied." );
    }
?>
</div>
</body>
</html>
<?php

require('tail_inc.php');

?>