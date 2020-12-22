import $ from 'jquery';
import domready from 'domready';


domready(() => {
  // Autofill confirmation password with password
  $("#password").on('change', function() {
    $("#password-confirmation").val($("#password").val());
  });

  // Toggle password field between type password and text
  $('#password-toggle').click(function() {
    const $input = $('.js-toggle-type');
    const willShow = $input.attr('type') === 'password';
    let nextAttr = willShow ? 'text' : 'password';

    $(this).attr('aria-pressed', willShow);
    $input.attr('type', nextAttr);
  });
});
