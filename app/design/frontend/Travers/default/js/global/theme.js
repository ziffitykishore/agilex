import $ from 'jquery';
import domready from 'domready';

const showStaticBlockSubmenu = () => {
  $('.level-top.parent').on('mouseover', function () {
    if ($(this).find('.static-block-submenu').length > 0) {
      setTimeout(() => {
        $(this).find('.static-block-submenu').addClass('show-submenu');
      }, 300);
    }
  })

  $('.level-top.parent').on('mouseout', function () {
    setTimeout(() => {
      $('.show-submenu').removeClass('show-submenu');
    }, 300);
  })
}
domready(() => {
  // this clones the account links from desktop to the mobile account menu
  $('.panel.header > .header.links').clone().appendTo('#store\\.links');

  // this clones the custom header links from desktop to the mobile main menu
  $('.contact-link').first().clone().appendTo('.navigation ul:first');
  $('.about-link').first().clone().appendTo('.navigation ul:first');
  $('.phone-number.only-on-mobile').first().clone().prependTo('.page-header');

showStaticBlockSubmenu()

});
