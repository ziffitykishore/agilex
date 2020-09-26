import $ from 'jquery';
import domready from 'domready';


// @todo remove setTimeout()

function GetIEVersion() {
  var sAgent = window.navigator.userAgent;
  var Idx = sAgent.indexOf("MSIE");

  if (Idx > 0) {
    return parseInt(sAgent.substring(Idx+ 5, sAgent.indexOf(".", Idx)));
  }

  else if (!!navigator.userAgent.match(/Trident\/7\./)) {
    return 11;
  }

  else {
    return 0; //It is not IE
  }
}

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
  });
}

if (GetIEVersion() > 0) {
  $(window).on('attributesLoaded', function() {
    setTimeout(() => {
      resizeTableHeaders(false);
      $('tr:not(.selected-row)').click(() => resizeTableHeaders(true));
      // $('.product-info .close-button').click(() => resizeTableHeaders(false)); @todo get this working. It is added/removed to the DOM
    }, 1000); // @todo remove setTimeout()
  });
}
