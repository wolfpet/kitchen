<html>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<body>
<table>
<?php

 // Connecting, selecting database
  $link = mysql_connect('localhost', 'client', 'client123')
      or die('Could not connect: ' . mysql_error());
  mysql_select_db('confa') or die('Could not select database');
  /*$query = 'SHOW VARIABLES LIKE \'character_set%\'';
  $result = mysql_query($query) or die('Failed set names ' . mysql_error());
  while ($row = mysql_fetch_row($result)) {
    print('1=' . $row[0] . '=' . $row[1] . '<BR>');
  }
  $query = 'SHOW VARIABLES LIKE \'collation%\'';
  $result = mysql_query($query) or die('Failed set names ' . mysql_error());
  while ($row = mysql_fetch_row($result)) {
    print('1=' . $row[0] . '=' . $row[1] . '<BR>');
  }
  //$query = 'set names \'cp1251\'';
  //mysql_query($query) or die('Failed set names');
  */$query = 'set character_set_connection=\'cp1251\'';
  $result = mysql_query($query) or die('Failed set names ' . mysql_error()); 
  $query = 'SELECT username, id  from confa_users order by username';
  $result = mysql_query($query) or die('Query failed ');
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
     $id = $row['id'];
     $user = $row['username'];
     print('<tr><td>' . $user . '</td><td>' . $id . '</td></tr>');
  }

  mysql_close($link);

?>
</table>
</body>
</html>
