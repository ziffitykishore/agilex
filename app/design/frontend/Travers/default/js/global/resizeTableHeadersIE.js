import $ from 'jquery';
import domready from 'domready';

const resizeTableHeaders = isProductInfoOpened => {
  const refinementsWidth = 240;
  const productInfoWidth = 230;
  const tablePadding = 2 * 70;
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
  })
}

$(window).on('attributesLoaded', function() {
  setTimeout(() => {
    resizeTableHeaders(false);
    $('tr:not(.selected-row)').click(() => resizeTableHeaders(true))
    $('.product-info .close-button').click(() => resizeTableHeaders(false))
  }, 1000);
})
