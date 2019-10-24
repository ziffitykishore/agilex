import $ from 'jquery';
import domready from 'domready';

const showStaticBlockSubmenu = () => {
  const $levelTop = $('.level-top.parent');
  const showSubmenuClass = 'show-submenu';
  const showSubmenuSpeed = 300;

  $levelTop.mouseenter(function () {
    const staticBlockSubmenu = $(this).find('.static-block-submenu');

    if (staticBlockSubmenu) {
      setTimeout(() => staticBlockSubmenu.addClass(showSubmenuClass), showSubmenuSpeed);
    }
  });

  $levelTop.mouseleave(() => {
    setTimeout(() => $(`.${showSubmenuClass}`).removeClass(showSubmenuClass), showSubmenuSpeed);
  });
}

domready(showStaticBlockSubmenu);
