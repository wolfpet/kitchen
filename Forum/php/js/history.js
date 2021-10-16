historyData = {
    host: "https://history.muffinlabs.com",

	  load : function(options) {
		    var callback, month, day, host;
 
		    if ( typeof(options) == "function" ) {
            var today = new Date();
            month = today.getMonth() + 1;
            day = today.getDate();
			      callback = options;
		    }
		    else if ( typeof(options) == "object" ) {
			      callback = options.callback;
			      month = options.month;
			      day = options.day;
	      }
        
        $.getJSON(this.host + '/date/' + month + '/' + day, function(tmp) {
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
            text = '<div><h4 class="history-header">' + event.date + ", " + event.year + '</h4>'
            + '<input id="skipback" class="playbutton" type="image" src="https://upload.wikimedia.org/wikipedia/commons/8/8d/Oxygen480-actions-media-skip-backward.svg" onclick="historyData.click(this);" focusable="false"></input>'
            + '<input id="play" class="playbutton'  + (historyData.paused ? " hidden" : "") + '" type="image" src="https://upload.wikimedia.org/wikipedia/commons/9/9d/Oxygen480-actions-media-playback-start.svg" onclick="historyData.click(this);" focusable="false"></input>'
            + '<input id="pause" class="playbutton' + (historyData.paused ? "" : " hidden") + '" type="image" src="https://upload.wikimedia.org/wikipedia/commons/8/83/Oxygen480-actions-media-playback-pause.svg" onclick="historyData.click(this);" focusable="false"></input>'
            + '<input id="skipforward" class="playbutton" type="image" src="https://upload.wikimedia.org/wikipedia/commons/4/4e/Oxygen480-actions-media-skip-forward.svg" onclick="historyData.click(this);" focusable="false"></input>'
            + '</div><div>' + event.no_year_html +"</div><p/>";
          }
        }
        div.innerHTML = text;
        historyData.time = setTimeout(historyData.display, historyData.timeout);
      }
  }
}