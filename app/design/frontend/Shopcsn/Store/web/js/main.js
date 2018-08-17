equire([
    'jquery',  
    'jquery/ui', 
    'jquery/validate', 
    'mage/translate' ,
    'bminjs',
    'slick'
], function ($){

//     `use strict`;
    console.log("Price Slider");
//require(["jquery" , "jquery/jquery-ui"], function($){
    // ...
    $('.filter-options-title').on('click', function(){
        $(this).toggleClass('active');
        $(this).next().toggle();
    });


    $('.overlay').on('click', function(){
        $('html').removeClass('nav-open');
        $('.body').removeClass('acc-opened cart-opened');
    });
    
    $(document).on('click', function(){
        $('.nav-sections').on('click', function(e){
            e.stopPropagation();
        });
    });
});