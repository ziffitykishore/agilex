import $ from 'jquery';
import peekaboo from 'peekaboo-toggle';
import domready from 'domready';
import breakpoints from 'utils/breakpoints';

// TODO: adjust collapsible.js to support this
domready(() => {
  $('.layered-nav-toggle').peekaboo();

  window.require(['matchMedia'], function(mediaCheck) {
    mediaCheck({
      media: `(min-width: ${breakpoints.screen__m + 1}px)`,
      entry: function () {
        $('.layered-nav-toggle[aria-expanded="false"], .filter-title.toggle-content > button[aria-expanded="false"]').trigger('click');
      },
      exit: function () {
        $('.layered-nav-toggle[aria-expanded="true"], .filter-title.toggle-content > button[aria-expanded="true"]').trigger('click');
      }
    });
  });
});
