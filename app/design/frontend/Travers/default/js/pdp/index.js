import "./preselect";
import $ from 'jquery';
import domready from 'domready';

domready(() => {
  if ($('.detail-block-description').find('div').length === 0) {
    $('.detail-block-description').hide();
  }

  $(document).on('click', '.product-info-price .action', function (e) {
    e.preventDefault();

    $('html, body').animate({
      scrollTop: $('.review-fieldset').offset().top - 40
    }, 500);
  });
});
