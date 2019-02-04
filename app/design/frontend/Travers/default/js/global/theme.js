import $ from 'jquery';
import domready from 'domready';

domready(() => {
    // this clones the account links from desktop to the mobile account menu
    $('.panel.header > .header.links').clone().appendTo('#store\\.links');

    // this clones the custom header links from desktop to the mobile main menu
    $('.contact-link').first().clone().appendTo('.navigation ul');
    $('.about-link').first().clone().appendTo('.navigation ul');
    $('.phone-number').first().clone().prependTo('.page-header');
    $('#algolia-searchbox').append('<button type="submit" class="search-button">Search</button>');
});
