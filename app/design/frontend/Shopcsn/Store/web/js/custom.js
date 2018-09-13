require(['jquery', 'jquery/ui'], function($, ui) {
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
l