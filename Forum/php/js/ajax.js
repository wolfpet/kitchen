
    function makeRequest(url, ffff, sAgument) {
        var http_request = false;
        if (window.XMLHttpRequest) { // Mozilla, Safari,...
		
            http_request = new XMLHttpRequest();
            if (http_request.overrideMimeType) {
                http_request.overrideMimeType('text/xml');
                // See note below about this line
            }
        } else if (window.ActiveXObject) { // IE
            try {
                http_request = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                try {
                    http_request = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {}
            }
        }

        if (!http_request) {
            alert('Giving up :( Cannot create an XMLHTTP instance');
            return false;
        }
        http_request.onreadystatechange = function() { alertContents(http_request, ffff, sAgument); };
        http_request.open('GET', url, true);
        http_request.send(null);

    }
	
	function makePostRequest(url, inner_func, snd, other_arg) {
        var http_request = false;
        if (window.XMLHttpRequest) { // Mozilla, Safari,...
            http_request = new XMLHttpRequest();
            if (http_request.overrideMimeType) {
                http_request.overrideMimeType('text/xml');
                // See note below about this line
            }
        } else if (window.ActiveXObject) { // IE
            try {
                http_request = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                try {
                    http_request = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {}
            }
        }

        if (!http_request) {
            alert('Giving up :( Cannot create an XMLHTTP instance');
            return false;
        }
        
		http_request.onreadystatechange = function() { alertContents(http_request, inner_func, other_arg); };
		http_request.open('POST', url, true);
		http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		//http_request.setRequestHeader("Content-length", snd.length);
    //http_request.setRequestHeader("Connection", "close");
    http_request.send(snd);
    }

    function alertContents(http_request, ffff, sAgument) {

        if (http_request.readyState == 4) {
            if (http_request.status == 200) {
              var ttt= ffff(http_request.responseText, sAgument);
              return http_request.responseText;
                //alert(http_request.responseText);
            } else {
                //alert('There was a problem with the request.');
              var ttt= ffff(http_request.responseText, sAgument, http_request.status);
              return http_request.responseText;
            }
        }
    }
/// onChange=" makeRequest('/js/postal.asp?ttt='+tttt()+'&zip='+this.value, fillCity, '3');"
	function tttt(){
		var dddd=new Date();
		return dddd.getTime();
		}
    