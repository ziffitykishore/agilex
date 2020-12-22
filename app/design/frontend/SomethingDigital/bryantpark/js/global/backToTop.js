import $ from 'jquery';
import debounce from 'debounce';

let lastScroll = 0;

function backToTop() {
  const scrollTop = $(this).scrollTop();

  if (lastScroll >= scrollTop) {
    $('.back-to-top').addClass('fadeIn');
  } else {
    $('.back-to-top').removeClass('fadeIn');
  }

  lastScroll = scrollTop;
}

window.onscroll = debounce(backToTop, 25);
