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
define(
    [
        'jquery',
        'ko',
        'Mageplaza_QuickOrder/js/model/resource/item',
        'Magento_Catalog/js/price-utils'
    ],
    function ($, ko, Item, priceUtils) {
        "use strict";
        var self = this;
        /**
         * Items object to manage quote items
         * @type {{items: *, initialize: Items.initialize, getItems: Items.getItems, getAddedItem: Items.getAddedItem, addItem: Items.addItem, getItem: Items.getItem, removeItem: Items.removeItem}}
         */
        var Items = {
            /**
             * List of cart items
             */
            items: ko.observableArray(),
            /**
             * Constructor
             * @returns {Items}
             */
            initialize: function () {
                self = this;
                /**
                 * Check if itemlist is empty
                 */
                self.isEmpty = ko.pureComputed(function () {
                    return (self.items().length > 0) ? false : true;
                });

                var retrievedObject = JSON.parse(localStorage.getItem('qodItems'));

                if (retrievedObject) {
                    this.items(retrievedObject);
                }

                return self;
            },

            /**
             * Get list of cart items
             * @returns {*}
             */
            getItems: function () {
                return this.items();
            },

            /**
             * Add product to list item
             * @param data
             */
            addItem: function (data) {
                var item = Item.init(data);
                this.items.push(item);
                this.setLocalStorage();
            },

            /**
             * Add product to list item
             * @param data
             */
            addItemFixPosition: function (i, data) {
                this.items.splice(i, 0, data);

                this.setLocalStorage();
            },

            /**
             * Get list of cart items
             * @returns {*}
             */
            doubleItem: function (itemId) {
                var olditem = false,
                    allItem = ko.observableArray([]);
                allItem(this.items.slice());
                var itemdouble = JSON.parse(JSON.stringify(allItem()));

                $.each(itemdouble, function (i, itemSelected) {
                    if (itemSelected.item_id === itemId) {
                        if (itemSelected.type_id !== 'simple') {
                            var dataPrepare = $.extend({}, itemSelected);
                            dataPrepare.item_id = dataPrepare.item_id + 1;
                            self.addItem(dataPrepare);
                            return false;
                        }
                    }
                });

                this.setLocalStorage();
            },

            /**
             * Get cart item by item id
             * @param itemId
             * @returns {boolean}
             */
            getItem: function (itemId) {
                var item = false;
                var foundItem = ko.utils.arrayFirst(this.items(), function (item) {
                    return (item.item_id === itemId);
                });
                if (foundItem) {
                    item = foundItem;
                }
                return item;
            },

            /**
             * Get item existing by type and sku
             * @param itemId
             * @returns {boolean}
             */
            getItemExisted: function (typeId, sku) {
                var itemExisted = false;
                var foundItem = ko.utils.arrayFirst(this.items(), function (item) {
                    if (typeId !== 'configurable') {
                        return (item.sku === sku);
                    }
                });
                if (foundItem) {
                    itemExisted = true;
                }
                return itemExisted;
            },

            /**
             * Remove item by id
             * @param itemId
             */
            removeItem: function (itemId) {
                this.items.remove(function (item) {
                    return item.item_id === itemId;
                });

                this.setLocalStorage();
            },

            /**
             * get sku item by id
             * @param itemId
             */
            getskuItem: function (list_items, itemId) {
                var skuItem = null;

                /** get sku Item data*/

                $.each(list_items, function (i, itemUpdate) {
                    if (itemUpdate.type_id === "grouped") {
                        $.each(itemUpdate.childProduct, function (index, value) {
                            if (value.item_id === itemId) {
                                skuItem = value.sku;
                            }
                        });
                    }
                    if (itemUpdate.item_id === itemId) {
                        if (itemUpdate.sku_child) {
                            skuItem = itemUpdate.sku_child;
                        } else {
                            skuItem = itemUpdate.sku;
                        }
                    }
                });

                return skuItem;
            },

            /**
             * plus qty item by id
             * @param itemId
             */
            plusQty: function (itemId) {
                var olditem = false,
                    allItem = ko.observableArray([]),
                    skuItem = null,
                    stockQtyofItem = null,
                    urlStockQty = window.qodConfig.itemqty,
                    el_overstock = $('#qty-message'),
                    el_lazyload = $('#lazyload');

                allItem(this.items.slice());
                skuItem = self.getskuItem(allItem(), itemId);
                el_lazyload.show();
                $.ajax({
                    url: urlStockQty,
                    data: {
                        itemsku: skuItem,
                    },
                    method: 'POST',
                    success: function (response) {
                        if (response) {
                            stockQtyofItem = response['stockQtyofItem'];
                            /** remove old item to change qty and replace new data*/
                            $.each(allItem(), function (i, itemUpdate) {
                                if (itemUpdate.item_id === itemId) {
                                    if (itemUpdate.type_id === 'configurable' || itemUpdate.type_id === 'simple') {
                                        if (itemUpdate.qty < stockQtyofItem) {
                                            self.removeItem(itemId);
                                            itemUpdate.qtystock = stockQtyofItem;
                                            itemUpdate.qty = parseInt(itemUpdate.qty) + response['minSaleQty'];
                                            itemUpdate.total = self.getTierPrices(itemUpdate.qty, itemUpdate);
                                            if (Array.isArray(itemUpdate.customOptions)) {
                                                $.each(itemUpdate.customOptions, function (i, customOption) {
                                                    itemUpdate.total += parseFloat(customOption.amount);
                                                });
                                            }
                                            self.addItemFixPosition(i, itemUpdate);
                                        } else {
                                            el_overstock.text("We don't have as many \"" + itemUpdate.name + "\" as you requested");
                                            self.showMessage(el_overstock, 5000);
                                        }
                                        el_lazyload.hide();
                                    } else {
                                        self.removeItem(itemId);
                                        itemUpdate.qtystock = stockQtyofItem;
                                        itemUpdate.qty = parseInt(itemUpdate.qty) + 1;
                                        itemUpdate.total = parseInt(itemUpdate.qty) * itemUpdate.price;
                                        if (Array.isArray(itemUpdate.customOptions)) {
                                            $.each(itemUpdate.customOptions, function (i, customOption) {
                                                itemUpdate.total += parseFloat(customOption.amount);
                                            });
                                        }
                                        self.addItemFixPosition(i, itemUpdate);
                                        el_lazyload.hide();
                                    }
                                }
                            });
                        }
                    }
                });
                this.items(allItem());
                this.setLocalStorage();
            },

            /**
             * minus qty item by id
             * @param itemId
             */
            minusQty: function (itemId) {
                var olditem = false,
                    parentItemId = 0,
                    total = 0,
                    allItem = ko.observableArray([]);
                allItem(this.items.slice());

                $.each(allItem(), function (i, itemUpdate) {
                    if (itemUpdate.item_id === itemId) {
                        if (parseInt(itemUpdate.qty) > 1) {
                            if (itemUpdate.type_id === "bundle") {
                                parentItemId = itemId;
                                self.removeItem(itemId);
                                itemUpdate.qty = parseInt(itemUpdate.qty) - itemUpdate.minSaleQty;
                                $.each(itemUpdate.bundleSelectOption, function (index, value) {
                                    var qty = parseInt(value.selection_qty);
                                    total += self.getTierPrices(qty, value);
                                });
                                total = total * itemUpdate.qty;
                                self.getBundleTierPrices(total, itemUpdate);
                                self.addItemFixPosition(i, itemUpdate);
                            } else {
                                self.removeItem(itemId);
                                itemUpdate.qty = parseInt(itemUpdate.qty) - itemUpdate.minSaleQty;
                                if (itemUpdate.tier_price.length > 0) {
                                    itemUpdate.total = itemUpdate.total - itemUpdate.price;
                                    for (var i = 0; i < itemUpdate.tier_price.length; i++) {
                                        if (i + 1 < itemUpdate.tier_price.length) {
                                            if (
                                                itemUpdate.qty >= itemUpdate.tier_price[i]['price_qty'] &&
                                                itemUpdate.qty < itemUpdate.tier_price[i + 1]['price_qty']
                                            ) {
                                                itemUpdate.total = parseInt(itemUpdate.qty) * itemUpdate.tier_price[i]['price'];
                                            } else if (itemUpdate.qty < itemUpdate.tier_price[0]['price_qty']) {
                                                itemUpdate.total = parseInt(itemUpdate.qty) * itemUpdate.price;
                                            } else if (itemUpdate.qty > itemUpdate.tier_price[itemUpdate.tier_price.length - 1]['price_qty']) {
                                                itemUpdate.total = parseInt(itemUpdate.qty) * itemUpdate.tier_price[itemUpdate.tier_price.length - 1]['price'];
                                            }
                                        } else {
                                            if (itemUpdate.qty >= itemUpdate.tier_price[itemUpdate.tier_price.length - 1]['price_qty']) {
                                                itemUpdate.total = parseInt(itemUpdate.qty) * itemUpdate.tier_price[itemUpdate.tier_price.length - 1]['price'];
                                            }
                                        }
                                    }
                                } else {
                                    itemUpdate.total = itemUpdate.total - (itemUpdate.price * itemUpdate.minSaleQty);
                                }
                                self.addItemFixPosition(i, itemUpdate);
                            }
                        }
                    }

                });

                this.items(allItem());
                this.setLocalStorage();
            },

            /**
             * save item
             */
            getAllItems: function () {
                var items = this.items();
                return items;
            },

            /**
             *
             * @param itemId
             * @param customOption
             * @param event
             */
            changeCustomOptions: function (itemId, customOption, event) {
                var allItem = ko.observableArray([]),
                    valueInput = event.currentTarget.value;
                allItem(this.items.slice());
                $.each(allItem(), function (i, itemUpdate) {
                    if (itemUpdate.item_id == itemId) {
                        self.removeItem(itemId);
                        /**
                         * Case custom option type is text
                         */
                        if (customOption.groupType == 'text') {
                            if (parseFloat(customOption.price) > 0 && customOption.title.toLowerCase() === valueInput.toLowerCase()) {
                                itemUpdate.total = itemUpdate.total + parseFloat(customOption.price);
                                switch (customOption.type) {
                                    case 'field':
                                        itemUpdate.customOptionValue.field = valueInput;
                                        break;
                                    default:
                                        itemUpdate.customOptionValue.area = valueInput;
                                }
                                customOption.amount = parseFloat(customOption.price);
                            } else if (customOption.amount !== 0 && valueInput === '') {
                                itemUpdate.total = itemUpdate.total - parseFloat(customOption.amount);
                                customOption.amount = 0;
                            }
                        }
                        /**
                         * Case custom option type is select
                         */
                        if (customOption.groupType == 'select') {
                            if (customOption.type == 'multiple') {
                                var total = 0;
                                valueInput = event.currentTarget.selectedOptions;
                                itemUpdate.customOptionValue.multiple = [];
                                $.each(valueInput, function (i, input) {
                                    var value = input.value;
                                    $.each(customOption.price, function (i, price) {
                                        if (value === i) {
                                            itemUpdate.customOptionValue.multiple.push(i);
                                            if (valueInput.length === 1) {
                                                itemUpdate.total = itemUpdate.total + parseFloat(price) - customOption.amount;
                                                customOption.amount = parseFloat(price);
                                            } else {
                                                total += parseFloat(price);
                                                itemUpdate.total = itemUpdate.total + total - customOption.amount;
                                                customOption.amount = total;
                                            }
                                        }
                                    });
                                })
                            } else if (customOption.type === 'checkbox') {
                                var checked = event.currentTarget.checked;
                                if (itemUpdate.customOptionValue.checkbox === '') {
                                    itemUpdate.customOptionValue.checkbox = [];
                                }
                                $.each(customOption.price, function (i, price) {
                                    if (checked === true && valueInput === i) {
                                        itemUpdate.customOptionValue.checkbox.push(i);
                                        itemUpdate.total = itemUpdate.total + parseFloat(price);
                                        customOption.amount += parseFloat(price);
                                    } else if (checked === false && valueInput === i) {
                                        itemUpdate.customOptionValue.checkbox.splice(itemUpdate.customOptionValue.checkbox.indexOf(valueInput), 1);
                                        itemUpdate.total = itemUpdate.total - parseFloat(price);
                                        customOption.amount = customOption.amount - parseFloat(price);
                                    }
                                });
                            } else if (customOption.type === 'radio') {
                                var checked = event.currentTarget.checked;
                                $.each(customOption.price, function (i, price) {
                                    if (checked && valueInput === i) {
                                        itemUpdate.customOptionValue.radio = i;
                                        if (customOption.amount === 0) {
                                            itemUpdate.total = itemUpdate.total + parseFloat(price);
                                        } else {
                                            itemUpdate.total = itemUpdate.total + parseFloat(price) - customOption.amount;
                                        }
                                        customOption.amount = parseFloat(price);
                                    }
                                });
                            } else {
                                $.each(customOption.price, function (i, price) {
                                    itemUpdate.customOptionValue.drop_down = valueInput;
                                    if (valueInput === i && customOption.amount === 0) {
                                        itemUpdate.total = itemUpdate.total + parseFloat(customOption.price[i]);
                                        customOption.amount = parseFloat(price);
                                    } else if (valueInput === i && customOption.amount > 0) {
                                        itemUpdate.total = itemUpdate.total - customOption.amount + parseFloat(customOption.price[i]);
                                        customOption.amount = parseFloat(price);
                                    } else if (valueInput === '' && customOption.amount > 0) {
                                        itemUpdate.total = itemUpdate.total - customOption.amount;
                                        customOption.amount = 0;
                                    }
                                });
                            }
                        }
                        self.addItemFixPosition(i, itemUpdate);
                    }
                });
                $('#detail' + itemId).show();
                $('#btn-show-detail-' + itemId).html('Hide details');


                this.items(allItem());
                this.setLocalStorage();
            },

            /**
             * change Qty
             */
            changeQty: function (itemId, event) {
                var allItem = ko.observableArray([]),
                    valueInput = event.currentTarget.value,
                    stockQtyofItem = null,
                    total = 0,
                    skuItem = null,
                    urlStockQty = window.qodConfig.itemqty,
                    el_lazyload = $('#lazyload'),
                    el_overstock = $('#qty-message'),
                    parentItemId = 0;
                allItem(this.items.slice());

                $.each(allItem(), function (i, itemUpdate) {
                    if (itemUpdate.type_id == "grouped") {
                        $.each(itemUpdate.childProduct, function (index, value) {
                            if (value.item_id === itemId) {
                                parentItemId = itemUpdate.item_id;
                            }
                        });
                    }
                });
                skuItem = self.getskuItem(allItem(), itemId);
                el_lazyload.show();
                $.ajax({
                    url: urlStockQty,
                    data: {
                        itemsku: skuItem,
                        currentQty: parseInt(valueInput),
                    },
                    method: 'POST',
                    success: function (response) {
                        if (response) {
                            stockQtyofItem = response['stockQtyofItem'];
                            /** remove old item to change qty and replace new data*/
                            $.each(allItem(), function (i, itemUpdate) {
                                if (itemUpdate.type_id === "grouped") {
                                    /**
                                     * Grouped product
                                     */
                                    var check = false;
                                    $.each(itemUpdate.childProduct, function (index, value) {
                                        if (value.item_id === itemId) {
                                            check = true;
                                        }
                                    });
                                    if (check == true) {
                                        var groupTotal = 0;
                                        $.each(itemUpdate.childProduct, function (index, value) {
                                            if (value.item_id === itemId) {
                                                if (parseInt(valueInput) <= parseInt(stockQtyofItem)) {
                                                    self.removeItem(itemUpdate.item_id);

                                                    //Set RoundOff Qty
                                                    valueInput = Math.round(valueInput/response['minSaleQty']) * response['minSaleQty'];

                                                    value.qty = parseInt(valueInput);
                                                } else {
                                                    self.removeItem(itemUpdate.item_id);
                                                    el_overstock.text("We don't have as many \"" + response['overStock'] + "\" as you requested");
                                                    self.showMessage(el_overstock, 5000);
                                                }
                                            }
                                            groupTotal += self.getTierPrices(value.qty, value);
                                        });
                                        itemUpdate.total = groupTotal;
                                        self.addItemFixPosition(i, itemUpdate);
                                        el_lazyload.hide();
                                    }
                                } else if (itemUpdate.item_id === itemId) {
                                    if (parseInt(valueInput) === 0) {
                                        self.removeItem(itemId);
                                        el_lazyload.hide();
                                    } else {
                                        if (parseInt(valueInput) <= parseInt(stockQtyofItem)) {
                                            self.removeItem(itemId);

                                            //set RoundOff Qty
                                            valueInput = Math.round(valueInput/response['minSaleQty']) * response['minSaleQty'];

                                            itemUpdate.qty = parseInt(valueInput);
                                            itemUpdate.total = self.getTierPrices(itemUpdate.qty, itemUpdate);
                                            if (Array.isArray(itemUpdate.customOptions)) {
                                                $.each(itemUpdate.customOptions, function (i, customOption) {
                                                    itemUpdate.total += parseFloat(customOption.amount);
                                                });
                                            }
                                            self.addItemFixPosition(i, itemUpdate);
                                        } else {
                                            self.removeItem(itemId);
                                            self.addItemFixPosition(i, itemUpdate);
                                            el_overstock.text("We don't have as many \"" + response['overStock'] + "\" as you requested");
                                            self.showMessage(el_overstock, 5000);
                                        }
                                        el_lazyload.hide();
                                    }

                                }
                            });
                            if (parentItemId !== 0) {
                                $('#detail' + parentItemId).show();
                                $('#btn-show-detail-' + parentItemId).html('Hide details');
                            }
                        }
                    }


                });

                this.items(allItem());
                this.setLocalStorage();
            },

            /**
             * change Options of each item
             * @param itemId
             */
            changeOptions: function (itemId, optionId, event) {
                var allItem = ko.observableArray([]),
                    attributeSelect = optionId,
                    productId = 0,
                    optionIds = '',
                    attrValueSelected = event.currentTarget.selectedOptions[0].value,
                    urlChangeOption = window.qodConfig.changeOption;
                allItem(this.items.slice());

                /** update key options*/
                $.each(allItem(), function (i, itemUpdate) {
                    if (itemUpdate.item_id == itemId) {
                        /** set attribute and value changed*/
                        $.each(itemUpdate.options, function (i, opAttribute) {
                            var opSplit = opAttribute.split(':'),
                                attributeChange = opSplit[0],
                                valueSelected = opSplit[1];

                            if (attributeChange == attributeSelect) {
                                valueSelected = attrValueSelected;
                                /** set order array value select*/
                                var options_select_value = itemUpdate.options_select_value[attributeChange];
                                $.each(options_select_value, function (key, value) {
                                    if (valueSelected == value) {
                                        options_select_value.splice(key, 1);
                                        options_select_value.unshift(valueSelected);
                                    }
                                });
                            }
                            opAttribute = attributeChange + ':' + valueSelected;
                            itemUpdate.options[i] = opAttribute;
                            productId = itemUpdate.product_id;
                        });
                    }
                });

                /** update key optionIds*/
                $.each(allItem(), function (i, itemUpdate) {
                    if (itemUpdate.item_id == itemId) {
                        /** find attribute Id need change*/
                        var superAttribute = itemUpdate.super_attribute,
                            attrNeedChange = '';
                        $.each(superAttribute, function (i, supperCode) {
                            var codeIdSplit = supperCode.split(':'),
                                attrCodeId = codeIdSplit[0],
                                attrCode = codeIdSplit[1];

                            if (attributeSelect == attrCode) {
                                attrNeedChange = attrCodeId;
                            }
                        });

                        /** set attribute and value changed*/
                        $.each(itemUpdate.optionIds, function (i, opIdAttribute) {
                            var opIdSplit = opIdAttribute.split(':'),
                                attributeChange = opIdSplit[0],
                                valueIdSelected = opIdSplit[1],
                                valuechange = valueIdSelected;


                            if (attributeChange == attrNeedChange) {
                                valueIdSelected = attrValueSelected;
                                /** update Id*/
                                var options_select_value_id = itemUpdate.options_select_value_id[attributeSelect];
                                $.each(options_select_value_id, function (key, value) {
                                    var idvalue = value.split(':');
                                    if (valueIdSelected == idvalue[1]) {
                                        valuechange = idvalue[0];
                                    }
                                });
                            }
                            opIdAttribute = attributeChange + ':' + valuechange;
                            itemUpdate.optionIds[i] = opIdAttribute;
                        });
                        optionIds = itemUpdate.optionIds;
                    }
                });

                $.ajax({
                    url: urlChangeOption,
                    data: {
                        optionIds: optionIds,
                        product_id: productId
                    },
                    method: 'POST',
                    success: function (response) {
                        $.each(allItem(), function (i, itemUpdate) {
                            if (itemUpdate.item_id == itemId) {
                                self.removeItem(itemId);
                                itemUpdate.qtystock = parseInt(response['qtyStock']);
                                itemUpdate.imageUrl = response['imageURL'];
                                itemUpdate.sku_child = response['sku_child'];
                                itemUpdate.price = response['price'];
                                itemUpdate.total = response['price'] * itemUpdate.qty;
                                if (Array.isArray(itemUpdate.customOptions)) {
                                    $.each(itemUpdate.customOptions, function (i, customOption) {
                                        itemUpdate.total += parseFloat(customOption.amount);
                                    });
                                }

                                self.addItemFixPosition(i, itemUpdate);
                                el_lazyload.hide();
                            }
                        });
                    }
                });
                this.items(allItem());
                this.setLocalStorage();
            },

            /**
             * change option bundle
             */
            changeBundle: function (item_id, product, option, event) {
                var allItem = ko.observableArray([]),
                    valueInput = event.currentTarget.value,
                    checkInput = event.currentTarget.checked,
                    total = 0,
                    parentItemId = 0,
                    number = null;
                allItem(this.items.slice());

                $.each(allItem(), function (i, itemUpdate) {
                    if (itemUpdate.item_id == item_id) {
                        parentItemId = itemUpdate.item_id;
                        self.removeItem(item_id);
                        if (option.type == "select" || option.type == "radio") {
                            $.each(itemUpdate.bundleSelectOption, function (index, value) {
                                if (option.option_id == value.option_id) {
                                    number = index;
                                }
                            });
                            if (number !== null) {
                                itemUpdate.bundleSelectOption.splice(number, 1);
                            }

                            if (valueInput !== 'null') {
                                for (var i = 0; i < product.length; i++) {
                                    if (product[i].selection_id == valueInput) {
                                        itemUpdate.bundleSelectOption.push(product[i]);
                                    }
                                }
                            }
                        }
                        if (option.type == "checkbox") {
                            $.each(itemUpdate.bundleSelectOption, function (index, value) {
                                if (checkInput == false && value.selection_id == valueInput) {
                                    number = index;
                                }
                            });
                            if (number !== null) {
                                itemUpdate.bundleSelectOption.splice(number, 1);
                            }
                            if (checkInput == true) {
                                for (var i = 0; i < product.length; i++) {
                                    if (product[i].selection_id == valueInput) {
                                        itemUpdate.bundleSelectOption.push(product[i]);
                                    }
                                }
                            }
                        }
                        if (option.type == "multi") {
                            var multiOptions = event.currentTarget.selectedOptions;
                            $.each(multiOptions, function (index, multiOption) {
                                for (var i = 0; i < itemUpdate.bundleSelectOption.length; i++) {
                                    if (multiOption.value !== itemUpdate.bundleSelectOption[i].selection_id && itemUpdate.bundleSelectOption[i].option_id == option.option_id) {
                                        itemUpdate.bundleSelectOption.splice(i, 1);
                                    }
                                }
                            });

                            $.each(multiOptions, function (index, multiOption) {
                                for (var i = 0; i < product.length; i++) {
                                    if (product[i].selection_id == multiOption.value) {
                                        itemUpdate.bundleSelectOption.push(product[i]);
                                    }
                                }
                            });
                        }
                        $.each(itemUpdate.bundleSelectOption, function (i, value) {
                            var qty = parseInt(value.selection_qty);
                            total += self.getTierPrices(qty, value);
                        });
                        total = total * itemUpdate.qty;
                        if (itemUpdate.tier_price.length > 0) {
                            for (var i = 0; i < itemUpdate.tier_price.length; i++) {
                                if (i + 1 < itemUpdate.tier_price.length) {
                                    if (
                                        itemUpdate.qty >= itemUpdate.tier_price[i]['price_qty'] &&
                                        itemUpdate.qty < itemUpdate.tier_price[i + 1]['price_qty']
                                    ) {
                                        itemUpdate.total = total * (100 - itemUpdate.tier_price[i]['price']) / 100;
                                    } else if (itemUpdate.qty < value.tier_price[0]['price_qty']) {
                                        itemUpdate.total = total;
                                    } else if (itemUpdate.qty > value.tier_price[value.tier_price.length - 1]['price_qty']) {
                                        itemUpdate.total = total * (100 - itemUpdate.tier_price[itemUpdate.tier_price.length - 1]['price']) / 100;
                                    }
                                } else {
                                    if (itemUpdate.qty >= itemUpdate.tier_price[itemUpdate.tier_price.length - 1]['price_qty']) {
                                        itemUpdate.total = total * (100 - itemUpdate.tier_price[itemUpdate.tier_price.length - 1]['price']) / 100;
                                    } else {
                                        itemUpdate.total = total;
                                    }
                                }
                            }
                        } else {
                            itemUpdate.total = total;
                        }
                        self.addItemFixPosition(i, itemUpdate);
                        if (parentItemId !== 0) {
                            $('#detail' + parentItemId).show();
                            $('#btn-show-detail-' + parentItemId).html('Hide details');
                        }
                    }
                });
            },

            /**
             * change option qty
             */
            changeOptionBundleQty: function (item_id, product, event) {
                var allItem = ko.observableArray([]),
                    valueInput = event.currentTarget.value,
                    skuItem = null,
                    stockQtyofItem = null,
                    urlStockQty = window.qodConfig.itemqty,
                    total = 0,
                    el_lazyload = $('#lazyload'),
                    parentItemId = 0,
                    el_overstock = $('#qty-message');
                allItem(this.items.slice());
                $.each(allItem(), function (i, itemUpdate) {
                    if (itemUpdate.item_id === item_id) {
                        valueInput = itemUpdate.qty * parseInt(valueInput);
                    }
                });
                skuItem = product.sku;
                el_lazyload.show();
                $.ajax({
                    url: urlStockQty,
                    data: {
                        itemsku: skuItem,
                        currentQty: parseInt(valueInput),
                    },
                    method: 'POST',
                    success: function (response) {
                        if (response) {
                            stockQtyofItem = response['stockQtyofItem'];
                            /** remove old item to change qty and replace new data*/
                            $.each(allItem(), function (i, itemUpdate) {
                                if (itemUpdate.item_id === item_id) {
                                    parentItemId = itemUpdate.item_id;
                                    if (parseInt(valueInput) <= parseInt(stockQtyofItem)) {
                                        self.removeItem(item_id);
                                        $.each(itemUpdate.bundleSelectOption, function (index, value) {
                                            if (product.selection_id == value.selection_id) {
                                                itemUpdate.bundleSelectOption[index].selection_qty = parseInt(valueInput);
                                            }
                                            var qty = parseInt(value.selection_qty);
                                            total += self.getTierPrices(qty, value);
                                        });
                                        total = total * itemUpdate.qty;
                                        self.getBundleTierPrices(total, itemUpdate);
                                        self.addItemFixPosition(i, itemUpdate);
                                    } else {
                                        self.removeItem(item_id);
                                        self.addItemFixPosition(i, itemUpdate);
                                        el_overstock.text("We don't have as many \"" + response['overStock'] + "\" as you requested");
                                        self.showMessage(el_overstock, 5000);
                                    }
                                    el_lazyload.hide();
                                }
                            });
                            if (parentItemId !== 0) {
                                $('#detail' + parentItemId).show();
                                $('#btn-show-detail-' + parentItemId).html('Hide details');
                            }
                        }
                    }
                });

                this.items(allItem());
                this.setLocalStorage();
            },

            /**
             * set Local Storage
             */
            setLocalStorage: function () {
                return localStorage.setItem('qodItems', JSON.stringify(this.items()));
            },

            /**
             * set Local Storage
             */
            clearLocalStorage: function () {
                return localStorage.removeItem('qodItems');
            },

            /**
             * get Data From search
             */
            getDataItemsFromSearch: function (sku) {
                var texts = [sku + ',' + 1],
                    url = window.qodConfig.buildItemUrl,
                    el_search = $('#quickod-instansearch'),
                    el_lazyload = $('#lazyload');

                el_lazyload.show();
                $.ajax({
                    url: url,
                    data: {
                        value: texts
                    },
                    method: 'POST',
                    success: function (response) {
                        if (response != '') {
                            for (var key in response) {
                                if (!response.hasOwnProperty(key)) continue;
                                var obj = response[key];
                                self.addItem(obj);
                                el_search.val('');
                                el_lazyload.hide();
                            }
                        }
                    }
                });
            },

            /**
             * Add item to cart
             */
            addCartAction: function () {
                var items = this.items(),
                    url = window.qodConfig.addCartAction,
                    url_cart_page = window.qodConfig.cartpage,
                    el_error_message = $('#addcart-message'),
                    el_lazyload = $('#lazyload'),
                    isRequire = false,
                    bundle_require = $('#bundle-message'),
                    product_message = $("#product-message");
                var title = [];

                console.log(url);


                if (items.length == 0 || items == '') {
                    self.showMessage(el_error_message, 5000);
                } else {
                    $.each(items, function (i, item) {
                        $.each(item.customOptions, function (j, customOption) {
                            if (parseInt(customOption.isRequire) === 1 && parseFloat(customOption.amount) === 0) {
                                isRequire = true;
                                title.push(customOption.title);
                            }
                        });
                    });
                    if (title !== '') {
                        el_error_message.text('These are required fields: ' + title.toString());
                        self.showMessage(el_error_message, 10000);
                    }
                    if (!isRequire) {
                        el_error_message.hide();
                        el_lazyload.show();
                        $.ajax({
                            url: url,
                            data: {
                                listitem: items
                            },
                            method: 'POST',
                            success: function (response) {
                                if (response) {
                                    if (response.requireoption == false) {
                                        bundle_require.show();
                                        self.showMessage(bundle_require, 5000);
                                        el_lazyload.hide();
                                    } else if (response.limitData == false) {
                                        product_message.show();
                                        self.showMessage(product_message, 5000);
                                        el_lazyload.hide();
                                    } else {
                                        self.clearAllItems();
                                        el_lazyload.hide();
                                        self.redirectNextProcess(url_cart_page);
                                    }
                                } else {
                                    el_lazyload.hide();
                                }
                            }
                        });
                    }


                }
            },

            /**
             * Add item to cart and redirect to checkout process
             */
            checkoutProcessAction: function () {
                var items = this.getItems(),
                    url = window.qodConfig.addCartAction,
                    url_checkout_step = window.qodConfig.checkoutStep,
                    el_error_message = $('#checkout-message'),
                    el_lazyload = $('#lazyload');

                if (items.length == 0 || items == '') {
                    $.ajax({
                        url: url,
                        method: 'POST',
                        success: function (response) {
                            if (response && response['hasItems']) {
                                el_lazyload.hide();
                                self.redirectNextProcess(url_checkout_step);
                            } else if (response && response['noItem']) {
                                el_lazyload.hide();
                                self.showMessage(el_error_message, 5000);
                            } else {
                                el_lazyload.hide();
                                self.showMessage(el_error_message, 5000);
                            }
                        }
                    });
                } else {
                    el_error_message.hide();
                    el_lazyload.show();
                    $.ajax({
                        url: url,
                        data: {
                            listitem: items
                        },
                        method: 'POST',
                        success: function (response) {
                            if (response) {
                                self.clearAllItems();
                                el_lazyload.hide();
                                self.redirectNextProcess(url_checkout_step);
                            } else {
                                el_lazyload.hide();
                            }
                        }
                    });
                }
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
            },

            /**
             * redirect to cart page
             */
            redirectNextProcess: function (url_next_process) {
                $(location).attr("href", url_next_process);
            },

            /**
             * remove all item
             */
            clearAllItems: function () {
                self.items.removeAll();
                this.clearLocalStorage();
            },

            /**
             *
             * @param price
             * @returns {*}
             */
            getFormattedPrice: function (price) {
                return priceUtils.formatPrice(price, window.qodConfig.priceFormat);
            },

            /**
             * plus qty Bundle item by id
             * @param itemId
             */
            plusBundleQty: function (itemId) {
                var allItem = ko.observableArray([]),
                    stockQtyofItem = null,
                    bundleProduct = null,
                    checkStock = null,
                    total = 0,
                    parentItemId = 0,
                    urlStockQty = window.qodConfig.bundleitemqty,
                    el_overstock = $('#qty-message'),
                    el_lazyload = $('#lazyload');
                allItem(this.items.slice());
                $.each(allItem(), function (i, itemUpdate) {
                    if (itemUpdate.item_id === itemId) {
                        bundleProduct = itemUpdate;
                        parentItemId = itemId;
                    }
                });
                el_lazyload.show();
                $.ajax({
                    url: urlStockQty,
                    data: {
                        bundleproduct: bundleProduct
                    },
                    method: 'POST',
                    success: function (response) {
                        if (response) {
                            stockQtyofItem = response['stockQtyofItem'];
                            checkStock = response['overStock'];
                            /** remove old item to change qty and replace new data*/
                            $.each(allItem(), function (i, itemUpdate) {
                                if (itemUpdate.item_id === itemId) {
                                    if (!checkStock) {
                                        self.removeItem(itemId);
                                        itemUpdate.qty = parseInt(stockQtyofItem);
                                        $.each(itemUpdate.bundleSelectOption, function (index, value) {
                                            var qty = parseInt(value.selection_qty);
                                            total += self.getTierPrices(qty, value);
                                        });
                                        total = total * itemUpdate.qty;
                                        self.getBundleTierPrices(total, itemUpdate);
                                        self.addItemFixPosition(i, itemUpdate);
                                    } else {
                                        self.removeItem(itemId);
                                        self.addItemFixPosition(i, itemUpdate);
                                        el_overstock.text("We don't have as many \"" + response['overStock'] + "\" as you requested");
                                        self.showMessage(el_overstock, 5000);
                                    }
                                    el_lazyload.hide();
                                }
                            });
                        }
                    }
                });

                this.items(allItem());
                this.setLocalStorage();
            },

            /**
             * change qty Bundle item by id
             * @param itemId
             */
            changeBundleQty: function (itemId, event) {
                var allItem = ko.observableArray([]),
                    valueInput = event.currentTarget.value,
                    stockQtyofItem = null,
                    bundleProduct = null,
                    total = 0,
                    parentItemId = 0,
                    urlStockQty = window.qodConfig.bundleitemqty,
                    el_lazyload = $('#lazyload'),
                    el_overstock = $('#qty-message');
                allItem(this.items.slice());
                /**
                 *
                 * change bundle product qty
                 */
                $.each(allItem(), function (i, itemUpdate) {
                    if (itemUpdate.item_id === itemId) {
                        bundleProduct = itemUpdate;
                        parentItemId = itemId;
                    }
                });

                el_lazyload.show();
                $.ajax({
                    url: urlStockQty,
                    data: {
                        bundleproduct: bundleProduct,
                        currentQty: parseInt(valueInput),
                    },
                    method: 'POST',
                    success: function (response) {
                        if (response) {
                            stockQtyofItem = response['stockQtyofItem'];
                            /** remove old item to change qty and replace new data*/
                            $.each(allItem(), function (i, itemUpdate) {
                                /** bundle product*/
                                if (itemUpdate.type_id === "bundle" && itemUpdate.item_id === itemId) {
                                    if (parseInt(valueInput) === 0) {
                                        self.removeItem(itemId);
                                        el_lazyload.hide();
                                    } else {
                                        if (parseInt(valueInput) == parseInt(stockQtyofItem)) {
                                            self.removeItem(itemId);
                                            itemUpdate.qty = parseInt(valueInput);
                                            $.each(itemUpdate.bundleSelectOption, function (index, value) {
                                                var qty = parseInt(value.selection_qty);
                                                total += self.getTierPrices(qty, value);
                                            });
                                            total = total * itemUpdate.qty;
                                            self.getBundleTierPrices(total, itemUpdate);
                                            self.addItemFixPosition(i, itemUpdate);
                                        } else {
                                            self.removeItem(itemId);
                                            self.addItemFixPosition(i, itemUpdate);
                                            el_overstock.text("We don't have as many \"" + response['overStock'] + "\" as you requested");
                                            self.showMessage(el_overstock, 5000);
                                        }
                                        el_lazyload.hide();
                                    }
                                }
                            });
                        }
                    }
                });

                this.items(allItem());
                this.setLocalStorage();
            },

            /**
             *
             * @param qty
             * @param product
             * @returns {number}
             */
            getTierPrices: function (qty, product) {
                var childTotal = 0,
                    total = 0;
                if (product.tier_price.length > 0) {
                    childTotal = qty * product.price;
                    for (var i = 0; i < product.tier_price.length; i++) {
                        if (i + 1 < product.tier_price.length) {
                            if (
                                qty >= product.tier_price[i]['price_qty'] &&
                                qty < product.tier_price[i + 1]['price_qty']
                            ) {
                                childTotal = product.tier_price[i]['price'] * qty;
                            } else if (qty < product.tier_price[0]['price_qty']) {
                                childTotal = qty * product.price;
                            } else if (qty > product.tier_price[product.tier_price.length - 1]['price_qty']) {
                                childTotal = product.tier_price[product.tier_price.length - 1]['price'] * qty;
                            }
                        } else {
                            if (qty >= product.tier_price[product.tier_price.length - 1]['price_qty']) {
                                childTotal = product.tier_price[product.tier_price.length - 1]['price'] * qty;
                            }
                        }
                    }
                    total = childTotal;
                } else {
                    total = qty * product.price;
                }

                return total;
            },

            /**
             *
             * @param qty
             * @param product
             */
            getBundleTierPrices: function (total, product) {
                if (product.tier_price.length > 0) {
                    for (var i = 0; i < product.tier_price.length; i++) {
                        if (i + 1 < product.tier_price.length) {
                            if (
                                product.qty >= product.tier_price[i]['price_qty'] &&
                                product.qty < product.tier_price[i + 1]['price_qty']
                            ) {
                                product.total = total * (100 - product.tier_price[i]['price']) / 100;
                            } else if (product.qty < product.tier_price[0]['price_qty']) {
                                product.total = total;
                            } else if (product.qty > product.tier_price[product.tier_price.length - 1]['price_qty']) {
                                product.total = total * (100 - product.tier_price[product.tier_price.length - 1]['price']) / 100;
                            }
                        } else {
                            if (product.qty >= product.tier_price[product.tier_price.length - 1]['price_qty']) {
                                product.total = total * (100 - product.tier_price[product.tier_price.length - 1]['price']) / 100;
                            } else {
                                product.total = total;
                            }
                        }
                    }
                } else {
                    product.total = total;
                }

                return product;
            },
        };
        return Items.initialize();
    }
);
