/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_QuickOrder
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
define([
    'jquery',
    'ko',
    'uiComponent',
    'Mageplaza_QuickOrder/js/model/qod_item',
    'mage/translate'
], function ($, ko, Component, itemModel, $t) {
    'use strict';
    var self;
    return Component.extend({
        defaults: {
            template: 'Mageplaza_QuickOrder/add_multiple'
        },
        csvcontent: [],
        items: itemModel.items,

        /**
         * init function
         */
        initialize: function () {
            this._super();
            self = this;
        },

        /**
         * get Item
         */
        getItems: function () {
            return this.items;
        },

        /**
         * get value input bulk add
         */
        getValueInputBulkAdd: function () {
            var lines = $('#addmultipleskus').val().split(/\n/);
            var texts = [];
            for (var i = 0; i < lines.length; i++) {
                if (/\S/.test(lines[i])) {
                    texts.push($.trim(lines[i]));
                }
            }
            return texts;
        },

        /**
         * get value input and read csv file
         */
        getValueInputCsvFile: function () {
            $("#qodupcsv").change(function (e) {
                var csvcontent = [];
                var ext = $("input#qodupcsv").val().split(".").pop().toLowerCase();
                if ($.inArray(ext, ["csv"]) == -1) {
                    alert('Upload CSV');
                    return false;
                }
                if (e.target.files != undefined) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        var csvval = e.target.result.split("\n");
                        for (var i = 0; i < csvval.length; i++) {
                            var temp = csvval[i];
                            csvcontent.push($.trim(temp));
                        }
                        self.csvcontent = csvcontent;
                    };
                    reader.readAsText(e.target.files[0]);
                }
            });
        },

        /**
         * bulk add item to list
         */
        bulkAddItemToList: function () {
            var value = this.getValueInputBulkAdd(),
                url = window.qodConfig.buildItemUrl,
                el_addmultiplesku = $('#addmultipleskus'),
                el_bulkmessage = $('#bulkadd-message'),
                el_bulkcomplete = $('#bulkadd-complete'),
                merge_data = '',
                error_message = $('#error-message');

            var csvfile = this.csvcontent,
                el_addCsvfile = $('#qodupcsv');

            if (value.length == 0 || value == '') {
                self.showMessage(el_bulkmessage, 5000);
            } else {
                /** merge data bulkadd and csv file*/
                if (csvfile.length > 0) {
                    merge_data = csvfile.concat(value);
                } else {
                    merge_data = value;
                }
                el_bulkmessage.hide();
                $.ajax({
                    url: url,
                    data: {
                        value: merge_data
                    },
                    method: 'POST',
                    success: function (response) {
                        if (response.errors === false) {
                            error_message.show();
                            self.showMessage(error_message, 5000);
                        } else if (response != '') {
                            for (var key in response) {
                                if (!response.hasOwnProperty(key)) continue;
                                var obj = response[key];
                                var checkExistItem = itemModel.getItemExisted(obj.type_id, obj.sku),
                                    hasExist = false;
                                if (checkExistItem) {
                                    hasExist = true;
                                }
                                if (!checkExistItem) {
                                    itemModel.addItem(obj);
                                }
                            }
                            el_addmultiplesku.val('');
                            el_addCsvfile.val('');
                            self.csvcontent = '';
                            if (hasExist) {
                                self.showMessage(el_bulkmessage, 5000);
                            } else {
                                self.showMessage(el_bulkcomplete, 5000);
                            }
                        } else {
                            self.showMessage(el_bulkmessage, 5000);
                        }
                    }
                });
            }
        },

        /**
         * add csv item to list
         */
        addCsvItemToList: function () {
            var csvfile = this.csvcontent,
                url = window.qodConfig.buildItemUrl,
                el_addCsvfile = $('#qodupcsv'),
                el_filemessage = $('#file-message'),
                el_filecomplete = $('#addcsv-complete'),
                error_message = $('#error-message');
            var itemsInList = self.items();
            csvfile.splice(csvfile.length - 1, 1);
            csvfile.splice(0, 1);
            if (csvfile.length == 0 || csvfile == '') {
                self.showMessage(el_filemessage, 5000);
            } else {
                el_filemessage.hide();
                $.ajax({
                    url: url,
                    data: {
                        value: csvfile
                    },
                    method: 'POST',
                    success: function (response) {
                        if (response.errors === false) {
                            error_message.show();
                            self.showMessage(error_message, 5000);
                        } else if (response != '') {
                            for (var key in response) {
                                if (!response.hasOwnProperty(key)) continue;
                                var obj = response[key];
                                var checkExistItem = itemModel.getItemExisted(obj.type_id, obj.sku),
                                    hasExist = false;
                                console.log(checkExistItem);
                                if (checkExistItem == true) {
                                    hasExist = true;
                                }
                                if (!checkExistItem) {
                                    itemModel.addItem(obj);
                                }
                            }
                            el_addCsvfile.val('');
                            self.csvcontent = '';
                            if (hasExist) {
                                self.showMessage(el_filemessage, 5000);
                            } else {
                                self.showMessage(el_filecomplete, 5000);
                            }
                        } else {
                            self.showMessage(el_filemessage, 5000);
                        }
                    }
                });
            }
        },

        /**
         * remove all item
         */
        clearAllItems: function () {
            itemModel.clearAllItems();
        },

        /**
         * show message
         */
        showMessage: function (el, timedelay) {
            el.show();
            if (timedelay <= 0) timedelay = 5000;
            setTimeout(function () {
                el.hide();
            }, timedelay);
        }
    })
});
