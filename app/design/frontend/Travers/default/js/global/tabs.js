import $ from 'jquery';
import domready from 'domready';

const activeClass = 'ui-state-active';

const deselectAll = () => {
  $('.tab-header').removeClass(activeClass);
  $('.tabs-content [data-content-type="tab-item"]').hide();
}

const select = tab => {
  const tabItemId = tab.attr('href'); // matches the corresponding content's id
  const tabContent = $(`.tabs-content ${tabItemId}`);
  tab.parents('li').addClass(activeClass);
  tabContent.show();
} 

$('.tab-header a').on('click', function(e) {
  e.preventDefault();
  deselectAll();
  select($(this));
});

domready(() => {
  $('.tab-header:first-of-type a').click(); // select the first tab
});
