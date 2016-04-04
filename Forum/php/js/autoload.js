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
              if (typeof instrument !== 'undefined') {
                instrument("#" + div.id);
              }
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
  if (ele != null) {
    var sidebar = document.getElementById("sidebar");
    if (sidebar == null)
      // setTimeout(window.scrollTo(ele.offsetLeft,ele.offsetTop), 100);
      $('#'+element).scrollTop(0);
    else
      sidebar.scrollTop = 0;
  } else {
    console.log("Cannot scroll to " + element);
  }
}

function load_more() {
	var div = document.getElementById("threads");
  var parent = document.getElementById("html_body");
  if (parent == null || div == null) {
    console.log("Something is not right: threads or html body element is not found");
    return;
  }
	var contentHeight = parent.offsetHeight;
	var yOffset = window.pageYOffset; 
	var y = yOffset + window.innerHeight;
	if ( y >= contentHeight - 300) {
		load_threads(div, max_id, limit);
    if (typeof instrument !== 'undefined') {
      instrument("#" + div.id);
    }
	}
  var scroller = document.getElementById("scroll2top");
  if (scroller != null) {
    var rect = div.getBoundingClientRect();
    // console.log("scroller y=" + y + " yOffset=" + yOffset + " threadsY=" + rect.top);
    if (rect.top < 0) {
      scroller.style.display = "block";
    } else {
      scroller.style.display = "none";
    }
  }
}

function set_max_id(id, how_many) {
  max_id = id;
  limit = how_many;
  console.log("set max id=" + (max_id) + " limit=" + limit);
}

window.onscroll = load_more;

$( "#sidebar" ).scroll(function() {
  if($(this)[0].scrollHeight - 300 > 0 && $(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight - 300) {
    var div = document.getElementById("threads");
    if (div != null)
      load_threads(div, max_id, limit / 2);    
  }
});
