import 'core-js/stable';
import 'regenerator-runtime/runtime';
import $ from 'jquery';
import domready from 'domready';
import objectFitImages from 'object-fit-images';
import customInputs from './inputs';
import promobar from './promobar';
import collapsible from './collapsible';
import './backToTop';
import './fullbleed';
import './theme';

require('lazysizes');
require('jquery-smooth-scroll');
require('peekaboo-toggle');

collapsible();
customInputs();
promobar();

domready(() => {
  objectFitImages();
  $('.toggle-content').peekaboo();
  $('.so-smooth').smoothScroll();
  $('.back-to-top').smoothScroll();
});
