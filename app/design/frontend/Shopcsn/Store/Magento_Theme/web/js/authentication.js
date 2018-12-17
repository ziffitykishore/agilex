/* 
 * 
 * authentication.js
 * its handling login, register, forget password popup related works.
 * 
 */

define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'googlecapjs',
    'mage/translate'
], function ($, modal, googlecapjs, $t) {
    'use strict';

    return function(config){
        var authOptions = {
            isCheckoutPage: (window.location.pathname == "/onestepcheckout/"),
            popupModalId: $('#popup-modal'),
            loginNotify: $('.login-notify'),
            pageReload: function(response){
                if(this.isCheckoutPage && !response.errors){
                    window.location.reload();
                    return;
                }
            },
            closeModal: function(){
                /*to close previous open modals && error messages*/
                $('._show[data-role="modal"]').find('.action-close').trigger( "click" );
                $('input.mage-error, label.mage-error').removeClass('mage-error');
                $('.signup-notify, .login-notify, .forget-notify, .mage-error').hide();
            },
            clearForm: function(form){
                form.find(':input')
                .not(':button, :submit, :reset, :hidden')
                .val('').closest('.field').removeClass('focused');  
            },
            resetForm: function(form){
                form.find(':input')
                .not(':button, :submit, :reset, :hidden, :password')
                .closest('.field').addClass('focused');
                form.find(':password').val('');
            }
        },
        options = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            modalClass: 'custom-block-customer-login',
            buttons: false
        };

        $(document).on("click", '#login-popup, #login-popup-form, .signin-popup', function(e){
            authOptions.closeModal();
            if(window.isCustomerLoggedIn != 'undefined' && parseInt(window.isCustomerLoggedIn)){
                return;
            }
            e.preventDefault();
            var popup = modal(options, authOptions.popupModalId);
            authOptions.clearForm(authOptions.popupModalId);
            authOptions.popupModalId.modal('openModal').on('modalclosed', function(){
            authOptions.loginNotify.hide().html('');
            });
            if(authOptions.isCheckoutPage){
                authOptions.popupModalId.closest('.modal-inner-wrap').addClass('fullwidth-popup');
            }
        });

        var optsignup = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            modalClass: 'custom-block-customer-signup',
            buttons: false
        };
        $('#signup-modal').on("click", function(){
            authOptions.closeModal();
            var ppModal = $('#popup-modal-signup');
            var popup = modal(optsignup, ppModal);
            var meter = ppModal.find('#strength-meter');
            authOptions.clearForm(ppModal);
            meter.attr('class', '');
            meter.find('[data-role="password-strength-meter-label"]').text($t('No Password'));
            ppModal.modal('openModal').on('modalclosed', function() { 
                $('#popup-modal-signup div.mage-error').remove();
            });
            if(authOptions.isCheckoutPage){
                ppModal.closest('.modal-inner-wrap').addClass('fullwidth-popup');
            }
        });

        var optforget = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            modalClass: 'customer-forgetpassword-popup',
            buttons: false
        };
        $('#forget-pwd, .forget-pwd').on("click", function(){
            authOptions.closeModal();
            var ppModal = $('#forget-popup');
            var popup = modal(optforget, ppModal);
            ppModal.modal('openModal');
            if(authOptions.isCheckoutPage){
                ppModal.closest('.modal-inner-wrap').addClass('fullwidth-popup');
            }
        });
        $('.bk-to-arw').on('click', function(){
            $('#forget-popup').modal('closeModal');
            authOptions.popupModalId.modal('openModal');
        });

        var forgetPasswordForm = $('#forget-password');
        forgetPasswordForm.on('submit', function(event){
            event.preventDefault();
            if(forgetPasswordForm.validation('isValid')) {
                $.ajax({
                    url: config.ajaxForgetPasswordUrl,
                    type:'POST',
                    showLoader: true,
                    dataType:'json',
                    data: forgetPasswordForm.serialize(true),
                    success:function(response) {
                        if(response.success) {
                            $('#forget-popup').modal('closeModal');
                            authOptions.loginNotify.fadeOut().html(response.message).fadeIn(100);
                            var popup = modal(options, authOptions.popupModalId);
                            authOptions.popupModalId.modal('openModal');
                        } else {
                            $('.forget-notify').fadeOut().html(response.message).fadeIn(100);
                        }
                        authOptions.clearForm(forgetPasswordForm);
                    }
                });
            }
        });

        /*ajaxlogin*/
        var loginForm = $('#login-form-popup');
        loginForm.on('submit', function (event){
            if(loginForm.validation('isValid')) {
                event.preventDefault();			
                $.ajax({
                    url: config.ajaxLoginUrl,
                    type: 'POST',
                    showLoader: true,
                    dataType: 'json',
                    data: loginForm.serialize(true),
                    success: function(response) {
                        authOptions.loginNotify.fadeOut().html(response.message).fadeIn(100);
                        $('.field').removeClass('focused');
                        authOptions.pageReload(response);
                        if(!response.errors && !authOptions.isCheckoutPage) {
                            window.location = config.accountUrl;
                        }
                        /*reset captcha*/
                        window.resetCaptcha(loginForm);
                        authOptions.resetForm(loginForm);
                    }
                });
            }
        });

        //form-validate-popup
        var regForm = $('#signup-popup');
        regForm.on('submit', function (event){
            if($('#signup-check:checked').length < 1) {
                $('.signupcheck').addClass('mage-error').show();
            } else {
                $('.signupcheck').removeClass('mage-error').show();
            }
            event.preventDefault();

            if(regForm.validation('isValid')) {
                $.ajax({
                    url: config.ajaxRegisterUrl,
                    type: 'POST',
                    showLoader: true,
                    dataType: 'json',
                    data: regForm.serialize(true),	
                    success:function(response) {

                        /*reset captcha*/
                        window.resetCaptcha(regForm);

                        if(response.success) {
                            $('#popup-modal-signup').modal('closeModal');
                            var popup = modal(optsignup, $('#popup-modal-signup'));
                            if(authOptions.popupModalId.closest('.modal-inner-wrap').length){
                                authOptions.popupModalId.modal('openModal');
                            }
                            authOptions.loginNotify.fadeOut().html(response.message).fadeIn(100);
                        } else {
                            $('.signup-notify').fadeOut().html(response.message).fadeIn(100);
                            $('.field').removeClass('focused');
                        }

                        if(response.email) {
                            $('#confirm_link').on('click', function(event){
                                event.preventDefault();
                                var optconf = {
                                    type: 'popup',
                                    responsive: true,
                                    innerScroll: true,
                                    modalClass: 'confirm-popup custom-block-customer-login',
                                    buttons: false
                                };
                                $('#email_address_confirm').val(response.email);
                                authOptions.popupModalId.modal('closeModal');
                                var popup = modal(optconf, $('#confirm-popup'));
                                $('#confirm-popup').modal('openModal');
                            });
                        }
                        authOptions.resetForm(regForm);
                    }
                });
            }
        });

        /*email reconfirmation form*/
        var reCForm = $('#confirm-popup-form');
        reCForm.on('submit', function (event){
            event.preventDefault();
            if(reCForm.validation('isValid')) {
                $.ajax({
                    url: config.ajaxReconfirmUrl,
                    type: 'POST',
                    showLoader: true,
                    dataType:'json',
                    data: reCForm.serialize(true),
                    success:function(response) {
                        if(response.errors) {
                            $('.confirm-notify').fadeOut().html(response.message).fadeIn(100);
                            $('.field').removeClass('focused');
                        } else {
                            $('#confirm-popup').modal('closeModal');
                            authOptions.popupModalId.modal('openModal');
                            authOptions.loginNotify.fadeOut().html(response.message).fadeIn(100);
                        }
                        authOptions.clearForm(reCForm);
                    }
                });
            }
        });
    };
});