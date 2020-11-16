import $ from 'jquery';
import debounce from 'debounce';

let lastScroll = 0;

function scrollPlpFlyout() {
  const scrollTop = $(this).scrollTop();
  const flyoutContent = $(".product-info-content--inner");

  if (lastScroll >= scrollTop) {
    flyoutContent.animate({ scrollTop: 0 });
  } else {
    flyoutContent.animate({ scrollTop: flyoutContent[0].scrollHeight });
  }

  lastScroll = scrollTop;
}

window.onscroll = debounce(scrollPlpFlyout, 50);
