import $ from 'jquery';
import domready from 'domready';

const showStaticBlockSubmenu = () => {
  const $levelTop = $('.level-top.parent');
  const showSubmenuClassDesktop = 'desktop-show-submenu';
  const showSubmenuSpeed = 300;

  // Desktop
  $levelTop.mouseenter(function() {
    const staticBlockSubmenu = $(this).find('.static-block-submenu');

    if (staticBlockSubmenu.length == 1) {
      setTimeout(() => staticBlockSubmenu.addClass(showSubmenuClassDesktop), showSubmenuSpeed);
    }
  });

  $levelTop.mouseleave(() => {
    setTimeout(() => $(`.${showSubmenuClassDesktop}`).removeClass(showSubmenuClassDesktop), showSubmenuSpeed);
  });

  // Mobile
  $levelTop.click(function(e) {
    const staticBlockSubmenu = $(this).find('.static-block-submenu');

    if (staticBlockSubmenu.length == 1) {
      e.preventDefault();
      e.stopPropagation();
      staticBlockSubmenu.toggleClass('mobile-show-submenu');
      $(this).find('a[role="menuitem"]').toggleClass('ui-state-active');
    }
  });
}

domready(showStaticBlockSubmenu);
