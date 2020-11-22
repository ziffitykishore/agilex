import $ from 'jquery';

let lastScroll = 0;

function scrollPlpFlyout() {
  const scrollTop = $(this).scrollTop();
  const flyoutContainer = $('.product-info-content');
  const flyoutContent = $('.product-info-content--inner');
  const windowHeight = window.innerHeight - 100;

  if (
    scrollTop < 500 ||
    !flyoutContent.length ||
    (flyoutContent.length && flyoutContent.height() < windowHeight)
  ) {
    return;
  }

  if (lastScroll >= scrollTop) {
    flyoutContainer.css({
      'transform': 'translate3d(0, 0, 0)',
      transition: '.8s'
    })
  } else {
    flyoutContainer.css({
      'transform': 'translate3d(0,' + (windowHeight - flyoutContent.height()) + 'px, 0)',
      transition: '.8s'
    })
  }

  lastScroll = scrollTop;
}

window.onscroll = scrollPlpFlyout;
