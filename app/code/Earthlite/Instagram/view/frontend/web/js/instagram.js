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
                    fields : 'id,caption,media_url,permalink',
                    limit : parseInt(self.limit)
                },
                dataType: "json",
                type: "GET",
                success: function (data) {
                    $.each(data.data ,function(key,value){
                        self.feed.push(value);
                    });

                    if(self.slick && $('.home-instagram #scroller li').length) {
                        $('.home-instagram #scroller').slick({
                            arrows: false,
                            slidesToShow: 6,
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
                        });
                    }
                },
                error: function (data) {
                    console.log(data);
                }
            })
        }
    })
});
