historyData = {
    host: "https://history.muffinlabs.com/",

    /*
     * Lightweight JSONP fetcher
     * Copyright 2010-2012 Erik Karlsson. All rights reserved.
     * BSD licensed
     */

    /*
     * Usage:
     * 
     * JSONP.get( 'someUrl.php', {param1:'123', param2:'456'}, function(data){
     *   //do something with data, which is the JSON object you should retrieve from someUrl.php
     * });
     */
    jsonP: (function(){
	      var counter = 0, head, window = this, config = {};
	      function load(url, pfnError) {
		        var script = document.createElement('script'),
			          done = false;
		        script.src = url;
		        script.async = true;
            
		        var errorHandler = pfnError || config.error;
		        if ( typeof errorHandler === 'function' ) {
			          script.onerror = function(ex){
				            errorHandler({url: url, event: ex});
			          };
		        }
		        
		        script.onload = script.onreadystatechange = function() {
			          if ( !done && (!this.readyState || this.readyState === "loaded" || this.readyState === "complete") ) {
				            done = true;
				            script.onload = script.onreadystatechange = null;
				            if ( script && script.parentNode ) {
					              script.parentNode.removeChild( script );
				            }
			          }
		        };
		        
		        if ( !head ) {
			          head = document.getElementsByTagName('head')[0];
		        }
		        head.appendChild( script );
	      }
	      function encode(str) {
		        return encodeURIComponent(str);
	      }
	      function jsonp(url, params, callback, callbackName) {
		        var query = (url||'').indexOf('?') === -1 ? '?' : '&', key;
				    
		        callbackName = (callbackName||config['callbackName']||'callback');
		        var uniqueName = callbackName + "_json" + (++counter);
		        
		        params = params || {};
		        for ( key in params ) {
			          if ( params.hasOwnProperty(key) ) {
				            query += encode(key) + "=" + encode(params[key]) + "&";
			          }
		        }	
		        
		        window[ uniqueName ] = function(data){
			          callback(data);
			          try {
				            delete window[ uniqueName ];
			          } catch (e) {}
			          window[ uniqueName ] = null;
		        };
            
		        load(url + query + callbackName + '=' + uniqueName);
		        return uniqueName;
	      }
	      function setDefaults(obj){
		        config = obj;
	      }
	      return {
		        get:jsonp,
		        init:setDefaults
	      };
    }()),
	  load : function(options) {
		    var callback, month, day, host;
 
		    if ( typeof(options) == "function" ) {
			      callback = options;
		    }
		    else if ( typeof(options) == "object" ) {
			      callback = options.callback;
			      month = options.month;
			      day = options.day;
	      }
        
		    this.jsonP.get(this.host + '/date', {}, function(tmp) {
				    historyData.data = tmp.data;
				    historyData.url = tmp.url;
				    historyData.date = tmp.date;

				    if ( typeof(callback) == "function" ) {
					      callback(historyData);
				    }
			  });
	  },
    shuffle : function(a) {
      var j, x, i;
      for (i = a.length - 1; i > 0; i--) {
          j = Math.floor(Math.random() * (i + 1));
          x = a[i];
          a[i] = a[j];
          a[j] = x;
      }
      return a;      
    },    
    start : function (callback) {
      historyData.load( function(payload) {
        historyData.shuffle(payload.data.Events);
        historyData.payload = payload;
        historyData.index = 0;
        historyData.callback = callback;
        historyData.display();
      });    
    },
    timeout : 15000,
    paused : false,
    click : function(btn) {
      if (btn.id == "play" || btn.id == "pause") {
        btn.classList.toggle("hidden");
        document.getElementById(btn.id == "pause" ? "play" : "pause").classList.toggle("hidden");
        historyData.paused = !historyData.paused;
      } else if (btn.id == "skipback") {
        historyData.skip(-1);
        historyData.display(true);
      } else {
        historyData.skip(1);
        historyData.display(true);        
      }
      btn.blur();
      return false;
    },
    skip : function (increment) {
      if (increment) {        
      } else {
        increment = 1;
      }
      historyData.index += increment;

      var data = historyData.payload.data;
      if (historyData.index >= data.Events.length) {
        historyData.index = 0;
      } else if (historyData.index < 0) {
        historyData.index = data.Events.length-1;
      }
    },
    display : function (noskip) {
      var div = document.getElementById("history");
      if (div) {
        if (historyData.time) clearTimeout(historyData.time);

        var text = "";

        var data = historyData.payload.data;
        if (data.Events.length > 0) {
          
          var event = data.Events[historyData.index];          
          event.date = historyData.payload.date;
          
          if (!noskip) historyData.skip();          
          
          if (historyData.callback) {
            text = historyData.callback(event);
          } else {
            + '<input id="skipback" class="playbutton" type="image" src="https://upload.wikimedia.org/wikipedia/commons/8/8d/Oxygen480-actions-media-skip-backward.svg" onclick="historyData.click(this);" focusable="false"></input>'
            + '<input id="play" class="playbutton'  + (historyData.paused ? " hidden" : "") + '" type="image" src="https://upload.wikimedia.org/wikipedia/commons/9/9d/Oxygen480-actions-media-playback-start.svg" onclick="historyData.click(this);" focusable="false"></input>'
            + '<input id="pause" class="playbutton' + (historyData.paused ? "" : " hidden") + '" type="image" src="https://upload.wikimedia.org/wikipedia/commons/8/83/Oxygen480-actions-media-playback-pause.svg" onclick="historyData.click(this);" focusable="false"></input>'
            + '<input id="skipforward" class="playbutton" type="image" src="https://upload.wikimedia.org/wikipedia/commons/4/4e/Oxygen480-actions-media-skip-forward.svg" onclick="historyData.click(this);" focusable="false"></input>'
            + '</div><div>' + event.html +"</div><p/>";
          }
        }
        div.innerHTML = text;
        historyData.time = setTimeout(historyData.display, historyData.timeout);
      }
  }
}