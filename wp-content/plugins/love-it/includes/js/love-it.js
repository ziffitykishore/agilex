jQuery(document).ready( function($) {	
    $('.love-it').on('click', function () {
        var $this = $(this);
        if ($this.hasClass('loved') || $this.hasClass('wishlist')) {
            alert(love_it_vars.already_loved_message);
            return false;
        }
        var post_id = $this.data('post-id');
        var post_data = {
            action: 'love_it',
            item_id: post_id,
            love_it_nonce: love_it_vars.nonce
        };
        $.post(love_it_vars.ajaxurl, post_data, function (response) {
            if (response == 'loved') {
                $this.addClass('wishlist');
                $this.find('.fa').removeClass('fa-heart-o');
                $this.find('.fa').addClass('fa-heart');
                var loveCount = $this.find('.love-count');
                var count = loveCount.text();
                loveCount.text(parseInt(count) + 1);
            } else {
                alert(love_it_vars.error_message);
            }
        });
        return false;
    });	
});