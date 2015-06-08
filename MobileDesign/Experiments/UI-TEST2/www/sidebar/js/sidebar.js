/*
 Copyright © 2013 Intel Corporation. All rights reserved
 */
(function($) {

    'use strict';
    window.uib_sb = {};

    /**
     *
     *  @exports uib_sb.reinit();  //not needed for general usage
     *  @exports uib_sb.toggle_sidebar(domNode_query)
     *  @exports uib_sb.close_sidebar(domNode_query, completef)
     *  @exports uib_sb.open_sidebar(domNode_query, completef)
     *  @exports uib_sb.close_all_sidebars()
     *  @exports uib_sb.show_all_overhang(show)
     *  @exports uib_sb.get_dispatch_arg_list(domNode_query)
     *  @exports uib_sb.move_sidebar(domNode_query, anim, v, finishf, property, partner_selector, partner_property, partner_base) //for swipe.js, not general usage
     */


    uib_sb.reinit = function() {
        uib_sb.close_all_sidebars().done(function() {
            $(".upage-content").removeAttr("data-ohb");
            initialize_sidebars();
        });
    };

    $(document).on('uib-sidebar-reinit', uib_sb.reinit);

    /**
     * toggle_sidebar
     * @param domNode_query -- a single jQuery wrapped domNode
     */
    uib_sb.toggle_sidebar = function(domNode_query)
    {
        ["leftbar", "rightbar", "topbar", "botbar"].forEach(function(cname) {
            if (domNode_query.hasClass(cname)) {
                if ($(cname).is(':animated')) { return; }
                else {
                    close_other_sidebars(domNode_query).done(function()
                    {
                        dispatch_sidebar(domNode_query, perform_toggle_sidebar);
                    });
                }
            }
        });
    };

    /**
     * close_sidebar
     * @param domNode_query -- a single jQuery wrapped domNode
     * @param completef     -- OPTIONAL  a function called when the close completes.
     */
    uib_sb.close_sidebar = function(domNode_query, completef)
    {
        dispatch_sidebar(domNode_query, perform_open_close_sidebar, [completef, false]);
    };

    /**
     * open_sidebar
     * @param domNode_query -- a single jQuery wrapped domNode
     * @param completef     -- OPTIONAL  a function called when the open completes.
     */
    uib_sb.open_sidebar = function(domNode_query, completef)
    {
        dispatch_sidebar(domNode_query, perform_open_close_sidebar, [completef, true]);
    };


    uib_sb.close_all_sidebars = function(now)
    {
        var d = $.Deferred();

        var dur = now ? 0 : 'fast';
        var left = close_other_sidebars($('<div class="leftbar"></div>'), dur);
        var right = close_other_sidebars($('<div class="rightbar"></div>'), dur);
        var top = close_other_sidebars($('<div class="topbar"></div>'), dur);
        var bottom = close_other_sidebars($('<div class="botbar"></div>'), dur);

        $.when(left, right, top, bottom).done(function() { d.resolve(true); });
        return d.promise();
    };


    /**
     * show_all_overhang
     * @param {Boolean} show
     * this will hide (or show) all the overhanging sidebars/crossbars
     * it also restores the margins on the main page content area.
     */
    uib_sb.show_all_overhang = function(show)
    {
        var vis = show ? "visible" : "hidden";
        $(".oh").css("visibility", vis);

        //backup
        if(!$(".upage-content").attr("data-ohb")){ $(".upage-content").attr("data-ohb", $(".upage-content").attr("data-oh")); }


        var oh_str = "{'left':0, 'top':0, 'right':0, 'bottom':0}";
        if(show)
        {
            oh_str = $(".upage-content").attr("data-ohb");
        }
        $(".upage-content").attr("data-oh", oh_str);
        var data_oh = JSON.parse(dquote(oh_str));
        var keys = Object.keys(data_oh);
        keys.forEach(function(key)
        {
            $(".upage-content").css("margin-" + key, data_oh[key] + "px");
        });
    };




    //data-anim="{'style':'squeeze', 'v':200, 'side':'left', 'oh':35, 'dur':500, 'io':true}"
    function initialize_sidebars()
    {
        var data_oh = {};//{"left":0, "top":0, "right":0, "bottom":0};
        var initially_open = [];
        $(".uib_sidebar, .uib_crossbar").each(function()
        {
            var domNode_query = $(this);
            var anim = JSON.parse(dquote(domNode_query.attr("data-anim")));
            var oh = anim["oh"] || 0;
            var extant_name = (anim.side == "left" || anim.side == "right") ? "width" : "height";
            domNode_query.css(extant_name, anim.v + oh + "px");
            domNode_query.not(".reveal").css(anim.side, -anim.v + "px");
            domNode_query.find(".reveal").css(anim.side,"0px");
            //thumb
            var floatv = (anim.side == "left") ? "right" : "left";
            var floatc = (anim.side == "left") ?  "left" : "right";
            domNode_query.find(".sidebar-thumb").css(extant_name, oh + "px");
            domNode_query.find(".sidebar-content").css(extant_name, anim.v + "px");
            if(extant_name == "width")
            {
                domNode_query.find(".sidebar-thumb").css("float", floatv).css("height", "100%");
                if(domNode_query.find(".sidebar-thumb").length > 0){ domNode_query.find(".sidebar-content").css("float", floatc); }
            }else
            {
                domNode_query.find(".sidebar-thumb").css("clear", "both").css("width","100%");
                //domNode_query.find(".sidebar-content").css("clear", "both");
            }
            //data_oh should have largest overhang value.
            if(oh)
            {
                var cur_max = data_oh[anim.side];
                if(!cur_max || oh > cur_max) { data_oh[anim.side] = anim.side === 'top' ? 0 : oh; }
            }
            //initially open
            if(anim["io"]){ initially_open.push(domNode_query); }
        });

        $(".upage-content").attr("data-oh", JSON.stringify(data_oh));
        uib_sb.show_all_overhang(true);
        initially_open.forEach(function(domNode_query){ uib_sb.open_sidebar(domNode_query); });
    }



    function close_other_sidebars(domNode_query, dur)
    {
        var other_sidebars = null,
            arg_list = uib_sb.get_dispatch_arg_list(domNode_query),
            sidebar_classes = ["leftbar", "rightbar", "topbar", "botbar"];
        sidebar_classes.forEach(function(cname)
        {
            if(domNode_query.hasClass(cname)){  other_sidebars = $("." + cname).not(domNode_query); }
        });


        //animate functions don't return promises. Small hacky replacement.
        var counter = 0, deferred = $.Deferred();
        var completef = function(){ counter++; };
        arg_list.push(completef); arg_list.push(false); arg_list.push(dur);

        var idx_mod = domNode_query.hasClass("leftbar")||domNode_query.hasClass("rightbar") ? 0 : 1;
        other_sidebars.not(".reveal").css("z-index", 3); //down you go
        domNode_query.not(".reveal").css("z-index", 5 + idx_mod); //and up goes that one.
        other_sidebars.each(function(){ arg_list[0] = $(this); perform_open_close_sidebar.apply(null, arg_list); });

        if(counter == other_sidebars.length){ deferred.resolve(true); }
        else
        {
            var interval = setInterval(function(){ if(counter == other_sidebars.length){ deferred.resolve(true); clearInterval(interval); }}, 401);
        }
        return deferred.promise();
    }

    function dispatch_sidebar(domNode_query, f, additional_args)
    {
        var arg_list = uib_sb.get_dispatch_arg_list(domNode_query);

        if(arg_list.length > 0)
        {
            if(additional_args){ arg_list = arg_list.concat(additional_args); }
            f.apply(null, arg_list);
        }
    }

    uib_sb.get_dispatch_arg_list = function(domNode_query)
    {
        var arg_list = [];
        var data_oh = JSON.parse(dquote($(".upage-content").attr("data-oh")));
        var overhang = function(prop){ return data_oh[prop] || 0;  };
        if(domNode_query.hasClass("leftbar")) { arg_list = [domNode_query, "left",  ".upage-content", "margin-left",   overhang("left")]; }
        if(domNode_query.hasClass("rightbar")){ arg_list = [domNode_query, "right", ".upage-content", "margin-right",  overhang("right")]; }
        if(domNode_query.hasClass("topbar"))  { arg_list = [domNode_query, "top",     ".upage-outer", "margin-top",    overhang("top")]; }
        if(domNode_query.hasClass("botbar"))  { arg_list = [domNode_query, "bottom",  ".upage-outer", "margin-bottom", overhang("bottom")]; }
        return arg_list;
    };

    var resize_sidebar = function() {
        var $sidebar = $(this);
        var page_height = $sidebar.closest('.upage').height();
        $sidebar.height(page_height);
    };

    if (window.af) {
        $(window).on('resize', function(e) { $('.uib_sidebar').each(resize_sidebar); });
        $(function() { $('.uib_sidebar').each(resize_sidebar); });
    }

    // perform_toggle_sidebar(domNode_query, "left", ".upage-content", "margin-left", 0)
    // perform_toggle_sidebar(domNode_query, "right", ".upage-content", "margin-right", 0)
    // perform_toggle_sidebar(domNode_query, "top", ".upage-outer", "margin-top", 0)
    // perform_toggle_sidebar(domNode_query, "bottom", ".upage-outer", "margin-bottom", 0)
    function perform_toggle_sidebar(domNode_query, property, partner_selector, partner_property, partner_base, completef, dur)
    {
        var anim = JSON.parse(dquote(domNode_query.attr("data-anim")));
        if (dur !== undefined) { anim.dur = dur; }

        var visible_now = (domNode_query.css(property) == "0px"); //if prop is 0, then the sidebar is visible right now.
        var target = visible_now ? -anim.v : 0; // If sidebar visible, our target is negative v.
        var reveal = false;
        if(anim.style == "reveal")
        {
            reveal = true;
            visible_now = !($(partner_selector).css(partner_property) == partner_base + "px"); //if partner property is 0 then we are hidden.
            target = !visible_now ? anim.v : 0;
        }
        var force_page_fixed = (domNode_query.css("position") == "fixed");
        if(reveal && force_page_fixed){ $(".upage-content").css("position", "fixed"); }
        if(reveal && !visible_now){  domNode_query.css('visibility', 'visible'); }
        var finishf = completef;
        if(reveal && visible_now){ finishf = function()
        {
            domNode_query.css('visibility', 'hidden');
            if(force_page_fixed){ $(".upage-content").css("position", "relative"); }
            if(completef){ completef(); }
        };}
        else if(reveal && force_page_fixed){ finishf = function(){ $(".upage-content").css("position", "relative"); if(completef){ completef(); } }}

        uib_sb.move_sidebar(domNode_query, anim, target, finishf, property, partner_selector, partner_property, partner_base);
    }

    var get_max_height = function($elements) {
        if (!$elements.length) { return 0; }

        var heights = $elements.map(function(i, element) {
            return $(element).height();
        });

        // App Framework returns an App Framework object
        if (toString.call(heights[0]) === '[object Array]') {
            heights = heights[0];
        }

        return Math.max.apply(null, heights);
    };

    uib_sb.move_sidebar = function(domNode_query, anim, v, finishf, property, partner_selector, partner_property, partner_base)
    {
        if (domNode_query.is(':animated'))
        {
            if (finishf) { finishf(); }
            return;
        }

        var method = anim.style;
        var dur    = anim.dur || 400;
        var properties = {}; properties[property] = v; //{left:v}

        if (window.af) { domNode_query.filter('.uib_sidebar').each(resize_sidebar); }

        var final_f = function() {
            $(this).toggleClass('uib_bar_visible', v === 0);
            if (finishf) { return finishf.call(this); }
        };

        if(method == "push")
        {
            var $partners;
            var $children = $(partner_selector + " > *");
            var $headers = $children.filter('.uib-header, [data-role="header"]');
            var $fixed = $headers.filter('.uib-header-fixed, [data-position="fixed"]');
            var fixedHeight = get_max_height($fixed);
            var adjust = $headers.hasClass('ui-header') ? fixedHeight : 0;

            if (anim.side === 'top' && $headers.length && !$fixed.length) {
                $partners = $headers; // just push on the headers if they're not fixed
            } else {
                $partners = $children.not('.uib_crossbar, .uib_sidebar, .uib-header, [data-role="header"]');
            }

            var opening = !domNode_query.hasClass('uib_bar_visible');
            var $content = $children.filter('.upage-content');
            var xbar_top_start = parseInt(domNode_query.css('top'), 10);

            domNode_query.animate(properties, { duration: dur, complete: final_f, step: function(x) {
                $partners.css(partner_property, anim.v + x - adjust);

                if (anim.side === 'top' && $fixed.length) {
                    var progress = Math.abs(xbar_top_start - x);
                    var remaining = Math.abs(anim.v - progress);

                    var ttt = Math.max(fixedHeight - (opening ? progress : remaining), 0);

                    $content.css({ top: ttt });
                }
            }});
        }
        else if(method == "squeeze")
        {
            domNode_query.animate(properties, { duration:dur, complete:final_f, step:function(x){ $(partner_selector).css(partner_property, anim.v + x + partner_base);}});
        }
        else if(method == "reveal")
        {
            properties = {}; properties[partner_property] = v + partner_base; //{'margin-left':v}
            $(partner_selector).animate(properties, {duration:dur, complete:final_f});
        }
        else //assume overlap
        {
            domNode_query.animate(properties, { duration:dur, complete:final_f });
        }
    };


    function perform_open_close_sidebar(domNode_query, property, partner_selector, partner_property, partner_base, completef, open, dur)
    {
        var should_toggle =  (domNode_query.hasClass("reveal")) ? (domNode_query.css('visibility') == "visible") : (domNode_query.css(property) == "0px");
        if(open){ should_toggle = !should_toggle; }
        if(should_toggle){ perform_toggle_sidebar(domNode_query, property, partner_selector, partner_property, partner_base, completef, dur); }
        else if(completef){ completef(); }
    }

    function dquote(str)
    {
        return str.replace(/'/g, "\"");
    }

    function initialize_triggers()
    {
        $("[data-trigger]").each(function()
        {
            var this_query = $(this);
            var data_trigger = this_query.attr("data-trigger");
            var oc = data_trigger.search("sb-oc") != -1;
            var togl = data_trigger.search(/(sb-togl|sb-open|sb-close)/);
            if(oc)
            {
                var sidebar_id_list = data_trigger.match(/\/\S*/g); // "sb_oc/uib_w_1 sb_oc/uib_w_2" =>  ["/uib_w_1"  "/uib_w_2"]
                sidebar_id_list.forEach(function(str)
                {
                    var sidebar_query = $("." + str.substr(1));
                    if(sidebar_query.length > 0)
                    {
                        this_query.change(function()
                        {
                            if(this.value){ uib_sb.open_sidebar(sidebar_query);  }else{ uib_sb.close_sidebar(sidebar_query); }
                        });
                    }
                });

            }
            else if(togl !== -1)
            {
                var reg = new RegExp(/([^/]*)\/(\S*)/g);
                var match = reg.exec(data_trigger); // ["sb_togl/uib_w_1", "sb_togl", "uib_w_1"]
                var f_map = {"sb-togl":uib_sb.toggle_sidebar, "sb-open":uib_sb.open_sidebar, "sb-close":uib_sb.close_sidebar};
                var proceedf = function(match)               //while(match) --while is not usable because f gets changed
                {
                    var f = f_map[match[1].trim()];
                    var sidebar_query = $("." + match[2].trim());
                    if(sidebar_query.length > 0)
                    {
                        this_query.click(function(){ f(sidebar_query); });
                    }
                    match = reg.exec(data_trigger);
                    if(match){ proceedf(match); }
                };
                proceedf(match);
            }
        });
    }

    //INIT
    $(document).ready(initialize_sidebars);
    $(document).ready(initialize_triggers);

})(window.jQuery);