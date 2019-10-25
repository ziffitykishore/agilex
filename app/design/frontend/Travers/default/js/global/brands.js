import $ from 'jquery';

const assignIdsToColumnTitles = () => {
  $('.pagebuilder-collapsible').each(function () {
    const collapsibleTitle = $(this).find('[data-role="title"]');
    $(collapsibleTitle).attr('id', collapsibleTitle.text().toLowerCase());
  });
}

const wrapNavItemsInAnchorTag = () => {
  $('.brands-nav [data-content-type="text"] p').each(function () {
    $(this).wrap(`<a href=#${$(this).text().toLowerCase()}></a>`)
  });
}

if ($('body').find('.brands-nav')) {
  $(document).on('click', '.brands-nav [data-content-type="text"] a', function (e) {
    e.preventDefault();

    $('html, body').animate({
      scrollTop: $($.attr(this, 'href')).offset().top - 70
    }, 500);
  });
}

assignIdsToColumnTitles();
wrapNavItemsInAnchorTag();
