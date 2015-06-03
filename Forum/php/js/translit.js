var lastRussianField = null;
var lastEmoticonField = null;
var transToggleKeyCode = 27;
var rusCharset      = false;


// Public function. 
// Should be executed on event "focus" from any field that wishes to receive
// Cyrillic letters and/or emoticons by the means of user clicking on a
// letter button or emoticon picture.
// Parameters:
//
// elemText - object of type INPUT or TEXTAREA
// acceptsRussian - boolean
function RegisterField(elemText, acceptsRussian, acceptsEmoticons) 
{
    if (acceptsRussian)
        lastRussianField = elemText;
    if (acceptsEmoticons)
        lastEmoticonField = elemText;
}  

function toggleCharset()
{
  rusCharset = ! rusCharset;
  document.getElementById('ruschars').style.display='none';
  document.getElementById('latchars').style.display='none';
  if (rusCharset) {
    document.getElementById('ruschars').style.display='block';
  } else {
    document.getElementById('latchars').style.display='block';
  }
}

function text_OnKeydown(event)
{
  transToggle(event);
}


// Public function.
function transToggle(evt)
{
    if (evt.keyCode == transToggleKeyCode) 
    {
        toggleCharset();
        // The following code prevents browser from clearing the input text box when ESC is pressed.
        if (evt.target) // W3C
        {
            evt.target.blur();
            evt.target.focus();
        }
        else            // IE specific
        {
            evt.returnValue = false;
        }
    }
}

// Public function.
function insertLetter(evt, charCode) 
{
    if (evt.target) // W3C
    {
        if (evt.shiftKey) {
            insertChar(String.fromCharCode(charCode).toUpperCase().charCodeAt(0), false); }
        else {
            insertChar(charCode, false); }
    }
    else            // IE specific  
    {
        if (lastRussianField != null) 
        {
            lastRussianField.focus();
            var caretPos = document.selection.createRange();
            if (window.event.shiftKey == true) {
                caretPos.text = String.fromCharCode(charCode).toUpperCase(); }
            else {
                caretPos.text = String.fromCharCode(charCode).toLowerCase(); }
            document.selection.empty();
            caretPos = document.selection.createRange();
            caretPos.move('character', 1);
        }
    }
}

// Public function.
function insertEmoticon(emoticonText) {
    if (lastEmoticonField != null) 
    {
        if (navigator.userAgent.indexOf("MSIE") != -1)  // IE specific
        {
            lastEmoticonField.focus();
            var caretPos = document.selection.createRange();
            caretPos.text = emoticonText;       
        }
        else                // W3C
        {
            var origPos = lastEmoticonField.selectionStart; 
            lastEmoticonField.value = lastEmoticonField.value.substring(0, origPos) + emoticonText + lastEmoticonField.value.substring(lastEmoticonField.selectionEnd, lastEmoticonField.value.length);
            lastEmoticonField.selectionStart = origPos + emoticonText.length;
            lastEmoticonField.selectionEnd = lastEmoticonField.selectionStart;
            lastEmoticonField.focus();  
        }
    }
}

// Public function.
function translate2(evt) 
{
    if (rusCharset == true) 
    {
        if (evt.target)
        {
            translateW3C(evt);
        }
        else
        {
            translateIE();
        }
    }
}
    
    
// Private function.
function insertChar(charCode, deletePrevChar) 
{
    if (lastRussianField != null) {
        var origPos = lastRussianField.selectionStart;
        var origScroll = lastRussianField.scrollTop;    
        if (deletePrevChar)
            origPos = origPos - 1
        lastRussianField.value = lastRussianField.value.substring(0, origPos) + String.fromCharCode(charCode) + lastRussianField.value.substring(lastRussianField.selectionEnd, lastRussianField.value.length);
        lastRussianField.selectionStart = origPos + 1;
        lastRussianField.selectionEnd = lastRussianField.selectionStart;
        lastRussianField.scrollTop = origScroll;
        lastRussianField.focus();
    }
}

