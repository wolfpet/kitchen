<?php

require_once('head_inc.php');
require_once('html_head_inc.php');

?>
<html>
<script>
var itm = parent.bottom.document.getElementById('msgform');
var cln = itm.cloneNode(true);


document.addEventListener("DOMContentLoaded", function(event) {
document.getElementById("form_clone").appendChild(cln);
document.getElementById("preview").checked = true;
document.getElementById("previewPath").style.fill="red";
cln.submit();

});

function resizeMe(iframe)
{
    iframe.width  = iframe.contentWindow.document.body.scrollWidth;
        iframe.height = iframe.contentWindow.document.body.scrollHeight;
}
</script>

<div id="form_clone" style="display: none">
</div>

</html>