import $ from 'jquery';

let lastScroll = 0;

function scrollPlpFlyout() {
  const scrollTop = $(this).scrollTop();
  const flyoutContainer = $('.product-info-content');
  const flyoutContent = $('.product-info-content--inner');
  const windowHeight = window.innerHeight - 100;
  const translateY = px => flyoutContainer.css('transform', `translateY(${px}px)`);

  if (
    scrollTop < 500 ||
    !flyoutContent.length ||
    (flyoutContent.length && flyoutContent.height() < windowHeight)
  ) {
    return;
  }

  if (lastScroll >= scrollTop) {
    translateY(0);
  } else {
    translateY(windowHeight - flyoutContent.height());
  }

  flyoutContainer.css('transition', '.8s');
  lastScroll = scrollTop;
}

window.onscroll = scrollPlpFlyout;
