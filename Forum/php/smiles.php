<?php
/*$Id: smiles.php 988 2014-01-05 01:14:33Z dmitriy $*/

require_once('head_inc.php');
require_once('html_head_inc.php');

?><link rel="stylesheet" type="text/css" href="<?=autoversion('css/diff.css');?>">
<style type="text/css">
body {background:none transparent;
}
</style>
<base target="bottom">
</head>
<body>
<?php
  echo smileys();
  
require_once('tail_inc.php');
?>
</body></html>