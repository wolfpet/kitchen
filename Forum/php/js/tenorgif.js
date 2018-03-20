// url Async requesting function
function httpGetAsync(theUrl, callback)
{
    // create the request object
    var xmlHttp = new XMLHttpRequest();

    // set the state change callback to capture when the response comes in
    xmlHttp.onreadystatechange = function()
    {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200)
        {
            callback(xmlHttp.responseText);
        }
    }

    // open as a GET call, pass in the url and set async = True
    xmlHttp.open("GET", theUrl, true);

    // call send with no params as they were passed in on the url string
    xmlHttp.send(null);

    return;
}

// callback for the top 8 GIFs of search
function tenorCallback_search(responsetext)
{
    // parse the json response
    var response_objects = JSON.parse(responsetext);

    top_10_gifs = response_objects["results"];

    // load the GIFs -- for our example we will load the first GIFs preview size (nanogif) and share size (tinygif)
    //less than 10?
    var collection = top_10_gifs.length;
    for(i=0; i<collection; i++)
    {
	document.getElementById("preview_gif" +i).src = top_10_gifs[i]["media"][0]["tinygif"]["url"];
    }
    //document.getElementById("share_gif").src = top_10_gifs[0]["media"][0]["tinygif"]["url"];

    return;

}


// function to call the trending and category endpoints
function grab_data(anon_id)
{
    // set the apikey and limit
    var apikey = "LSVB2NY57LBO";
    var lmt = 10;

    // test search term
    var search_term = document.getElementById("gifSearchText").value;
    // using default locale of en_US
    var search_url = "https://api.tenor.com/v1/search?tag=" + search_term + "&key=" +
            apikey + "&limit=" + lmt + "&anon_id=" + anon_id;

    httpGetAsync(search_url,tenorCallback_search);

    // data will be loaded by each call's callback
    return;
}


// callback for anonymous id -- for first time users
function tenorCallback_anonid(responsetext)
{
    // parse the json response
    var response_objects = JSON.parse(responsetext);

    anon_id = response_objects["anon_id"];

    user_anon_id = anon_id;

    // pass on to grab_data
    grab_data(anon_id);
}
