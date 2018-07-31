<?php

// increments a love count
function li_mark_post_as_loved($post_id) {
    // retrieve the love count for $post_id
    $love_count = get_post_meta($post_id, '_li_love_count', true);
    if ($love_count) {
        $love_count = $love_count + 1;
    } else {
        $love_count = 1;
    }
    update_post_meta($post_id, '_li_love_count', $love_count);
    return true;
}

// returns a love count for a post
function li_get_love_count($post_id) {
    $love_count = get_post_meta($post_id, '_li_love_count', true);
    if ($love_count) {
        return $love_count;
    }
    return 0;
}

// processes the ajax request
function li_process_love() {
    if (isset($_POST['item_id']) && wp_verify_nonce($_POST['love_it_nonce'], 'love-it-nonce')) {
        if (li_mark_post_as_loved($_POST['item_id'])) {
            echo 'loved';
        } else {
            echo 'failed';
        }
    }
    die();
}
add_action('wp_ajax_nopriv_love_it', 'li_process_love');