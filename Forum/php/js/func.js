  function insertTag(fieldId, tagId)
  {
    var myField = document.getElementById(fieldId);
    var startTag, endTag;
    if (myField != null)
    {
        // Link tag
        if (tagId == 1)
        {
          startTag = '[url=';
          endTag = ']Title[/url]';
        }
        // Image tag
        else if (tagId == 2)
        {
          startTag = '[img=';
          endTag = ']';
        } else if ( tagId == 3) {
          startTag = '[trl]';
          endTag = '[/trl]';
        }
    
      //Mozilla/Firefox/Netscape 7+/Opera/Chrome support
      if (myField.selectionStart || myField.selectionStart == '0')
      {
        var startPos = myField.selectionStart;
        var endPos = myField.selectionEnd;
        myField.value = myField.value.substring(0, startPos)
          + startTag + myField.value.substring(startPos, endPos)
          + endTag + myField.value.substring(endPos, myField.value.length);
      } 
      //IE support
      else if (document.selection)
      {
        myField.focus();
        sel = document.selection.createRange();
        sel.text = startTag + sel.text + endTag;
  //      alert('IE');
      }
      else 
      {
        myField.value = myField.value + startTag + endTag;
      }
    }    
  }

function insertURL(element) {
  // get selection start, end and selected content
  var ss = element.selectionStart;
  var se = element.selectionEnd;
  var st = ""; // selected text
  var tl = 0;  // length of text after selected text
  
  if (typeof ss === "number" && typeof se === "number") { // Support real browsers only. Because f### MS.
    st = element.value.substring(ss, se);
    tl = element.value.length - se;
  } else {
    return;
  }
  
  setTimeout(function() {
    var nt = element.value.substring(ss, element.value.length - tl);
    var pattern = /(\.jpg|jpeg|gif|png|bmp|googleusercontent\.com)/i;
    if (st.length > 0) { // Only activate if there is selection
      if (nt.indexOf("http") == 0) {
        element.value = ((ss > 0) ? element.value.substring(0, ss) : "") + "[url=" + nt + "]" + 
          (st.length > 1 && st.charAt(st.length-1) == ' ' ? st.substring(0, st.length-1) : st) + "[/url]" + (st.length > 1 && st.charAt(st.length-1) == ' ' ? " " : "") + element.value.substring(element.value.length - tl); 
        if (element.setSelectionRange) { 
          element.setSelectionRange(element.value.length - tl, element.value.length - tl); 
        }
      }
    } else if (nt.indexOf("http") == 0 && pattern.test(nt)) { // image?
        element.value = ((ss > 0) ? element.value.substring(0, ss) : "") + "[img=" + nt + "]" + element.value.substring(element.value.length - tl);
    }
  }, 4);
}

function insertBBCode(fieldId, tag)
{
  var element = document.getElementById(fieldId);

  // get selection start, end and selected content
  var ss = element.selectionStart;
  var se = element.selectionEnd;
  var st = ""; // selected text
  var tl = 0;  // length of text after selected text
  
  if (typeof ss === "number" && typeof se === "number") { // Support real browsers only. Because f### MS.
    st = element.value.substring(ss, se);
    tl = element.value.length - se;
    // get rid of the trailing space that Chrome adds to the selection when you select by double-click  
    if (st.length > 1 && st.charAt(st.length-1) == ' ' && st.charAt(st.length-2) != ' ') { 
      st = st.substring(0, st.length-1); 
      tl++;
    } 
  }
 
  if (st.length > 0) { // if there is selection
    element.value = ((ss > 0) ? element.value.substring(0, ss) : "") + "[" + tag + "]" + st + "[/" + tag + "]" + 
    element.value.substring(element.value.length - tl); 
    if (element.setSelectionRange) { 
      element.setSelectionRange(element.value.length - tl, element.value.length - tl); 
    }
  } else { // no selection, insert the tag at selection start
    if (element.value.substring(0, ss).lastIndexOf('[/' + tag + ']') >= element.value.substring(0, ss).lastIndexOf('[' + tag + ']')) {
      element.value = ((ss > 0) ? element.value.substring(0, ss) : "") + '[' + tag +']' + element.value.substring(element.value.length - tl); 
      if (element.setSelectionRange) { 
        element.setSelectionRange(ss + tag.length + 2, ss + tag.length + 2); 
      }
    } else {
      element.value = ((ss > 0) ? element.value.substring(0, ss) : "") + '[/' + tag +']' + element.value.substring(element.value.length - tl); 
      if (element.setSelectionRange) { 
        element.setSelectionRange(ss + tag.length + 3, ss + tag.length + 3); 
      }
    }
  }
  element.focus();
}

// Basic cross browser addEvent
function addEvent(elem, event, fn){
if(elem.addEventListener){
  elem.addEventListener(event, fn, false);
}else{
  elem.attachEvent("on" + event,
  function(){ return(fn.call(elem, window.event)); });
}}

// Demo: move caret to the end of textbox on focus
/*
addEvent(element,focus,function(){
  this.selectionStart = this.selectionEnd = this.value.length;
});
*/