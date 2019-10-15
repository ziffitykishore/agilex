define([
    'jquery',
    'mage/translate'
], function ($,$t) {
    return function (data, currencySymbol) {
        var sku = $('[itemprop=sku]').text();
        var price = data[sku]['price'];

        if (price != null && price != 0) {
            $('.product-info-main div.price-final_price > span:not(.old-price) .price').text(currencySymbol + price);
            $('table.prices-tier').hide();
            if (data[sku]['QtyPrice1'] != 0 || data[sku]['QtyPrice2'] != 0 || data[sku]['QtyPrice3'] != 0) {
                let tiersHtml = renderTierPriceTable(data, sku);
                $('.product-add-form').before(tiersHtml);
                let prices = [data[sku]['QtyPrice1'], data[sku]['QtyPrice2'], data[sku]['QtyPrice3']];
                $lowestPrice = Math.min.apply(null, prices.filter(Boolean));
                $('.as-low-as .price-wrapper ').text(currencySymbol + $lowestPrice);
            }
        }

        function renderTierPriceTable(data, sku) {
            let html = '<table class="prices-tier">';
            html += '<tbody>';
            html += '<tbody>';
            html += '<tr>';
            html += '<th>'+$t('Quantity')+'</th>';

            if (data[sku]['QtyPrice1'] != 0) {
                html += '<th>'+data[sku]['QtyBreak1'];
                if (data[sku]['QtyPrice2'] == 0 && data[sku]['QtyPrice3'] == 0) {
                    html += '+</th>';
                } else {
                    html += '</th>';
                }
            }
            if (data[sku]['QtyPrice2'] != 0) {
                html += '<th>'+data[sku]['QtyBreak2'];
                if (data[sku]['QtyPrice3'] == 0) {
                    html += '+</th>';
                } else {
                    html += '</th>';
                }
            }
            if (data[sku]['QtyPrice3'] != 0) {
                html += '<th>'+data[sku]['QtyBreak3']+'+</th>';
            }
            html += '</tr>';

            html += '<tr>';
            html += '<th class="item row-index">'+$t('Price (ea.):')+'</th>';
            if (data[sku]['QtyPrice1'] != 0) {
                html += '<th class="item">'+ currencySymbol + precise_round(data[sku]['QtyPrice1'], 2)+'</th>';
            }
            if (data[sku]['QtyPrice2'] != 0) {
                html += '<th class="item">'+ currencySymbol + precise_round(data[sku]['QtyPrice2'], 2)+'</th>';
            }
            if (data[sku]['QtyPrice3'] != 0) {
                html += '<th class="item">'+ currencySymbol + precise_round(data[sku]['QtyPrice3'], 2)+'</th>';
            }
            html += '</tr>';

            html += '<tr>';
            html += '<th class="item row-index">'+$t('You Save:')+'</th>';
            if (data[sku]['QtyPrice1'] != 0) {
                html += '<th class="item">'+Math.round(100 - ((100 / price) * data[sku]['QtyPrice1']))+'%'+'</th>';
            }
            if (data[sku]['QtyPrice2'] != 0) {
                html += '<th class="item">'+Math.round(100 - ((100 / price) * data[sku]['QtyPrice2']))+'%'+'</th>';
            }
            if (data[sku]['QtyPrice3'] != 0) {
                html += '<th class="item">'+Math.round(100 - ((100 / price) * data[sku]['QtyPrice3']))+'%'+'</th>';
            }
            html += '</tr>';

            html += '</tbody>';
            html += '</table>';

            return html;
        }

        function precise_round(num, dec){
            return Math.round(num*Math.pow(10,dec))/Math.pow(10,dec).toFixed(dec);
        }
    }
});