import "./preselect";
import $ from 'jquery';
import domready from 'domready';

const setQtyToMinimumIncrement = () => {
  const minimumIncrementText = $('#product_addtocart_form').find('.product.pricing').text();
  const minimumIncrementValue = minimumIncrementText.trim().split(' ').pop();
  const quantityInput = $('#product_addtocart_form').find('#qty');

  if (!minimumIncrementText) {
    return;
  }

  quantityInput.val(minimumIncrementValue);
}

domready(() => {
  if(!$.trim($('.detail-block-description').html()).length ) {
    $('.detail-block-description').hide();
  }

  setQtyToMinimumIncrement();

  $(document).on('click', '.product-info-price .action', function (e) {
    e.preventDefault();

    $('html, body').animate({
      scrollTop: $('.review-fieldset').offset().top - 40
    }, 500);
  });
});