// Private function.
function translateIE()
{
    var cyrCode = letter(window.event.keyCode);
    lastRussianField.range = document.selection.createRange();
    lastRussianField.range.moveStart("character", -1);
    switch (String.fromCharCode(window.event.keyCode).toLowerCase()) {
        case "a": 
            switch (lastRussianField.range.text.charCodeAt(0)) {
                case 1049: //Ja
                case 1067: //Ya
                    lastRussianField.range.text = "";
                    cyrCode = 1071;
                    break; 
                case 1081: //ja
                case 1099: //ya
                    lastRussianField.range.text = "";
                    cyrCode = 1103;
                    break;
            }
            break;
        case "e":
            switch (lastRussianField.range.text.charCodeAt(0)) {
                case 1049: //Je
                    lastRussianField.range.text = "";
                    cyrCode = 1069;
                    break;
                case 1081: //je
                    lastRussianField.range.text = "";
                    cyrCode = 1101; 
                    break;
            }
            break;
        case "h":
            switch (lastRussianField.range.text.charCodeAt(0)) {
                case 1062: //Ch
                    lastRussianField.range.text = "";
                    cyrCode = 1063;
                    break;
                case 1094: //ch
                    lastRussianField.range.text = "";
                    cyrCode = 1095;
                    break;
                case 1043: //Gh
                case 1047: //Zh
                    lastRussianField.range.text = "";
                    cyrCode = 1046;
                    break;
                case 1075: //gh
                case 1079: //zh
                    lastRussianField.range.text = "";
                    cyrCode = 1078;
                    break;
                case 1057: //Th
                    lastRussianField.range.text = "";
                    cyrCode = 1064;
                    break;
                case 1089: //th
                    lastRussianField.range.text = "";
                    cyrCode = 1096;
                    break;
                case 1058: //Sh
                    lastRussianField.range.text = "";
                    cyrCode = 1065;
                    break;
                case 1090: //sh
                    lastRussianField.range.text = "";
                    cyrCode = 1097;
                    break;
            }
            break;
        case "o":
            switch (lastRussianField.range.text.charCodeAt(0)) {
                case 1049: //Jo
                case 1067: //Yo
                    lastRussianField.range.text = "";
                    cyrCode = 1025;
                    break;
                case 1081: //jo
                case 1099: //yo
                    lastRussianField.range.text = "";
                    cyrCode = 1105; 
                    break;
            }
            break;
        case "u":
            switch (lastRussianField.range.text.charCodeAt(0)) {
                case 1049: //Ju
                case 1067: //Yu
                    lastRussianField.range.text = "";
                    cyrCode = 1070;
                    break;
                case 1081: //ju
                case 1099: //yu
                    lastRussianField.range.text = "";
                    cyrCode = 1102; 
                    break;
            }
            break;
        case "y":
            switch (lastRussianField.range.text.charCodeAt(0)) {
                case 1045: //Ey
                    lastRussianField.range.text = "";
                    cyrCode = 1069;
                    break;
                case 1077: //ey
                    lastRussianField.range.text = "";
                    cyrCode = 1101;
                    break;
            }
            break;
        case "'":
            if (lastRussianField.range.text.charCodeAt(0) == 1100) {
                lastRussianField.range.text = "";
                cyrCode = 1068; 
            }
            break;
    }
    window.event.keyCode = cyrCode;
}

