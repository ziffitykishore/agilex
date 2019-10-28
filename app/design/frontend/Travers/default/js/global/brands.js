import $ from 'jquery';

const assignIdsToColumnTitles = () => {
  $('.pagebuilder-collapsible').each(function () {
    const collapsibleTitle = $(this).find('[data-role="title"]');
    collapsibleTitle.attr('id', collapsibleTitle.text().toLowerCase());
  });
}

const wrapNavItemsInAnchorTag = () => {
  $('.brands-nav [data-content-type="text"] p').each(function () {
    $(this).wrap(`<a href=#${$(this).text().toLowerCase()}></a>`)
  });
}

const $brandsNav = $('.brands-nav');

if ($brandsNav.length) {
  $(document).on('click', '.brands-nav [data-content-type="text"] a', function (e) {
    e.preventDefault();
    history.replaceState({page: 'brands'}, `Brand ${$(this).attr('href')}`, `${$(this).attr('href')}`);

    $('html, body').animate({
      scrollTop: $($(this).attr('href')).offset().top - 70
    }, 500);
  });
}

assignIdsToColumnTitles();
wrapNavItemsInAnchorTag();
