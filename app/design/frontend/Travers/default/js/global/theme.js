import $ from 'jquery';
import domready from 'domready';

domready(() => {
  // this clones the account links from desktop to the mobile account menu
  $('.panel.header > .header.links').clone().appendTo('#store\\.links');

  // this clones the custom header links from desktop to the mobile main menu
  $('.contact-link').first().clone().appendTo('.navigation ul:first');
  $('.about-link').first().clone().appendTo('.navigation ul:first');
  $('.phone-number.only-on-mobile').first().clone().prependTo('.page-header');
  $('#algolia-autocomplete-container .col9').prepend(`<span class="category">Products</span>`);
});
