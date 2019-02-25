var style_collapsed = 'normal'; // italic
var style_normal = 'normal';
var weight_collapsed = 'normal'; // bold
var weight_normal = 'normal';

function getDL(target) {
  // get the span
  target = target.parentNode;
  // find the end of message title  
  while (target != null && target.tagName != "BR") {
    target = target.nextSibling;
  }
  // find next DL or message title
  while (target != null && target.tagName != "DL" && target.tagName != "A") {
    target = target.nextSibling;
  }
  // return DL, if found
  return (target != null && target.tagName == "DL") ? target : null;
}

function isTopic(item) {
  for (var i=0; i<4; i++) {
    if (item!=null) item = item.parentNode;
  }
  return item != null && item.tagName == "DIV";
}

function decorate(target, topic, display_value) {
  // console.log("decorating " + target + " topic= " + topic + " " + display_value);
  target.style.display = display_value;
  if (display_value == 'none') {
    topic.style.fontStyle = style_collapsed;
    topic.style.fontWeight= weight_collapsed;
  } else {
    topic.style.fontStyle = style_normal;
    topic.style.fontWeight= weight_normal;
  }
}

function toggle(target) {
  if (target.nextSibling == null) return;
  
  var id  = target.parentNode.id; 
  var src = target;  
  
  target = getDL(src);
  if (target == null) return;
  
  var topic = src.nextSibling;
  while (topic != null && topic.tagName != 'A') {
    topic = topic.nextSibling;    
  } 
    
  if (target.style.display == "none") {
    decorate(target, topic, "block");
  } else {
    decorate(target, topic, "none");
  }  
  // use Web Storage to persist user's selection
  if(typeof(Storage)!=="undefined") {
    localStorage.setItem(id, target.style.display);
  }
}

function recall_state() {
  // restore DL visibility based on data in local storage
  if (typeof(Storage)!=="undefined") {
      // use Web Storage to restore user's selection
    
    // iterate through message topics 
    var array = document.getElementsByTagName("A");
        
    for (var i = 0; i < array.length; ++i) {
      var item = array[i];	  
      if (item.target != "bottom") continue; 
      var value = localStorage.getItem(item.parentNode.id);
            
      if (value != null) { // topic visibility state found in local storage
        var target = getDL(item);           
        if (target != null) {  // sanity check
          decorate(target, item, value);
        } 
      } 
    }     
  } 
};


var toggled = 'none';

function toggleAll() {
    decorateAll(toggled);
    if(toggled=='none') {
      toggled='block'; 
      parent.document.getElementById("toggle").innerHTML = "Expand";
    } else {
      toggled='none'; 
      parent.document.getElementById("toggle").innerHTML = "Collapse";
    }
}

function decorateAll(toggled) {
    // iterate through message topics     
    var array = document.getElementsByTagName("A");
    for (var i = 0; i < array.length; ++i) {
      var item = array[i];
      if (item.target != "bottom" || !isTopic(item)) continue;
      var target = getDL(item);
      
      if (target != null) {  // sanity check
        var value = null;
        
        // if hiding, hide topic else restore the saved state, if found.
        if (toggled != 'none' && typeof(Storage) !== "undefined") {
          value = localStorage.getItem(item.parentNode.id);
        } 
        
        if (value == null) {
          value = toggled;
        }
        
        decorate(target, item, value);
      }
    }  
}
// this function is called on autoload -- we use this to decorate new loaded topics
function instrument(div) {
  decorateAll(toggled=='none' ? 'block' : 'none');
}

var windowonloadbeforejunk = window.onload;

window.onload = function (e) {
  try {
    recall_state();
    console.log("recall_state() called");
  } catch (e) {
    console.log("recall_state() failed: " + e);
  }
  if (windowonloadbeforejunk != null) {
     windowonloadbeforejunk(e);
  }
}