/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'porthole',
        'simplewebpay',
        'viewprocess',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Checkout/js/action/redirect-on-success',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/model/error-processor',
        'mage/url'
    ],
    function ($, porthole, simplewebpay,viewprocess,
        quote,
        Component,
        additionalValidators,
        redirectOnSuccessAction,
        fullScreenLoader,
        errorProcessor,
        urlBuilder
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Cenpos_SimpleWebpay/payment/swppayment',
                webpaytokenid: ''
            },
            initObservable: function () {
                this._super()
                    .observe([
                        'webpaytokenid'
                    ]);
                return this;
            },
            /** Returns send check to info */

            getMailingAddress: function () {
                return window.checkoutConfig.payment.checkmo.mailingAddress;
            },

            getSimpleForm: function () {
                $("#NewCenposPlugin").html("<div></div>");
                $('.payment-method-result-webpay').html("");
                $("#NewCenposPlugin").show();
                var isToken = window.checkoutConfig.payment.swppayment.usetoken == "true";

                try{
                    debugger;
                    $.ajax({
                        type: "POST",
                        url: window.checkoutConfig.payment.swppayment.urlsession,
                        data: msg,
                        beforeSend: function () {
                            $(".payment-method-result-webpay").append("<div id='loadersavecard' style='background-color: rgba(255,255,255,0.5);width:100%;position: relative;z-index: 100;top: 0;height: 130px;margin-top: -120px;'><img style='display: block;margin: 28px 0 0 71px;float: left;' src='" + window.checkoutConfig.payment.swppayment.urlimage + "' /></div>");
                        },
                        success: function (msg) {
                            msg = $.parseJSON(msg);
                            if (msg.Result === 0) {
                                var params = "";
                                params += "verifyingpost=" + encodeURIComponent(msg.Data);
                                //        params += "&address=9048";
                                //       params += "&zipcode=33189";
                                params += "&isemail=true";
                                params += "&iscvv="+window.checkoutConfig.payment.swppayment.iscvv;
                                if(window.checkoutConfig.payment.swppayment.istoken19 === "true") params += "&type=createtoken19";
                                if (quote.guestEmail !== "" && quote.guestEmail !== null && quote.guestEmail !== undefined) {
                                    params += "&email=" + quote.guestEmail;
                                    isToken = false;
                                }
                                
                                params += "&onlyform="+((isToken) ? "false" : "true");
                                    
                                $("#NewCenposPlugin > div").createWebpay(
                                    {
                                        url: window.checkoutConfig.payment.swppayment.url,
                                        params: params,
                                        width: "500",
                                        height: "340",
                                        sessionToken: true,
                                        success: function (msg) {
                                            if (msg !== "Error") {
                                                $("#NewCenposPlugin").hide();
                                                if (typeof (msg) !== "object") msg = $.parseJSON(msg);
                                                if (msg.RecurringSaleTokenId !== null && msg.RecurringSaleTokenId !== "" && msg.RecurringSaleTokenId !== undefined)
                                                    $("#FormWebpay").html("");
                                                for (var indice in msg) {
                                                    if (indice.toLowerCase() === "recurringsaletokenid") {
                                                        if (msg[indice].indexOf("CRYPTO") < 0  && isToken) isToken = false;
                                                    }
                                                    if (indice.toLowerCase() === "cardtype") {
                                                        $(".payment-method-result-webpay").append("<strong>Card Type: </strong>" + msg[indice] + "<br />");
                                                    }
                                                    if (indice.toLowerCase() === "protectedcardnumber") {
                                                        $(".payment-method-result-webpay").append("<strong>Card Number: </strong>" + msg[indice] + "<br />");
                                                    }
                                                    if (indice.toLowerCase() === "cardexpirationdate") {
                                                        $(".payment-method-result-webpay").append("<strong>Expiration: </strong>" + msg[indice] + "<br />");
                                                    }
                                                    $("#FormWebpay").append('<input type="hidden" name="payment[webpay' + indice.toLowerCase() + ']" value="' + msg[indice] + '" />')
                                                    this[indice.toLowerCase()] = msg[indice];
                                                }
                
                                                if (isToken) {
                                                    $(".payment-method-result-webpay").append("<a id='SendTokenClick' style='display:block; cursor: pointer'>Save card</a>");
                                                    $("#SendTokenClick").click(function () {
                                                        $.ajax({
                                                            type: "POST",
                                                            url: window.checkoutConfig.payment.swppayment.urlsave,
                                                            data: msg,
                                                            beforeSend: function () {
                                                                $(".payment-method-result-webpay").append("<div id='loadersavecard' style='background-color: rgba(255,255,255,0.5);width:100%;position: relative;z-index: 100;top: 0;height: 130px;margin-top: -120px;'><img style='display: block;margin: 28px 0 0 71px;float: left;' src='" + window.checkoutConfig.payment.swppayment.urlimage + "' /></div>");
                                                            },
                                                            success: function (msg) {
                                                                $("#loadersavecard").remove();
                                                                msg = $.parseJSON(msg);
                                                                if (msg.Result === 0) {
                                                                    $("#SendTokenClick").hide();
                                                                } else {
                                                                    alert(msg.Message);
                                                                }
                                                            }
                                                        });
                                                    });
                                                }
                                                $("#SubmitWebpay").hide();
                                                $("#SubmitWebpaySend").show();
                                            }
                                        },
                                        cancel: function (msg) {
                                            alert(msg.Message);
                                        }
                                });
                                
                               
                
                                $("#SubmitWebpay").on('click', function () {
                                    // if ($("input[name=termnconditions]").prop('checked')) {
                                    //$("#NewCenposPlugin > div").sendAction("selectedget"); 
                                    //$("#NewCenposPlugin > div").sendAction("submitadd");
                                    $("#NewCenposPlugin > div").submitAction();
                                    // }else alert("Please agree to our terms of use and privacy policy")
                                });
                
                                $("#cenposPayIFrameId").attr("style", "border: none !important;margin-top: 0px;");
                            } else {
                                alert(msg.Message);
                            }
                        }
                    });
                }catch(error)
                {
                     trace(error.message);
                     alert(error.message);
                }

                

               return window.checkoutConfig.payment.swppayment.dataConfig;
            },

            getCode: function () {
                return 'swppayment';
            },
            getData: function () {
                var additional_data = {};
                $("#FormWebpay").children().each(function () {
                    additional_data[$(this).attr("name")] = $(this).val();
                });

                return {
                    'method': this.item.method,
                    'additional_data': additional_data
                };
            },
            getEmail: function () {
                return quote.guestEmail;
            },
            afterPlaceOrder: function (data, event) {
            },
            placeOrder: function (data, event) {
                var self = this;
                var msgtemp = {};
                var eventtemp = {};
                if (event) {
                    event.preventDefault();
                }
                $("#CardinalResponse").off("change");
                $("#CardinalResponse").on('change', function () {
                    var Value = $(this).val();
                    if (Value !== "") {
                        var resposems = JSON.parse(Value);
                        $("#Form3dSecure").hide();
                        if (resposems.Result !== 0) {
                            self.isPlaceOrderActionAllowed(true);
                            eventtemp.responseText = msgtemp.Message;
                            errorProcessor.process(eventtemp);
                            fullScreenLoader.stopLoader();
                        } else {
                            $.ajax({
                                type: "POST",
                                url: window.checkoutConfig.payment.swppayment.url3d,
                                data: resposems,
                                beforeSend: function () {
                                    self.isPlaceOrderActionAllowed(false);
                                    fullScreenLoader.startLoader();
                                },
                                success: function (msg) {
                                    msg = $.parseJSON(msg);
                                    if (msg.Result === "0") {
                                        $("#SubmitWebpaySend").trigger("click");
                                       // var url = urlBuilder.build("customer/account");
                                       // window.location.href = url;
                                    } else {
                                            $("#SubmitWebpaySend").trigger("click");
                                            self.isPlaceOrderActionAllowed(true);
                                            fullScreenLoader.stopLoader();
                                            msg.message = msg.Message;
                                            event.responseText = JSON.stringify(msg);
                                            errorProcessor.process(event);
                                    }
                                }
                            });
                        }
                    }
                });
                if (this.validate() && additionalValidators.validate()) {
                    this.isPlaceOrderActionAllowed(false);
                    this.getPlaceOrderDeferredObject()
                        .fail(
                            function (msg, data, data2) {
                                self.isPlaceOrderActionAllowed(true);
                            }
                        ).done(
                            function (msg, message, event) {
                                msgtemp = msg;
                                eventtemp = event;
                                if (Number.isInteger(Number.parseInt(msg))) {
                                    msg = { Result: 0, Message: "Approval" }
                                } else{
                                    if(msg.indexOf("{") == 0){
                                        var msgsplit = msg.split("}");
                                        var msgcompu = "";
                                        for(var n in msgsplit){
                                            if(msgsplit[n].indexOf("View3D") > 0 || n==0 ){
                                                msgcompu = msgsplit[n] + "}";
                                                break;
                                            }
                                        }
                                        try{
                                            msg = $.parseJSON(msgcompu);
                                        }catch(error)
                                        {
                                            msg =  msg.slice(0,msg.indexOf("['returnCardinalMag'](messageEvent.data)}});")+"['returnCardinalMag'](messageEvent.data)}}); </script>'a}".length);
                                            msg = $.parseJSON(msg);
                                        }
                                    }else{
                                        var  resp = {};
                                        resp.Message = msg;
                                        resp.Result = -1;
                                        msg = resp;
                                    }
                                } 
                                if (msg.Result === 0) {
                                    self.afterPlaceOrder();
                                    if (self.redirectAfterPlaceOrder) {
                                        redirectOnSuccessAction.execute();
                                    }
                                } else if (msg.Result === 21) {
                                    fullScreenLoader.stopLoader();
                                   // msg.View3D = msg.View3D.replace("<script>", " < script > ");
                                    //  msg.View3D = msg.View3D.replace("function(messageEvent){","function(messageEvent){ var respose = JSON.parse(messageEvent.data); ");
                                    msg.View3D = msg.View3D.replace("function(messageEvent){", "function(messageEvent){ document.getElementById('CardinalResponse').value = messageEvent.data; document.getElementById('CardinalResponse').dispatchEvent(new Event('change')); ");
                                    msg.View3D = msg.View3D.replace("window['returnCardinalMag'](messageEvent.data)", "");
                                    msg.View3D = msg.View3D.replace("framecenpos'  width='100%'", "framecenpos' width='100%' height='400'");
                                    $("#Form3dSecure").show();
                                    $("#Form3dSecure").html("<div>" + msg.View3D + "</div>");
                                } else {
                                    self.isPlaceOrderActionAllowed(true);
                                    msg.message = msg.Message;
                                    event.responseText = JSON.stringify(msg);
                                    errorProcessor.process(event);
                                    fullScreenLoader.stopLoader();
                                    //alert(msg.Message);
                                }
                            }
                        );

                    return true;
                }

                return false;
            }

        });
    }
);