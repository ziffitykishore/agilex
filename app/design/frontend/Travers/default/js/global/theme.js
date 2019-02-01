import $ from 'jquery';
import domready from 'domready';

domready(() => {
    // this clones the account links from desktop to the mobile account menu
    $('.panel.header > .header.links').clone().appendTo('#store\\.links');

    // this clones the custom header links from desktop to the mobile main menu
    $('.contact-link').first().clone().appendTo('#store\\.menu');
    $('.about-link').first().clone().appendTo('#store\\.menu');
});