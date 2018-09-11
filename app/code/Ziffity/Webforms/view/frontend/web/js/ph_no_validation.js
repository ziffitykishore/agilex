define([
   'jquery',
   'jquery/ui',
   'jquery/validate',
   'mage/translate'
], function($){
   'use strict';
   return function() {
       $.validator.addMethod(
           "ph_no_validation",
           function(value) {
                return value.length > 9 &&  value.length < 17 && value.match(/^[\d{3}]?[\d{1}]?[\d{1}]?[-\. ]?\(?(\d{3})\)?[-\. ]?(\d{3})[-\. ]?(\d{4})$/);
               //return false;
           },
           $.mage.__("Please enter a valid phone number. For example (123) 456-7890 or 123-456-7890.")
       );
   };
});