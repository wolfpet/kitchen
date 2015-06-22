// Automatic loading of threads

var max_id = "";
var loading = false;
var limit = "200";

function load_threads(div, id, count) {
  if (loading) return;
  loading = true;
  
  var indicator = document.getElementById('loading');
  if (indicator != null) 
    indicator.style.display = "block";
  
  if (max_id.length == 0) {
    max_id = "" + id;  
    console.log("max id=" + max_id);
  }
  
  // Initialize the Ajax request.
  var xhr = new XMLHttpRequest();
  var url = 'get_threads.php?id=' + max_id + "&custom=" + count;
  console.log("loading " + url);

  xhr.open('get', url);
   
  // Track the state changes of the request.
  xhr.onreadystatechange = function () {
    var DONE = 4; // readyState 4 means the request is done.
    var OK = 200; // status 200 is a successful return.
    if (xhr.readyState === DONE) {        
      try {
        var indicator = document.getElementById('loading');
        if (indicator != null) indicator.style.display = "none";
        if (xhr.status === OK) {
            // alert(xhr.responseText); // 'This is the returned text.'
            var text = xhr.responseText;
            var id = text.indexOf("id=");
            var start = text.indexOf("<dl");
            if (start >= 0) {
              max_id = text.substring(id + 3, start);
              console.log("new max id=" + (max_id));
              div.innerHTML = div.innerHTML + " " + text.substring(start);
            }
        } else {
            // alert('Error: ' + xhr.status); // An error occurred during the request.
        }
      } finally {
        loading = false;
      }
    }
  };
   
  xhr.send(null);
}

function scroll2Top(element){ 
  var ele = document.getElementById(element); 
  setTimeout(window.scrollTo(ele.offsetLeft,ele.offsetTop), 100);
}

function load_more() {
	var div = document.getElementById("threads");
	var contentHeight = document.getElementById("body").offsetHeight;
	var yOffset = window.pageYOffset; 
	var y = yOffset + window.innerHeight;
	if ( y >= contentHeight - 300) {
		load_threads(div, max_id, limit);
	}
}

function set_max_id(id, how_many) {
  max_id = id;
  limit = how_many;
  console.log("set max id=" + (max_id) + " limit=" + limit);
}

window.onscroll = load_more;