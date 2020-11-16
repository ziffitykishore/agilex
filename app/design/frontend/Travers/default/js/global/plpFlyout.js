import $ from 'jquery';
import debounce from 'debounce';

let lastScroll = 0;

function scrollPlpFlyout() {
  const scrollTop = $(this).scrollTop();

  if (lastScroll >= scrollTop) {
    $(".product-info-content--inner").animate({ scrollTop: 0 });
  } else {
    $(".product-info-content--inner").animate({ scrollTop: $(".product-info-content--inner")[0].scrollHeight });
  }

  lastScroll = scrollTop;
}

window.onscroll = debounce(scrollPlpFlyout, 50);
