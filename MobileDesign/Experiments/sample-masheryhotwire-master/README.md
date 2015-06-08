MASHERY-HOTWIRE API DEMO APP
==================================================================
Created lovingly for the developer community by Mashery.
http://www.mashery.com
http://developer.mashery.com

Intel(R) XDK
-------------------------------------------
This sample is part of the Intel(R) XDK. 
Please sign up at http://software.intel.com/en-us/html5.
To see the technical detail of the sample, please visit the sample article page 
at http://software.intel.com/html5/articles/integrating-apis-with-xdk-hotwire-sample-app. 

Application Files
-----------------
* app.json
* icon.png
* index.html
* readme.md
* screenshot.png
* css/*
* js/*

Copyright (c) 2012-2013, Intel Corporation. All rights reserved.

Redistribution and use in source and binary forms, with or without modification, 
are permitted provided that the following conditions are met:

- Redistributions of source code must retain the above copyright notice, 
  this list of conditions and the following disclaimer.

- Redistributions in binary form must reproduce the above copyright notice, 
  this list of conditions and the following disclaimer in the documentation 
  and/or other materials provided with the distribution.

- Neither the name of Intel Corporation nor the names of its contributors 
  may be used to endorse or promote products derived from this software 
  without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, 
THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE 
ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE 
LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR 
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE 
GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) 
HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT 
LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT 
OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

App Framework (formerly jQ.Mobi)
-----------------------------------------------------------------------------
* source:  https://github.com/01org/appframework
* license: https://github.com/01org/appframework/blob/master/license.txt

SYNOPSIS
==================================================================
This demo App provides a way to see the Hotwire API in action. 
It is built using Intel(R) XDK that lets you create mobile apps for smartphones and tablets using
standard web languages (HTML5, CSS, and JavaScript).


WHAT DOES THIS APP DO?
==================================================================
Uses Hotwire's API to find fantastic hotel deals within the vicinity of a postal code.

GETTING STARTED
==================================================================
You will need the following to get started -

1. Intel(R) XDK. Free Download - http://xdk-software.intel.com/
2. A Hotwire API key (Register at http://developer.hotwire.com/member/register).


OBTAINING THE API KEY
==================================================================
Before you can begin using this app, you will need to get an API key 
from Hotwire at http://developer.hotwire.com/member/register. This will also 
give you a Single Sign-On Mashery ID with access to hundreds of other APIs.


SETTING UP THE API KEY IN THIS APP
==================================================================
Once you have obtained your API key, assign the API key to the 
variable apiKey on line 1 of the file api.js, like so -

<pre>
	var apiKey ='your_api_key_here';
</pre>


ABOUT THE HOTWIRE API
==================================================================
Hotwire's APIs allows developer to access travel deals and pricing information. The APIs available are:

1. Hotel Deals
2. Hotel Shopping
3. Rental Car Shopping
4. TripStarter
5. Travel Ticker Deals

In this demo app we utilize the Hotel Deals API. In the source code
you can observe how easy it is to make an API call, parse the 
API response and return the information back to the end-user. 

Get detailed information about the Hotwire APIs at 
http://developer.hotwire.com/docs/read/Home

HOTWIRE AFFILIATE PROGRAM
=========================
Monetizing your apps built on top of the Hotwire API is possible
with the Hotwire affiliate program. When you are approved, you
modify your API calls to include your affiliate ID. Qualified
airline, hotel and rental car bookings will earn you money as
end-users find and purchase travel deals. 

You can find out more at the Hotwire develeoper portal. 


HOTWIRE INTERACTIVE API DOCUMENTATION
==================================================================
To learn more about the data set provided by Hotwire's APIs, visit
their interactive documentation at http://developer.hotwire.com/iodocs


ABOUT THE MASHERY API NETWORK
==================================================================
The Mashery API Network (http://developer.mashery.com) is an open
data commons of over 50 RESTful APIs that developers can access 
with their Mashery ID.  

Mashery is the world's leading API management service provider, helping 
companies provide the best API experience for developers, as well as 
the most advanced API management and reporting tools to our clients. 


EXPLORE MORE APIS
==================================================================
Check out Mashery's API Network at http://developer.mashery.com/apis
to explore other awesome APIs including NY Times, Klout, USA TODAY, 
Rotten Tomatoes, Best Buy, Hoovers, Edmunds, Netflix, Rdio,
ESPN, Rovi and many more. 


SUPPORT
==================================================================
If you have any questions or need any help obtaining an API key, 
you can reach out to us at: developer-relations@mashery.com
