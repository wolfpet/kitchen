/*
 *  Copyright (C) 2014 Intel Corporation. All rights reserved.
 */

/*
 * This script determines the correct height for widgets that must take the 'rest of height'
 *
 * Currently this only works with a single column laying using a upage-content as the inital
 * node and urow and the secondary node.
 *
 * It will go through all of the pages and determine how much height rows and widgets are
 * taking up and add this to the total height.  It will use these numbers to calculate the
 * correct spacing for all of Rest of Height widgets/rows.
 *
 * It will then iterate through each page and set the correct height by the number of widgets
 * on that page that need the 'rest of height'.
 *
 */

(function () {

    'use strict';
    window.restOfSpace = {};
    /**
     *  restOfSpace.js - Generic code for taking the 'rest of space' for one or more widgets.
     *
     *  @exports - calculateSpace()
     *
     */

    var pageData = [];

    $(document).ready(function () {
        init();
    });

    /**
     * Calls the first calculateSpace function and then creates a handler to trigger calculateSpace
     * on a window resize.
     */
    function init() {
        calculateSpace();
        $(window).on('resize', calculateSpace);
        $(document)
            .on('pagechange', calculateSpace)
            .on('servicesEvent', calculateSpace);
    }

    /**
     * Clears the pageData so that following calls will get an empty array.  It then finds each page
     * in the document and loops through them.  For each page it will find the rows or widgets in a row/subpage/page
     * and calculate the amount of space taken of by rest of height widget/rows.  It will use a upageData
     * object to keep track of information for that particular page.
     *
     */
    function calculateSpace() {
        //Clear page data
        pageData = [];
        //Find each page and interate through them
        var $upage = $(".upage:not(.hidden) .upage-content:not(.hidden), .inner-element .sidebar-content, .outer-element .sidebar-content");
        //Check whether upage is active and doesn't have upage-content (no subpage)
        $upage = $upage.add( $(".upage:not(.hidden)").filter(function(key, item){ return $(item).find('.upage-content').length == 0; }));
        $upage.each(function (key, item) {
            var $page = $(item);
            var ROHWidgetHeight = 0;

            //Page data object
            var upageData = {
                'page': item,
                'count': 0,
                'availableSpace': 0
            };

            ROHWidgetHeight = calculatePageSpace(item, upageData);
            $upage.siblings('.uib-header, .uib-footer').each(function(key, item){ ROHWidgetHeight -= $(item).height(); });
            var pageHeight = $page.children().toArray().reduce(function(prev, curr) { return prev + $(curr).outerHeight(true); }, 0);
            var baseHeight = $page.parent().is('.outer-element') ? $page.parent().outerHeight(true) : $(window).height();
            upageData.availableSpace = baseHeight - (pageHeight - ROHWidgetHeight);
            pageData.push(upageData);
        });

        setRestOfHeight();
    }
    restOfSpace.calculateSpace = calculateSpace;

    /*
     * Calculates the page space by looking at all of the rows and determining whether it is a ROH
     * widget.  If it is it will add the row height to the total Rest of Height value.  Otherwise,
     * it will look at the widgets in the row and check whether they have the ROH class.  If they do,
     * it will be added to the Rest of Height value.  After these are checked it will return the total
     * height value for all Rest of Height rows or widgets.
     *
     * @param {Object} item - A urow page node
     * @param {Object} pageData - An object that contains all of the information needed for a page
     * @returns {Number} - The total height used by non 'rest of height' widgets
     */
    function calculatePageSpace(item, pageData) {
        var ROHWidgetHeight = 0;
        var $containers = $(item).find('.rest-of-height').filter(function(key, item){
          return $(item).parents('.rest-of-height').length == 0;
        })
        $containers.each(function(key, item){
            ROHWidgetHeight += $(item).height();
            pageData.count += 1;
        });

        return ROHWidgetHeight;
    }

    /*
     * Sets the 'rest of height' for each of the pages.  This will itereate through the local
     * variable pageData and will use the information in there to set the height of 'rest of
     * height' widgets.  It First finds the 'rest of height' widgets and then gets the total
     * available space and total number of widgets.  It divides those numbers to get a equal
     * value to set for all of the widgets.  If the value is less than 50 it will instead be
     * set to 50 since anything smaller might be un-useable. It will also set the overflow-x
     * of the widget to auto incase in needs to scroll.  We will also special case values for
     * textareas and check whether they have any margin/padding from a possible label.
     */
    function setRestOfHeight() {
        pageData.forEach(function (item, key) {
            var $page = $(item.page);
            //Find all rows that are rest of height
            var $restOfHeightNodes = $page.find('.rest-of-height').filter(function(key, item){
              return $(item).parents('.rest-of-height').length == 0;
            });
            var heightToSet = item.availableSpace / item.count;
            var toSet = heightToSet < 50 ? 50 : heightToSet;
            $restOfHeightNodes.each(function (key, node) {
                var $node = $(node);
                var $textarea = $node.find('textarea');
                if($textarea.length !== 0){
                    var toMinus = parseInt($(node).css('margin-top')) + parseInt($(node).css('margin-bottom'));
                    var $label = $textarea.siblings('label');
                    if($label.hasClass('label-top-left') || $label.hasClass('label-top-right')) {
                        toMinus += ($label.height() +
                                    parseInt($label.css('margin-top')) +
                                    parseInt($label.css('margin-bottom')) +
                                    parseInt($label.css('padding-top')) +
                                    parseInt($label.css('padding-bottom')))
                    }
                    $node.find('textarea')
                        .css('height', toSet - toMinus);
                }
                $node.height(toSet)
                    .css('overflow-x', 'auto');
            });
        })
    }

})();