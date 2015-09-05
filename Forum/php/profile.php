<?php
/*$Id: profile.php 942 2013-09-01 12:10:18Z dmitriy $*/
require_once('dump.php');
require_once('head_inc.php');
require_once('html_head_inc.php');

    $title= 'Update profile';

?>

<script>
$(document).ready(function() {
            //$("#status_text").html("&nbsp;");
            $('#btnIgnor').click(function(e) {
            $("#status_text").html("&nbsp;");
                var selectedOpts = $('#lstNoIgnor option:selected');
                if (selectedOpts.length == 0) {
                    alert("Nothing to move.");
                    e.preventDefault();
                }

                $('#lstIgnor').append($(selectedOpts).clone());
                $(selectedOpts).remove();
                e.preventDefault();
            });

            $('#btnNoIgnor').click(function(e) {
            $("#status_text").html("&nbsp;");
                var selectedOpts = $('#lstIgnor option:selected');
                if (selectedOpts.length == 0) {
                    alert("Nothing to move.");
                    e.preventDefault();
                }

                $('#lstNoIgnor').append($(selectedOpts).clone());
                $(selectedOpts).remove();
                e.preventDefault();
            });
            $('#Save').click(function(e) {
            $("#status_text").html("&nbsp;");
		var arr = new Array();
                var lstIgnor = document.getElementById('lstIgnor');
                for (i = 0; i < lstIgnor.options.length; i++) {
		   arr.push(lstIgnor[i].value);   
                }
                var show_hidden = 0;
                if ($('#show_hidden').is(':checked')) {
	          show_hidden = 1;	
                } 
                $.post("api.php", {'ignored': arr,'request' : 'ignore.user_ids', 'update_show_hidden' : show_hidden}, function(data,status){
    			//alert(data);
                        //document.getElemenetById("status_text").innerHTML=OK"";
                        $("#status_text").html(data);
  		});
                e.preventDefault();
            });
            $('#safe_mode').change( function(e) {
              $("#status_text").html("&nbsp;");
              var safe_mode = 1;
              if ($('#safe_mode').is(':checked')) {
                safe_mode = 0;	
              } 
              $.post("api.php", {'update_safe_mode' : safe_mode}, function(data,status){
                $("#status_text").html(data);
                if (status == "success" && parent.contents !== undefined) {
                  parent.contents.location.reload(); 
                }
              });
            });            
        });

</script>

<base target="bottom">
</head>
<body>
<h3><?php print($title);?></h3>
<span style="color:red; font-weight:bold;" id="status_text">&nbsp;</span>
<?php

    $query=' SELECT email, prop_bold, prop_tz, safe_mode, show_smileys from confa_users where id = ' . $user_id;
    $result = mysql_query( $query );
    if (!$result) {
        mysql_log(__FILE__, 'query failed ' . mysql_error() . ' QUERY: ' . $query);
        die('Query failed ' );
    }
    $row = mysql_fetch_assoc( $result );
    $email = $row['email'];
    $email2 = $email;
    $profile_bold = $row['prop_bold'];
    $prop_tz = $row['prop_tz'];
    $smileys = $row['show_smileys'];
?>
<div style="position:absolute;left: 0px;top: 0px;width: 100%;z-index: 9999;text-align: right;"><input id="safe_mode" type="checkbox" <?=!isset($safe_mode) || $safe_mode == 0 ? "checked" : ""?>><i>Show NSFW content</i></input>&nbsp;</div>
<?php
require_once("profile_inc.php");
require_once('tail_inc.php');
?>