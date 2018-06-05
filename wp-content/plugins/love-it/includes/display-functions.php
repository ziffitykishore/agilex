<?php

// outputs the love it link
function li_love_link($love_text = null) {

    global $post;

    ob_start();

    // retrieve the total love count for this item
    $love_count = li_get_love_count($post->ID);

    // our wrapper DIV
    echo '<div class="love-it-wrapper">';

    $love_text = is_null($love_text) ? __('Love It', 'love_it') : $love_text;
    // only show the Love It link if the user has NOT previously loved this item
    echo '<a href="#" class="love-it" data-post-id="' . esc_attr(get_the_ID()) . '" >' . $love_text . '</a> (<span class="love-count">' . $love_count . '</span>)';

    // close our wrapper DIV
    echo '</div>';

    // append our "Love It" link to the item content.
    $link = ob_get_clean();
    return $link;
}

// adds the Love It link and count to post/page content automatically
function li_display_love_link( $content ) {

	$types = apply_filters( 'li_display_love_links_on', array( 'blog' ) );
	if( is_category( $types ) ) {
		$content .= li_love_link();
	}
	return $content;
}
add_filter( 'the_content', 'li_display_love_link', 100 );