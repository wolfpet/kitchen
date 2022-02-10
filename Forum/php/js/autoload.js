// Automatic loading of threads

var max_id = "";
var loading = false;
var limit = "200";

function load_threads(div, id, count) {
  if (loading) return;
  loading = true;
  
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
        if (xhr.status === OK) {
            // alert(xhr.responseText); // 'This is the returned text.'
            var text = xhr.responseText;
            var id = text.indexOf("id=");
            var start = text.indexOf("<dl");
            if (start >= 0 && text.indexOf("<span") > 0) {
              max_id = text.substring(id + 3, start);
              console.log("new max id=" + (max_id));
              append_content(div, " " + text.substring(start));
              if (typeof instrument !== 'undefined') {
                instrument("#" + div.id);
              }
            } else {
              console.log("no new content");
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

function append_content(div, html) {
  // div.insertAdjacentHTML('beforeend', html);
  var newcontent = document.createElement('div'); newcontent.innerHTML = html; 
  while (newcontent.firstChild) {
    div.appendChild(newcontent.firstChild);
  }
}

function scroll2Top2(){ 
  if ( $('#_contents').length ) {
    $('#_contents').scrollTop(0);
  } else {
    $('#contents', top.document).contents().scrollTop(0);
  }
  return false;
}    

function load_more() {
  // console.log("load_more called");
  
	var div = document.getElementById("threads");
  var parent = document.getElementById("threads_body");
  var iframes = parent != null;
  
  if (!iframes) {
    parent = document.getElementById("_contents");
  }
  
  if (parent == null || div == null) {
    console.log("Something is not right: threads or html body element is not found");
    return;
  }
	var contentHeight = iframes ? parent.offsetHeight : parent.scrollHeight;
	var y = iframes ? window.pageYOffset + window.innerHeight : parent.scrollTop + parent.offsetHeight;
  // console.log("scroller y=" + y + " contentHeight=" + contentHeight + " " + parent.scrollTop);

	if ( y >= contentHeight - 300) {
    console.log("load_more thinks we need to load more and called load_threads");
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

if (document.getElementById("_contents")) {
  console.log("div mode detected");
  document.getElementById("_contents").onscroll = load_more;
} else if (parent.load_more) {
  // iOS
  console.log("iOS needs to ask whether to load more");
  
  setInterval(function(){ 
    // ask parent script to see if we need to load more threads
    if (parent.load_more()) {
      console.log("iOS needs to ask to load more");
      var div = document.getElementById("threads");
      if (div != null) {
        console.log("iOS calls load_threads");
        load_threads(div, max_id, limit);
      }
    }
  }, 500);
} else {
  // everything else
  window.onscroll = load_more;
}

function set_max_id(id, how_many) {
  max_id = id;
  limit = how_many;
  console.log("set max id=" + (max_id) + " limit=" + limit);
}
