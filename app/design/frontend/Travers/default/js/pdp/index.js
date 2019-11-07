import "./preselect";
import $ from 'jquery';

$(document).on('click', '.product-info-price .action', function (e) {
  e.preventDefault();

  $('html, body').animate({
    scrollTop: $('.review-fieldset').offset().top - 40
  }, 500);
});
