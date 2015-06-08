(function()
{
 "use strict";  
 /*
   hook up event handlers 
 */
 function register_event_handlers()
 {
    
    
     /* button  Add Item */
    $(document).on("click", ".uib_w_25", function(evt)
    {
        /* your code goes here */ 
        //this sample add list item to the set
        //alert("button pressed");
        //var myListItem = "<li class=\"widget uib_w_7\" data-uib=\"app_framework/listitem\" data-ver=\"1\"><a href=\"#uib_page_2\" data-transition=\"fade\">Static Item 2</a></li>";
        //$("stuffs_list").append(myListItem);
        //$.ui.loadContent("#stuffs_list", false, false, "");
        
        var li = document.createElement('li');
        li.setAttribute('class','widget uib_w_7');
        li.setAttribute('data-uib','app_framework/listitem');
        li.innerHTML="<span class='af-badge tl'>250</span><a href='http://google.com'>stuffs stuffs and then it's quite a bit of extra text here to understand how the list item is going to behave. And one more: stuffs stuffs and then it's quite a bit of extra text here to understand how the list item is going to behave</a>";
        
        var listStuff = document.getElementById("stuffs_list");
        listStuff.appendChild(li);
        
        
        
        
    });
    
    }
 document.addEventListener("app.Ready", register_event_handlers, false);
})();
