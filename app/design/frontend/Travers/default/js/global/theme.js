import $ from 'jquery';
import domready from 'domready';

domready(() => {
  // this clones the account links from desktop to the mobile account menu
  $('.panel.header > .header.links').clone().appendTo('#store\\.links');

  // this clones the custom header links from desktop to the mobile main menu
  $('.contact-link').first().clone().appendTo('.navigation ul:first');
  $('.about-link').first().clone().appendTo('.navigation ul:first');
  $('.phone-number.only-on-mobile').first().clone().prependTo('.page-header');

  $('.level-top.level0').each(function() {
    if ($(this).find('.mobile-submenu').length > 0) {
      $(this).addClass('has-mobile-submenu');
    }
  });

  $('.level-top.level0').on('click', function() {
    $(this).find('.mobile-submenu').toggleClass('show');
  })

  $(window).on('refined', function() {
    const maxGroupCount = 3;

    for (let i = 1; i <= maxGroupCount; i++) {
      $(`.list-and-flyout .group--level-${i}`).each(function () {
        if ($(this).find('.list-item').length === 0) {
          $(this).hide();
        } else {
          $(this).show();
        }
      });

      $(`.table-and-flyout .group--level-${i}`).each(function () {
        if ($(this).find('.react-bootstrap-table').length === 0) {
          $(this).hide();
        } else {
          $(this).show();
        }
      });
    }
  });
});

