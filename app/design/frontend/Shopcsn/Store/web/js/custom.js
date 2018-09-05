require(['jquery', 'jquery/ui'], function ($, ) {
    $(document).ready(function () {
$(".form-group  input.form-control").on("focus blur", function() {
    if ($(this).val() == "") {
        $(this)
            .parents(".form-group")
            .toggleClass("focused");
    }
});

$(".form-group select.form-control").on("focus blur", function() {
    if ($(this).val() == "") {
        $(this)
            .parents(".form-group")
            .toggleClass("focused");
    }
});
    });
});