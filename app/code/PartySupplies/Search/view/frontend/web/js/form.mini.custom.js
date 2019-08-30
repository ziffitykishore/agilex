define(["jquery"], function($) {
        $('#search_mini_form').on('submit',function(e){
            e.preventDefault();
            var form_data = $(this).serialize();
            var url = $('#categories').val();
            
            if($('#search').val()) {
                urlBinderChar = url.indexOf('?') != -1 ? "&" : "?";
                url = url+urlBinderChar+form_data;                
            }                
            window.location.replace(url);
        });
    });