// Private function.
function translateW3C(evt) 
{
    var returnValue;
    if (rusCharset && !evt.altKey && !evt.ctrlKey) {
        returnValue = false;
        var cyrCode = letter(evt.which);
        switch (String.fromCharCode(evt.which).toLowerCase()) {
        case "a":
            switch (lastRussianField.value.charCodeAt(lastRussianField.selectionStart-1)) {
                case 1049: //Ja
                case 1067: //Ya
                    insertChar(1071, true);
                    break;
                case 1081: //ja
                case 1099: //ya
                    insertChar(1103, true);
                    break;
                default:
                    insertChar(cyrCode, false); 
            }
            break;
        case "e":
            switch (lastRussianField.value.charCodeAt(lastRussianField.selectionStart-1)) {
                case 1049: //Je
                    insertChar(1069, true);
                    break;
                case 1081: //je
                    insertChar(1101, true);
                    break;
                default:
                    insertChar(cyrCode, false); 
            }
            break;
        case "h":
            switch (lastRussianField.value.charCodeAt(lastRussianField.selectionStart-1)) {
                case 1062: //Ch
                    insertChar(1063, true);
                    break;
                case 1094: //ch
                    insertChar(1095, true);
                    break;
                case 1043: //Gh
                case 1047: //Zh
                    insertChar(1046, true);
                    break;
                case 1075: //gh
                case 1079: //zh
                    insertChar(1078, true);
                    break;
                case 1057: //Th
                    insertChar(1064, true);
                    break;
                case 1089: //th
                    insertChar(1096, true);
                    break;
                case 1058: //Sh
                    insertChar(1065, true);
                    break;
                case 1090: //sh
                    insertChar(1097, true);
                    break;
                default:
                    insertChar(cyrCode, false); 
            }
            break;
        case "o":
            switch (lastRussianField.value.charCodeAt(lastRussianField.selectionStart-1)) {
                case 1049: //Jo
                case 1067: //Yo
                    insertChar(1025, true);
                    break;
                case 1081: //jo
                case 1099: //yo
                    insertChar(1105, true);
                    break;
                default:
                    insertChar(cyrCode, false); 
            }
            break;
        case "u":
            switch (lastRussianField.value.charCodeAt(lastRussianField.selectionStart-1)) {
                case 1049: //Ju
                case 1067: //Yu
                    insertChar(1070, true);
                    break;
                case 1081: //ju
                case 1099: //yu
                    insertChar(1102, true);
                    break;
                default:
                    insertChar(cyrCode, false); 
            }
            break;
        case "y":
            switch (lastRussianField.value.charCodeAt(lastRussianField.selectionStart-1)) {
                case 1045: //Ey
                    insertChar(1069, true);
                    break;
                case 1077: //ey
                    insertChar(1101, true);
                    break;
                default:
                    insertChar(cyrCode, false);
            }
            break;
        case "'":
            if (lastRussianField.value.charCodeAt(lastRussianField.selectionStart-1) == 1100)
                insertChar(1068, true)
            else
                insertChar(cyrCode, false);
            break;
        default:
            if (evt.which!=cyrCode)
                insertChar(cyrCode, false);
            else
                returnValue = true; 
        } 
    }
    else
        returnValue = true;
    if (!returnValue)
        evt.preventDefault();
}

// Private function.
function letter (keyCode) {
    switch (keyCode) {
        case 65:
            return 1040;
        case 66:
            return 1041;
        case 86:
            return 1042;
        case 71:
            return 1043;
        case 68:
            return 1044;
        case 69:
            return 1045;
        case 90:
            return 1047;
        case 73:
            return 1048;
        case 74:
            return 1049;
        case 75:
            return 1050;
        case 76:
            return 1051;
        case 77:
            return 1052;
        case 78:
            return 1053;
        case 79:
            return 1054;
        case 80:
            return 1055;
        case 82:
            return 1056;
        case 83:
            return 1057;
        case 84:
            return 1058;
        case 85:
            return 1059;
        case 70:
            return 1060;
        case 88:
            return 1061;
        case 72:
            return 1061;
        case 67:
            return 1062;
        case 87:
            return 1065;
        case 126:
            return 1066;
        case 89:
            return 1067;
        case 81:
            return 1071;
        case 97:
            return 1072;
        case 98:
            return 1073;
        case 118:
            return 1074;
        case 103:
            return 1075;
        case 100:
            return 1076;
        case 101:
            return 1077;
        case 122:
            return 1079;
        case 105:
            return 1080;
        case 106:
            return 1081;
        case 107:
            return 1082;
        case 108:
            return 1083;
        case 109:
            return 1084;
        case 110:
            return 1085;
        case 111:
            return 1086;
        case 112:
            return 1087;
        case 114:
            return 1088;
        case 115:
            return 1089;
        case 116:
            return 1090;
        case 117:
            return 1091;
        case 102:
            return 1092;
        case 120:
            return 1093;
        case 104:
            return 1093;
        case 99:
            return 1094;
        case 119:
            return 1097;
        case 96:
            return 1098;
        case 121:
            return 1099;
        case 39:
            return 1100;
        case 113:
            return 1103;
        default:
            return keyCode;
    }
}

