define([
    "jquery",
    "infiniteAjaxScroll",
    "catalogAddToCart"
], function($) {
    "use strict";
    window.CustomIAScroll = {
        debug: window.iasConfig.debug,
        _log: function(object) {
            if(this.debug) {
                console.log(object);
            }
        },
        init: function(){
            jQuery(function($) {
                // get system config:
                var config = {
                    item: window.iasConfig.mode,
                    container : window.iasConfig.container,
                    next: window.iasConfig.next,
                    pagination: window.iasConfig.pagination,
                    delay: 600,
                    negativeMargin: window.iasConfig.buffer,
                    history: {
                        prev: window.iasConfig.prev
                    },
                    noneleft: {
                        text: window.iasConfig.text,
                        html: window.iasConfig.html
                    },
                    spinner: {
                        src: window.iasConfig.spinnerSrc,
                        html: window.iasConfig.spinnerHtml
                    },
                    trigger: {
                        text: window.iasConfig.trigger.text,
                        html: window.iasConfig.trigger.html,
                        textPrev: window.iasConfig.trigger.textPrev,
                        htmlPrev: window.iasConfig.trigger.htmlPrev,
                        offset: window.iasConfig.trigger.offset
                    }
                };
                // check for custom configurations:
                if (window.iasConfigCustom){
                    $.extend(config, window.iasConfigCustom);
                }
                // logs:
                CustomIAScroll._log({extension: 'ias', config: config});
                window.ias = $.ias(config);
                CustomIAScroll._log({extension: 'paging'});
                window.ias.extension(new IASPagingExtension());
                CustomIAScroll._log({extension: 'spinner'});
                window.ias.extension(new IASSpinnerExtension(config.spinner));
                CustomIAScroll._log({extension: 'noneleft'});
                window.ias.extension(new IASNoneLeftExtension(config.noneleft));
                CustomIAScroll._log({extension: 'trigger'});
                window.ias.extension(new IASTriggerExtension(config.trigger));
                if(window.iasConfig.memoryActive){
                    CustomIAScroll._log({extension: 'history'});
                    window.ias.extension(new IASHistoryExtension(config.history));
                }
                // debug events
                window.ias.on('scroll', function(scrollOffset, scrollThreshold){
                    CustomIAScroll._log({eventName: 'scroll', scrollOffset: scrollOffset, scrollThreshold: scrollThreshold});
                });
                window.ias.on('load', function(event){
                    if (event.ajaxOptions) {
                        event.ajaxOptions.cache = true;
                    }
                    CustomIAScroll._log({eventName:'load', event: event});
                });
                window.ias.on('loaded', function(data, items){
                    CustomIAScroll._log({eventName: 'loaded', data: data, items: items});
                });
                window.ias.on('render', function(items){
                    CustomIAScroll._log({eventName: 'render', items: items});
                });
                window.ias.on('rendered', function(items){
                    CustomIAScroll._log({eventName: 'render', items: items});
                    $('body').trigger('contentUpdated');
                });
                window.ias.on('noneLeft', function(){
                    CustomIAScroll._log({eventName: 'noneLeft'});
                });
                window.ias.on('next', function(url){
                    CustomIAScroll._log({eventName: 'next', url: url});
                });
                window.ias.on('ready', function(){
                    CustomIAScroll._log({eventName: 'ready'});
                });
                if(window.iasConfig.toolbarAction == 'show') {
                    $(window.iasConfig.toolbarSelector).show();
                } else {
                    $(window.iasConfig.toolbarSelector).hide();
                }
                // custom fix for url protocol issue in jquery ias library:
                window.ias.getNextUrl = function(container) {
                    if (!container) {
                        container = window.ias.$container;
                    }
                    // always take the last matching item + fix to be protocol relative
                    var nexturl = $(window.ias.nextSelector, container).last().attr('href');
                    if(typeof nexturl !== "undefined") {
                        if (window.location.protocol == 'https:') {
                            nexturl = nexturl.replace('http:', window.location.protocol);
                        } else {
                            nexturl = nexturl.replace('https:', window.location.protocol);
                        }
                    }
                    return nexturl;
                };
                // custom infinitescroll done event:
                CustomIAScroll._log('Done loading IAS.');
                $(document).trigger( "infiniteScrollReady", [window.ias]);
            });
        }
    };
});
