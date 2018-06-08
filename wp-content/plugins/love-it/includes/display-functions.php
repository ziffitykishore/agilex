<?php

// outputs the love it link
function li_love_link($love_text = null) {

    global $post;

    ob_start();

    // retrieve the total love count for this item
    $love_count = li_get_love_count($post->ID);


    $love_text = is_null($love_text) ? __('Love It', 'love_it') : $love_text;
    // only show the Love It link if the user has NOT previously loved this item
    echo '<span class="love-it extras-link" data-post-id="' . esc_attr(get_the_ID()) . '" ><i class="fa fa-heart-o"></i> <span class="love-count">' . sprintf("%02d", $love_count) . '</span></span>';


    // append our "Love It" link to the item content.
    $link = ob_get_clean();
    return $link;
}

// adds the Love It link and count to post/page content automatically
//function li_display_love_link( $content ) {
//	$types = apply_filters( 'li_display_love_links_on', array( 'blog' ) );
//	if( in_category( $types ) || is_singular($types)) {
//		$content .= li_love_link();
//	}
//	return $content;
//}
//add_filter( 'the_content', 'li_display_love_link', 100 );


add_shortcode('wishlist-feed', 'getwishlistContent');
// adds the Love It link and count to post/page content automatically
function getwishlistContent( $content ) {
	$types = apply_filters( 'li_display_love_links_on', array( 'blog' ) );
	if( in_category( $types ) || is_singular($types)) {
		$content .= li_love_link();
	}
	return $content;
}