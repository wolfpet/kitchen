var style_collapsed = 'normal'; // italic
var style_normal = 'normal';
var weight_collapsed = 'normal'; // bold
var weight_normal = 'normal';

function getDL(target) {
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

function decorate(target, topic, display_value) {
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

  var src = target;  
  var id = src.nextSibling.name; 
  
  target = getDL(src);
  
  if (target == null) return;
  
  if (target.style.display == "none") {
	decorate(target, src.nextSibling, "inline");
  } else {
	decorate(target, src.nextSibling, "none");
  }  
  // use Web Storage to persist user's selection
  if(typeof(Storage)!=="undefined") {
    localStorage.setItem(id, target.style.display);
  }
}

function recall_state() {
  // restore DL visibility based on data in local storage
  if(typeof(Storage)!=="undefined") {
      // use Web Storage to restore user's selection
    
    // iterate through message topics 
    var array = document.getElementsByTagName("A");
        
    for (var i = 0; i < array.length; ++i) {
      var item = array[i];	  
      if (item.target != "bottom") continue; 
      
      var value = localStorage.getItem(item.name);
            
      if (value != null) { // topic visibility state found in local storage
        var target = getDL(item);           
        if (target != null) {  // sanity check
          decorate(target, item, value);
        } 
      } 
    }     
  } 
};

var windowonloadbeforejunk = window.onload;

window.onload = function (e) {
  try {
    recall_state();
    console.log("recall_state() called");
  } catch (e) {
    console.log("recall_state() failed");
  }
  if (windowonloadbeforejunk != null) {
     windowonloadbeforejunk(e);
  }
}