  function insertTag(fieldId, tagId) {
    var myField = fieldId == null ? lastRussianField : document.getElementById(fieldId);
    console.log("insertTag('" + fieldId + "'," + tagId + "): " + myField);
    if (typeof myField == 'undefined' || myField == null) return;

    var startTag, endTag;
    if (myField != null)
    {
        // Link tag
        if (tagId == 1)
        {
          startTag = '[url=';
          endTag = ']Link[/url]';
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
        if (tagId == 1 && myField.setSelectionRange) { 
          var pos = startPos + startTag.length + (endPos - startPos) + 1;
          myField.setSelectionRange(pos, pos + 4); // length of 'Link'
        }
        myField.focus();          
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
  var tv = ""; // text after selection
  
  if (typeof ss === "number" && typeof se === "number") { // Support real browsers only. Because f### MS.
    st = element.value.substring(ss, se);
    tv = element.value.substring(se);
    // console.log("insertURL: [" + st + "] ss=" + ss + " se=" + se);
  } else {
    return;
  }
  
  setTimeout(function() {
    var value = element.value;
    var ep = value.lastIndexOf(tv);   // new 'after selection' position
    var nt = value.substring(ss, ep); // new text
    var pattern = /(\.jpg|jpeg|gif|png|bmp|googleusercontent\.com)/i;
    if (st.length > 0) { // Only activate if there is selection
      if (nt.indexOf("http") == 0) {
        // console.log("insertURL: nt=[" + nt + "]");
        element.value = ((ss > 0) ? value.substring(0, ss) : "") + "[url=" + nt + "]" + 
          (st.length > 1 && st.charAt(st.length-1) == ' ' ? st.substring(0, st.length-1) : st) + "[/url]" +
          (st.length > 1 && st.charAt(st.length-1) == ' ' ? " " : "") + value.substring(ep);
        if (element.setSelectionRange) { 
          ep = element.value.lastIndexOf(tv);
          element.setSelectionRange(ep, ep); 
        }
      }
    }
  }, 4);
}

function insertBBCode(fieldId, tag) {
  var element = document.getElementById(fieldId);
  console.log("insertBBCode('" + fieldId + "','" + tag + "'): " + element);
  if (typeof element == 'undefined' || element == null) return;
  
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
 
  var msgbody = document.getElementById("msgbody");
  var subject = msgbody != null ? document.getElementById("subject") : null;
  //console.log("tag=" + tag + " message to quote? " + (msgbody != null));
  if (st.length > 0) { // if there is selection
    element.value = ((ss > 0) ? element.value.substring(0, ss) : "") + "[" + tag + "]" + st + "[/" + tag + "]" + 
    element.value.substring(element.value.length - tl); 
    if (element.setSelectionRange) { 
      element.setSelectionRange(element.value.length - tl, element.value.length - tl); 
    }
  } else if (tag == "quote" && msgbody != null && (getSelectedTextWithin(msgbody) != "" || getSelectedTextWithin(subject) != "")) {
    // insert quoted text at insertion
    var quote = getSelectedTextWithin(msgbody);
    if (quote == "") quote = getSelectedTextWithin(subject);
    var textToInsert = '[' + tag +']' + quote + '[/' + tag +']';
    element.value = ((ss > 0) ? element.value.substring(0, ss) : "") + textToInsert + element.value.substring(element.value.length - tl); 
    if (element.setSelectionRange) { 
      element.setSelectionRange(ss + textToInsert.length, ss + textToInsert.length); 
    }
  } else { // no selection, insert the tag at selection start
    if (element.value.substring(0, ss).lastIndexOf('[/' + tag + ']') >= element.value.substring(0, ss).lastIndexOf('[' + tag /*+ ']'*/)) {  // to support tags like quote=
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

  function insertSmiley(fieldId, smileyName)
  {
    var myField = document.getElementById(fieldId);
    if (myField != null)
    {    
      //Mozilla/Firefox/Netscape 7+/Opera/Chrome support
      if (myField.selectionStart || myField.selectionStart == '0')
      {
        var startPos = myField.selectionStart;
        var endPos = myField.selectionEnd;
        myField.value = myField.value.substring(0, startPos)
          + ":" + smileyName + ":" +  myField.value.substring(endPos, myField.value.length);
      } 
      //IE support
      else if (document.selection)
      {
        myField.focus();
        sel = document.selection.createRange();
        sel.text = ":" + smileyName + ":";
      }
      else 
      {
        myField.value = myField.value + ":" + smileyName + ":";
      }
    }    
  }

// Basic cross browser addEvent
function addEvent(elem, event, fn) {
  if(elem.addEventListener){
    elem.addEventListener(event, fn, false);
  }else{
    elem.attachEvent("on" + event, function(){ return(fn.call(elem, window.event)); });
  }
}

function bbcode_on() {
    document.getElementById('translit_help').style.display='none';
    document.getElementById('smileys_help').style.display='none';
    if (document.getElementById('bbcode_help').style.display != 'block') {
        document.getElementById('bbcode_help').style.display='block';
    } else {
        document.getElementById('bbcode_help').style.display='none';
    }
}

function smileys_on() {
    document.getElementById('translit_help').style.display='none';
    document.getElementById('bbcode_help').style.display='none';
    if (document.getElementById('smileys_help').style.display != 'block') {
        document.getElementById('smileys_help').style.display='block';
    } else {
        document.getElementById('smileys_help').style.display='none';
    }
}

function getSelectedTextWithin(el) {
    var selectedText = "";
    if (el == null) 
      return selectedText;
    else if (typeof window.getSelection != "undefined") {
        var sel = window.getSelection(), rangeCount;
        if ( (rangeCount = sel.rangeCount) > 0 ) {
            var range = document.createRange();
            for (var i = 0, selRange; i < rangeCount; ++i) {
                range.selectNodeContents(el);
                selRange = sel.getRangeAt(i);
                if (selRange.compareBoundaryPoints(range.START_TO_END, range) == 1 && selRange.compareBoundaryPoints(range.END_TO_START, range) == -1) {
                    if (selRange.compareBoundaryPoints(range.START_TO_START, range) == 1) {
                        range.setStart(selRange.startContainer, selRange.startOffset);
                    }
                    if (selRange.compareBoundaryPoints(range.END_TO_END, range) == -1) {
                        range.setEnd(selRange.endContainer, selRange.endOffset);
                    }
                    selectedText += range.toString();
                }
            }
        }
    } else if (typeof document.selection != "undefined" && document.selection.type == "Text") {
        var selTextRange = document.selection.createRange();
        var textRange = selTextRange.duplicate();
        textRange.moveToElementText(el);
        if (selTextRange.compareEndPoints("EndToStart", textRange) == 1 && selTextRange.compareEndPoints("StartToEnd", textRange) == -1) {
            if (selTextRange.compareEndPoints("StartToStart", textRange) == 1) {
                textRange.setEndPoint("StartToStart", selTextRange);
            }
            if (selTextRange.compareEndPoints("EndToEnd", textRange) == -1) {
                textRange.setEndPoint("EndToEnd", selTextRange);
            }
            selectedText = textRange.text;
        }
    }
    return selectedText;
}
