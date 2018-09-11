define([
    'jquery'
], function ($) {
    'use strict';
    $(document).ready(function(){
        var gCaptchaKey = window.gCaptchaKey || '';
        if(grecaptcha !== 'undefined' && gCaptchaKey){
            $('.custom-g-recaptcha').each(function(){
                var element = $(this);
                grecaptcha.render(element.attr('id'),{
                    'sitekey' : gCaptchaKey,
                    'callback' : function(response){
                        element.closest('.g-div').find('.custom-g-input').val(response);
                    }
                });
            });
        }
    });    
});