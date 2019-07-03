define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/storage',
    'mage/translate',
    'jquery/ui'
], function ($, modal,storage, $t) {
    'use strict';

    $.widget('ajaxlogin.customerAuthenticationPopup', {
        options: {
            login: '#customer-popup-login',
            loginLink: '#customer-popup-sign-in',
            register: '#customer-popup-register',
            registerLink: '#customer-popup-registration',
            forgot : '#customer-popup-forgot',
            forgotLink: '#customer-forgot-popup'
        },

        /**
         *
         * @private
         */
        _create: function () {
            var self = this,
                authentication_options = {
                    type: 'popup',
                    clickableOverlay: false,
                    modalClass: 'popup-modal',
                    responsive: true,
                    innerScroll: true,
                    title: this.options.popupTitle,
                    buttons: false
                };
            modal(authentication_options, this.element);

            $('body').on('click', '.signin-link, '+self.options.loginLink, function() {
                self._closePopup(self.options.register);
                self._closePopup(self.options.forgot);
                $(self.options.login).modal('openModal');
                self._setStyleCss();
                return false;
            });

            $('body').on('click', self.options.registerLink, function() {
                self._closePopup(self.options.login);
                $(self.options.register).modal('openModal');
                self._setStyleCss(self.options.innerWidth);
                return false;
            });
            
            $('body').on('click',self.options.forgotLink + ',a.forget-link', function() {
                self._closePopup(self.options.login);
                self._closePopup(self.options.register);
                $(self.options.forgot).modal('openModal');
                self._setStyleCss(self.options.innerWidth);
                return false;
            });
            
            $('body').on('click', '.wishlist-signin', function () {
                $(self.options.login).modal('openModal');
                var redirect = $(this).attr('params');
                redirect.replace(/\//g,'');
                var url = JSON.parse(redirect);
                $('#redirect-url').val(url.action + 'product/' + url.data['product'] + '/uenc/' + url.data['uenc'] + '/isAjax/true');
                self._setStyleCss();
            });
            
            $('.popup-modal').on('modalopened', function () {
                $('.popup-modal .mage-error').each(function() {
                    if ($(this).is('div')) {
                         $(this).remove();
                    }else{
                        $(this).removeClass('mage-error');
                    }
                });
            });
            
            this._ajaxSubmit();
            this._resetStyleCss();
        },

        /**
         * Set width of the popup
         * @private
         */
        _setStyleCss: function(width) {
            width = width || 400;
            if (window.innerWidth > 786) {
                this.element.parent().parent('.modal-inner-wrap').css({'max-width': width+'px'});
            }
        },

        /**
         * Reset width of the popup
         * @private
         */
        _resetStyleCss: function() {
            var self = this;
            $( window ).resize(function() {
                if (window.innerWidth <= 786) {
                    self.element.parent().parent('.modal-inner-wrap').css({'max-width': 'initial'});
                } else {
                    self._setStyleCss(self.options.innerWidth);
                }
            });
        },

        /**
         * Submit data by Ajax
         * @private
         */
        _ajaxSubmit: function() {
            var self = this,
                form = this.element.find('form'),
                inputElement = form.find('input');

            inputElement.keyup(function (e) {
                self.element.find('.messages').html('');
            });

            form.submit(function (e) {
                if (form.validation('isValid')) {
                    if (form.hasClass('form-create-account') || form.hasClass('form-forgot')) {
                        $.ajax({
                            url: $(e.target).attr('action'),
                            data: $(e.target).serializeArray(),
                            showLoader: true,
                            type: 'POST',
                            dataType: 'json',
                            success: function (response) {
                                self._showResponse(response, form.find('input[name="redirect_url"]').val());
                            },
                            error: function() {
                                self._showFailingMessage();
                            }
                        });
                    } else {
                        var submitData = {},
                        formDataArray = $(e.target).serializeArray();
                        formDataArray.forEach(function (entry) {
                            submitData[entry.name] = entry.value;
                        });
                        $('body').loader().loader('show');
                        storage.post(
                            $(e.target).attr('action'),
                            JSON.stringify(submitData)
                        ).done(function (response) {
                            $('body').loader().loader('hide');
                            self._showResponse(response, form.find('input[name="redirect_url"]').val());
                        }).fail(function () {
                            $('body').loader().loader('hide');
                            self._showFailingMessage();
                        });
                    }
                }
                return false;
            });
        },

        /**
         * Display messages on the screen
         * @private
         */
        _displayMessages: function(className, message) {
            $('<div class="message '+className+'"><div>'+message+'</div></div>').appendTo(this.element.find('.messages'));
        },

        /**
         * Showing response results
         * @private
         * @param {Object} response
         * @param {String} locationHref
         */
        _showResponse: function(response, locationHref) {
            var self = this,
                timeout = 800;
            this.element.find('.messages').html('');
            if (response.errors || response.error) {
                this._displayMessages('message-error error', response.message);
            } else {
                this._displayMessages('message-success success', response.message);
            }
            this.element.find('.messages .message').show();
            $('#customer-popup-register').scrollTop(0);
            if(typeof locationHref !== 'undefined') {
                 setTimeout(function() {
                if (!response.errors) {
                    self.element.modal('closeModal');
                    window.location.href = locationHref;
                }
            }, timeout);
            }
        },

        /**
         * Show the failing message
         * @private
         */
        _showFailingMessage: function() {
            this.element.find('.messages').html('');
            this._displayMessages('message-error error', $t('An error occurred, please try again later.'));
            this.element.find('.messages .message').show();
        },
        
        /**
         * Close the popup
         * @param {string} popup
         */
        _closePopup: function(popup) {
            if ($(popup).closest('aside').hasClass('_show')) {
                $(popup).modal('closeModal');
            }
        }
    });

    return $.ajaxlogin.customerAuthenticationPopup;
});
