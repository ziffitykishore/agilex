<?php

function li_front_end_js() {
   //  if (is_user_logged_in()) {
        wp_enqueue_script('love-it', LI_BASE_URL . '/includes/js/love-it.js',
            array('jquery'));
        wp_localize_script('love-it', 'love_it_vars',
            array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('love-it-nonce'),
            'already_loved_message' => __('You have already loved this item.', 'love_it'),
            'error_message' => __('Sorry, there was a problem processing your request.',
                'love_it')
            )
        );
   // }
}
add_action('wp_enqueue_scripts', 'li_front_end_js');