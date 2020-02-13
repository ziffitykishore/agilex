import $ from 'jquery';
import domready from 'domready';
import objectFitImages from 'object-fit-images';
import customInputs from './inputs';
import promobar from './promobar';
import collapsible from './collapsible';
import './backToTop';
import './brands';
import './fullbleed';
import './stickyNav';
import './theme';
import './coupon';

require('lazysizes');
require('jquery-smooth-scroll');
require('peekaboo-toggle');
require('sticky-table-headers');

collapsible();
customInputs();
promobar();

domready(() => {
  objectFitImages();
  $('.categories-accordion__trigger').peekaboo();
  $('.toggle-content').peekaboo();
  $('.so-smooth').smoothScroll();
  $('.back-to-top').smoothScroll();

  $(window).on('table-headers-ready', function() {
    $('table').stickyTableHeaders({
      fixedOffset: 60
    });
  });
});
