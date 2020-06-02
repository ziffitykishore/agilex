define([
    'uiComponent',
    'jquery',
    'ko',
    'slick'
], function (
    component,$,ko
) {
    return component.extend({

        feed : ko.observableArray([]),

        initialize: function () {
            var self = this;
            self._super();
            $.ajax({
                url: self.api_endpoint,
                data: {
                    access_token: self.access_token,
                    fields : 'id,caption,media_url,permalink'
                },
                dataType: "json",
                type: "GET",
                success: function (data) {
                    $.each(data.data ,function(key,value){
                        self.feed.push(value);
                    });

                    var insta_set = {
                        arrows: false,
                        slidesToShow: 2,
                        mobileFirst: true,
            
                        responsive: [
                            {
                                breakpoint: 992,
                                settings: 'unslick'
                            },
                            {
                                breakpoint: 768,
                                settings: {
                                    slidesToShow: 4
                                }
                            },
                            {
                                breakpoint: 480,
                                settings: {
                                    slidesToShow: 3
                                }
                            }
                        ]
                    };
                    $('.home-instagram ul').slick(insta_set);
                },
                error: function (data) {
                    console.log(data);
                }
            })
        }
    })
});
