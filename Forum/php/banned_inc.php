<?php
  $query = 'SELECT u.username, m.username as banned_by, u.ban, CONVERT_TZ(u.ban_ends, \'' . $server_tz . '\', \'' . $prop_tz . ':00\') as ban_end, h.ban_reason from confa_users u, confa_ban_history h, confa_users m '
          .'WHERE u.status = 1 and u.ban_ends>current_timestamp() and u.ban = h.id and m.id=h.moder';
  $result = mysql_query($query);
  if (!$result) {
      mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
      die('Query failed ' );
  }
?>
<table class="banned" border="0" cellspacing=12>
<?php
    $count = 0;
    while ($row = mysql_fetch_assoc($result)) {
        if ($count == 0) {
          print('<tr><th>Username</th><th>Reason</th><th>Moderator</th><th>Ban ends</th></tr>');
        }
        $username = htmlentities($row['username'], HTML_ENTITIES,'UTF-8');
        $translit_done = false;
        $reason = translit($row['ban_reason'], $translit_done);
        $reason = render_for_display($reason);
        $banned_by = htmlentities($row['banned_by'], HTML_ENTITIES,'UTF-8');
        $ban_ends = $row['ban_end'];
        print('<tr><td>' . $username . '</td><td>' . $reason . '</td><td>' . $banned_by . '</td><td>' . $ban_ends . '</td></tr>');
        $count++;
    }
    if ($count == 0) {
        print('(none)');
    }
?>
</table>
