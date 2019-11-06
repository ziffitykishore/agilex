import "./preselect";
import $ from 'jquery';
import domready from 'domready';

domready(() => {
  if ($('.detail-block-description').find('div').length === 0) {
    $('.detail-block-description').hide();
  }
});
