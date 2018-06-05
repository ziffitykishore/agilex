jQuery(document).ready( function($) {	
    $('.love-it').on('click', function () {
        var $this = $(this);
        if ($this.hasClass('loved')) {
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
                $this.addClass('wishlist')
                var count_wrap = $this.next();
                var count = count_wrap.text();
                count_wrap.text(parseInt(count) + 1);
            } else {
                alert(love_it_vars.error_message);
            }
        });
        return false;
    });	
});