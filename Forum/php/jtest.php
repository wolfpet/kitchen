<!DOCTYPE html>
<html>
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
</script>
<script>
$(document).ready(function(){
  $("button").click(function(){
    var ar = {};
    ar['test3'] = '5';
    ar['test4'] = '6';
    $.getJSON("json.php", {'json' : ar}/*{'test1' : '1', 'test2' : '3'}*/, function(result){
      $.each(result, function(i, field){
        $("div").append(i +"|");
        $("div").append(field + " ");
      });
    });
  });
});
</script>
</head>
<body>

<button>Get JSON data</button>
<div></div>

</body>
</html>
