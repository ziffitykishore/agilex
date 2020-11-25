import $ from 'jquery';
import domready from 'domready';
import breakpoints from '../utils/breakpoints';

const $menuItem = $('.level-top.level0 a');

const toggleMobileSubmenu = menuItem => {
  menuItem.next('.static-block-submenu').toggle();
}

window.require(['matchMedia'], function(mediaCheck) {
  mediaCheck({
    media: `(max-width: ${breakpoints.mobile__nav__breakpoint}px)`,
    entry: function () {
      $menuItem.click(e => {
        e.stopPropagation();

        if ($(e.currentTarget).find('span').length) {
          e.preventDefault();
        }

        toggleMobileSubmenu($(e.currentTarget));
      });
    },
    exit: function () {}
  });
});

domready(() => {
  // this clones the account links from desktop to the mobile account menu
  $('.panel.header > .header.links').clone().appendTo('#store\\.links');

  // this clones the custom header links from desktop to the mobile main menu
  $('.contact-link').first().clone().appendTo('.navigation ul:first');
  $('.about-link').first().clone().appendTo('.navigation ul:first');
  $('.phone-number.only-on-mobile').first().clone().prependTo('.page-header');

  const switcherTextWithoutPrefix = $('.switcher-option a').text().replace('Country - ', '');
  $('.switcher-option a').text(switcherTextWithoutPrefix);

  $('.level-top.level0').each(function() {
    if ($(this).find('.mobile-submenu').length > 0) {
      $(this).addClass('has-mobile-submenu');
    }

    $(this).hover(() => {
      $('.submenu').hide();
      $(this).find('.submenu').css({
        display: 'block',
        top: '45px',
        left: '70px'
      });
    });
  });
});
