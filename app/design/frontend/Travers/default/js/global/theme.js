import $ from 'jquery';
import domready from 'domready';

domready(() => {
    // this clones the account links from desktop to the mobile account menu
    $('.panel.header > .header.links').clone().appendTo('#store\\.links');
});