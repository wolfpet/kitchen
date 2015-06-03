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
  }
  
  if (st.length > 0) { // Only activate if there is selection
    setTimeout(function() {
      var nt = element.value.substring(ss, element.value.length - tl);
      if (nt.startsWith("http")) {
        element.value = ((ss > 0) ? element.value.substring(0, ss) : "") + "[url=" + nt + "]" + 
          (st.length > 1 && st.charAt(st.length-1) == ' ' ? st.substring(0, st.length-1) : st) + "[/url]" + (st.length > 1 && st.charAt(st.length-1) == ' ' ? " " : "") + element.value.substring(element.value.length - tl); 
        if (element.setSelectionRange) { 
          element.setSelectionRange(element.value.length - tl, element.value.length - tl); 
        }
      }
    }, 4);
  }
}

