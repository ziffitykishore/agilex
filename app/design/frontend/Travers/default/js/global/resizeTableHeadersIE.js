import $ from 'jquery';
import domready from 'domready';


// @todo remove setTimeout()

const isIE = window.navigator.userAgent.indexOf("MSIE") > 0 || !!navigator.userAgent.match(/Trident\/7\./);

const resizeTableHeaders = isProductInfoOpened => {
  const refinementsWidth = 240;
  const productInfoWidth = 230;
  const tablePadding = 140; // 2 * 70px left/right padding
  let tableWidth;
  
  if (isProductInfoOpened) {
    tableWidth = $(window).innerWidth() - (refinementsWidth + tablePadding + productInfoWidth);
  } else {
    tableWidth = $(window).innerWidth() - (refinementsWidth + tablePadding);
  }

  $('.react-bootstrap-table').each(function() {
    const tableHeaders = $(this).find('thead th');
    const tableRows = $(this).find('tbody tr td');
    const tableHeadersCount = tableHeaders.length;
    const newColumnWidth = tableWidth / tableHeadersCount;

    if (tableHeadersCount < 7) {
      tableHeaders.css('width', newColumnWidth);
      tableRows.css('width', newColumnWidth);
    }
  });
}

if (isIE) {
  $(window).on('attributesLoaded', function() {
    setTimeout(() => {
      resizeTableHeaders(false);
      $('tr:not(.selected-row)').click(() => resizeTableHeaders(true));
    }, 500); // @todo remove setTimeout()
  });

  $(window).on('productInfoOpened', function() {
    $('.plp-content').on('click', '.product-info .close-button', function() {
      resizeTableHeaders(false);
    });
  });
}
