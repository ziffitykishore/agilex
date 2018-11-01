import $ from 'jquery';
import peekaboo from 'peekaboo-toggle'
import domready from 'domready';
import breakpoints from 'utils/breakpoints';

const collapsible = () => {
  domready(() => {
    $('.block-collapsible-nav-title').peekaboo();

    window.require(['matchMedia'], mediaCheck => {
      mediaCheck({
        // that's (min-width: ($screen__m + 1px)), as defined in _variables.scss
        media: `(min-width: ${breakpoints.screen__m + 1}px)`,
        entry() {
          $('.block-collapsible-nav-title[aria-expanded="false"]').trigger('click');
        },
        exit() {
          $('.block-collapsible-nav-title[aria-expanded="true"]').trigger('click');
        },
      });
    });
  });
};

export default collapsible;
