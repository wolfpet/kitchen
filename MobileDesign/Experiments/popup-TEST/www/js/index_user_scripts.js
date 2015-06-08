(function()
{
 "use strict";
 /*
   hook up event handlers 
 */
 function register_event_handlers()
 {
    
    
     /* button  Home */
    $(document).on("click", ".uib_w_7", function(evt)
    {
         activate_subpage("#uib_page_2"); 
    });
    
        /* button  Favourites */
    $(document).on("click", ".uib_w_6", function(evt)
    {
         activate_subpage("#uib_page_3"); 
    });
    
        /* button  messages */
    $(document).on("click", ".uib_w_3", function(evt)
    {
         activate_subpage("#uib_page_4"); 
    });
    
        /* button  Profile */
    $(document).on("click", ".uib_w_4", function(evt)
    {
         activate_subpage("#uib_page_5"); 
    });
    
        /* button  Button */
    $(document).on("click", ".uib_w_11", function(evt)
    {
        /* your code goes here */ 
      $.ui.popup({
    title: "Login",
    message: "Username: <input type='text' class='af-ui-forms'><br>Password: <input type='text' class='af-ui-forms' style='webkit-text-security:disc'>",
    cancelText: "Cancel",
    cancelCallback: function () {},
    doneText: "Login",
    doneCallback: function () {
        alert("Logging in")
    },
    cancelOnly: false
});
        
    });
    
    }
 document.addEventListener("app.Ready", register_event_handlers, false);
})();
