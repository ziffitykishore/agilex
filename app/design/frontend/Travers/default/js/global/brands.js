import $ from 'jquery';

const numbersId = 'numbers';

const assignIdsToColumnTitles = () => {
  $('.pagebuilder-collapsible').each(function () {
    const collapsibleTitle = $(this).find('[data-role="title"]');
    collapsibleTitle.attr('id', collapsibleTitle.text().toLowerCase());

    if (collapsibleTitle.text() === '#') {
      collapsibleTitle.attr('id', numbersId);
    }
  });
}

const wrapNavItemsInAnchorTag = () => {
  $('.brands-nav [data-content-type="text"] p').each(function () {
    if ($(this).text() === '#') {
      $(this).wrap(`<a href='#${numbersId}'></a>`)
    } else {
      $(this).wrap(`<a href=#${$(this).text().toLowerCase()}></a>`)
    }
  });
}

const $brandsNav = $('.brands-nav');

if ($brandsNav.length) {
  $(document).on('click', '.brands-nav [data-content-type="text"] a', function (e) {
    e.preventDefault();
    history.replaceState({page: 'brands'}, `Brand ${$(this).attr('href')}`, `${$(this).attr('href')}`);

    $('html, body').animate({
      scrollTop: $($(this).attr('href')).offset().top - 80
    }, 500);
  });
}

assignIdsToColumnTitles();
wrapNavItemsInAnchorTag();
