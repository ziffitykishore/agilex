require(["jquery","jquery/jquery-migrate","slick"],function($) {
    $(document).ready(function() {
        $('.related .product-items').not('.slick-initialized').slick({
            infinite: true,
            slidesToShow: 4,
            slidesToScroll: 1,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3
                    }
                },
                {
                    breakpoint: 769,
                    settings: {
                        slidesToShow: 2,
                        dots: false,
                        arrows: false,
                        autoplay:true,
                        centerMode: true
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        dots: false,
                        arrows: false,
                        autoplay:true,
                        centerMode: true
                    }
                }]
        });
    });
});