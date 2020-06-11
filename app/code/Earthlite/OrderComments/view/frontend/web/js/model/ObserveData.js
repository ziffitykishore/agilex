define(
    [
        'jquery',
        'ko',
    ], function ($, ko) {
        "use strict";
        
        var self = this;
        var orderComments = {
            /**
             * List of  items
             */
           comment: ko.observable(),           
            /**
             * Constructor
             * @returns {Items}
             */
            initialize: function () {
                self = this;
                this.comment();                
                return self;
            },

            setComment: function(val) {
                this.comment(val);
            },

            getComment: function() {
                return this.comment();
            }

           
        };
       
        return orderComments.initialize();
    }
);