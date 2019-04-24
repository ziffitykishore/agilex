import $ from 'jquery';
import breakpoints from 'utils/breakpoints';
import domready from 'domready';


const $window = $(window);
// Element that will be sticky
const stickyElement = '.page-header';

/* marginClass is class of an element to which padding
   is added when entering sticky mode,
   ensuring the rest of the content doesn't jump.
*/

const marginClass = '.page-wrapper';
const className = 'page-header--sticky';
const navBreakpoint = 880;
let transitionOffset = null;
let paddingTop = 0;

const isBlacklisted = (blacklist = ['checkout-index-index']) => {
  return blacklist
    .map((className) => $('body').hasClass(className))
    .filter(Boolean)
    .length > 0;
}

const stickyHandler = () => {
  const $stickyElement = $(stickyElement);

  const getTransitionElement = () => {
    if (window.matchMedia(`(min-width: ${navBreakpoint + 1}px)`).matches) {
      return $(`.navigation`);
    } else {
      return $(`.page-main`);
    }
  };

  const getStickyElementHeight = () => {
    if ($stickyElement.length) {
      return $stickyElement.outerHeight(true);
    } else {
      return 0;
    }
  };

  const $transitionElement = getTransitionElement();

  if (!$stickyElement.hasClass(className)) {
    transitionOffset = $transitionElement.offset().top;
    paddingTop = getStickyElementHeight();
  }

  if ($window.scrollTop() > transitionOffset) {
    // Engage sticky
    $(marginClass).css('padding-top', paddingTop);
    $stickyElement.addClass(className);

    $(document).trigger('stickynav:on');
  } else {
    // Disengage sticky
    $stickyElement.removeClass(className);
    $(marginClass).css('padding-top', 0);

    $(document).trigger('stickynav:off');
  }
}

domready(() => {
    if (!isBlacklisted()) {
        stickyHandler();
        $window.on('load ready scroll pageshow touchmove resize', stickyHandler);
    }
});
