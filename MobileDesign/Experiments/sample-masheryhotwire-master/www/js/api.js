/*jslint browser:true, devel:true, white:true, vars:true, eqeq:true */
/*global $:false, html:false, invalidKey:false*/
// ********************SET YOUR API KEY HERE*******************
// Insert your Hotwire API Key here. README for more info.
var apiKey = 'xgcwuv5g7vbn8p4h7srpux6b';
var invalidKey = false;
// ************************************************************

// Check if valid API Key
function checkKeyValidity() {
    var url = 'http://api.hotwire.com/v1/deal/hotel?limit=1&dest=94103&distance=15&apikey=' + apiKey;
    // Docs: http://app-framework-software.intel.com/api2/index.html#$_get
    
    invalidKey = false;
    var keyTest = $.get(url, "GET", function(data) {});
    keyTest.onreadystatechange = function() {
        if (keyTest.readyState == 4) {
            if (keyTest.status == 403) {
                invalidKeyAlert();
                invalidKey = true;
            }
        }
    };
}

function invalidKeyAlert() {
    alert('Invalid API Monkey Ass key. See README and edit js/api.js file.');
}

// Make search API call to Hotwire
// Docs: http://developer.hotwire.com/docs/read/Hotel_Deals_API
function searchDeals() {
    // Check for invalidKey var set in checkKeyValidity function above.
    // If invalid, then send alert, and stop API call execution.
    if (invalidKey) {
        invalidKeyAlert();
        return false;
    }
    var zip = $('#zip').val();
    if (zip.length < 5) {
        alert('Please provide a ZIP code.');
        return false;
    }
    var url = 'http://api.hotwire.com/v1/deal/hotel?format=json&limit=10&distance=10&apikey=' + apiKey + '&dest=' + zip;

    // Docs: http://app-framework-software.intel.com/api2/index.html#$_get
    var apiCall = $.get(url, function(data) {
        searchDealsCallback(data);
    });
}

// The callback function that's executed after the API call is made above.
// If there are no results, send alert. If there are results, iterate through
// and display as links.
function searchDealsCallback(payload) {
    // Docs: http://app-framework-software.intel.com/api2/index.html#$_parseJSON
    var data = $.parseJSON(payload);

    if (!data.Result[0]) {
        alert('No hotel deals could be found there. Sorry!');
        return false;
    }
    
    // Clear out results div (from any previous searches)
    $("#hotwire-results-output .deals").empty();
    
    var html = "<p>Results for hotel deals in " + $('#zip').val() + ":</p>";
    $("#hotwire-results-output .deals").append(html);
    
    for (var x in data.Result) {
        var deal = data.Result[x];
        var html_deal = "<a href=\"" + deal.Url + "\");' target=\"_blank\">" + deal.Headline + 
            "</a>&nbsp;<img src='images/hwarrow.png' height=/></span><br /><br />";
        $("#hotwire-results-output .deals").append(html_deal);
    }
    
    // Transition to 'search-results' panel
    // Docs: http://app-framework-software.intel.com/api2/index.html#$_ui_loadContent
    $.ui.loadContent("#search-results", false, false, "slide");
}
