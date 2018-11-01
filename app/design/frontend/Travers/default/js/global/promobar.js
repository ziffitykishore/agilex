import $ from 'jquery';

export default function init() {
  const init = () => {
    const $promobar = $('.promobar');

    if (sessionStorage.getItem('promobar-dismiss')) {
      // Hide promobar if previously dismissed
      $promobar.addClass('js-hide');
    } else {
      $promobar.slideToggle('fast', function(){
        $promobar.addClass('js-animate');
        $promobar.removeClass('js-hide');
      });
    }

    $(document).on('click', '.promobar__close', function() {
      $promobar.slideToggle('fast', function(){
        $(this).closest('.promobar').remove();
      });

      // Remember hidden promo-banner as session storage object
      try {
        sessionStorage.setItem('promobar-dismiss', true);
      } catch(e) {
        // console.log('Your web browser does not support storing settings locally. In Safari, the most common cause of this is using "Private Browsing Mode". Some settings may not save or some features may not work properly for you.');
      }
    });
  }
    
  $(document).ready(function() {
    if ($('.widget .promobar')) {
      init();
    }
  });
};
