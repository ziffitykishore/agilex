define([
    'uiComponent',
    'jquery',
    'ko'
], function(Component,$,ko) {
    'use strict';

    var breadcrumbsData= ko.observableArray([]);
 
    return Component.extend({
        breadcrumbs: null,
        initialize: function () {
            this._super();

            var stopLoop = false;
            var showFirst = false;
            if (!document.referrer) {
                showFirst = true;
            }
            this.breadcrumbs.forEach(function(breadcrumbs) {
                if (stopLoop) {
                    return;
                }
                breadcrumbsData.destroyAll();
                breadcrumbs.forEach(function(item) {
                    var breadcrumb = [];
                    breadcrumb['name'] = item.name;
                    breadcrumb['url'] = item.url;
                    breadcrumbsData.push(breadcrumb);
                    if (document.referrer == item.url) {
                        stopLoop = true;
                    }
                });
                if (showFirst) {
                    return;
                }
            });

        },
        getBreadcrumbs: function () {
            return breadcrumbsData;
        }
    });
});
