/**
 * Functionality specific to Bootstrap Canvas WP.
 *
 * Provides helper functions to enhance the theme experience.
 */

function myFunction() {
    var x = document.getElementById("primary-menu");
    if (x.className === "nav-menu") {
        x.className += " responsive";
    } else {
        x.className = "nav-menu";
    }
}
(function ($) {
  $(document).ready(function () {
    $('#searchsubmit, #commentform #submit').addClass('btn btn-default');
	$('button, html input[type="button"], input[type="reset"], input[type="submit"]').addClass('btn btn-default');
	$('input:not(button, html input[type="button"], input[type="reset"], input[type="submit"]), input[type="file"], select, textarea').addClass('form-control');
	if ($('label').parent().not('div')) {
	  $('label:not(#searchform label,#commentform label)').wrap('<div></div>');
	}
    $('table').addClass('table table-bordered');
    $('.attachment-thumbnail').addClass('thumbnail');
    $('embed-responsive-item,iframe,embed,object,video').parent().addClass('embed-responsive embed-responsive-16by9');
	$('.navbar-nav').addClass('blog-nav');
	$('.dropdown-menu > .active > a, .dropdown-menu > .active > a:hover, .dropdown-menu > .active > a:focus, .navbar-inverse .navbar-nav .open .dropdown-menu > .active > a, .navbar-inverse .navbar-nav .open .dropdown-menu > .active > a:hover, .navbar-inverse .navbar-nav .open .dropdown-menu > .active > a:focus').closest('.navbar-nav').removeClass('blog-nav');
  });

  $('.hambur-wrap').on('click', function(e) {
    $(this).toggleClass('js-hambur-active');
    $('.sidebar-push').toggleClass('js-slide-active');
    
    e.stopPropagation();
});

$('#hero-slider').slick({
    autoplay: true,
    autoplaySpeed: 10000,
    dots: true,
    fade: true,
    arrows: false
});




$('#hero-slider .slider-blk').each(function() {
    var img = $(this).find('img');
    var height = img.height();
    var img_src = img.attr('src');


    $(this).css({
        'background-image': 'url(' + img_src + ')',
        'background-repeat': 'no-repeat',
        'background-size': 'cover',
        'background-position': 'center'


    });

    img.hide();
});


$(".btn-ripple").click(function (e) {
  
    // Remove any old one
    $(".ripple").remove();
  
    // Setup
    var posX = $(this).offset().left,
        posY = $(this).offset().top,
        buttonWidth = $(this).width(),
        buttonHeight =  $(this).height();
    
    // Add the element
    $(this).prepend("<span class='ripple'></span>");
  
    
   // Make it round!
    if(buttonWidth >= buttonHeight) {
      buttonHeight = buttonWidth;
    } else {
      buttonWidth = buttonHeight; 
    }
    
    // Get the center of the element
    var x = e.pageX - posX - buttonWidth / 2;
    var y = e.pageY - posY - buttonHeight / 2;
    
   
    // Add the ripples CSS and start the animation
    $(".ripple").css({
      width: buttonWidth,
      height: buttonHeight,
      top: y + 'px',
      left: x + 'px'
    }).addClass("rippleEffect");
  });


}) (jQuery);