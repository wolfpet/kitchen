$(document).ready(function() {

colorInit();

function sortUL(selector) {
  var $list = $(selector);

  $list.children().detach().sort(function(a, b) {
    return $(a).text().localeCompare($(b).text());
  }).appendTo($list);
}
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
                sortUL('#lstIgnor');
                sortUL('#lstNoIgnor');
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
                sortUL('#lstIgnor');
                sortUL('#lstNoIgnor');
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
