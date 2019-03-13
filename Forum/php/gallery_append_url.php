<html>
<script>
<?php
  $exploded = explode("//", $_GET["image"]);
?>
parent.insertBodyText('body','<?=implode("//", array(
      $exploded[0], 
      implode("/", array_map("rawurlencode", explode("/", $exploded[1]))) ))?>');
parent.toggleImageUpload();
</script>
</html>