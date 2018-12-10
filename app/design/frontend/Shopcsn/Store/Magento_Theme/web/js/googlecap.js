define([
    'jquery'
], function ($) {
    'use strict';
    
    $(document).ready(function(){
        window.recaptchaOnload = function(){
            var gCaptchaKey = window.gCaptchaKey || '';
            if(typeof grecaptcha !== 'undefined' && gCaptchaKey){
                setTimeout(function(){
                    $('.custom-g-recaptcha').each(function(index, val){
                        var element = $(this),
                            custInput = element.closest('.g-div').find('.custom-g-input');
                            custInput.attr('data-index', index);
                        grecaptcha.render(element.attr('id'),{
                            'sitekey' : gCaptchaKey,
                            'callback' : function(response){
                                custInput.val(response);
                            },
                            'expired-callback': function(){
                                custInput.val('');
                            },
                            'data-error-callback': function(){
                                custInput.val('');
                            }
                        });
                    });
                },200);
            }
        };
        require(['//www.google.com/recaptcha/api.js?onload=recaptchaOnload&render=explicit']);

        window.resetCaptcha = function(form){
            var custInput = form.find('.custom-g-input');
            custInput.val('');
            if(typeof grecaptcha !== 'undefined' && window.gCaptchaKey){
                grecaptcha.reset(custInput.data('index'));
            }
        };
    });
});