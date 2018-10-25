<?php
/**
 * The loop that displays posts
 *
 * The loop displays the posts and the post content. See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 *
 * This can be overridden in child themes with loop.php or
 * loop-template.php, where 'template' is the loop context
 * requested by a template. For example, loop-index.php would
 * be used if it exists and we ask for the loop with:
 * <code>get_template_part( 'loop', 'index' );</code>
 *
 * @package Bootstrap Canvas WP
 * @since Bootstrap Canvas WP 1.0
 */
?>
	  <?php 
	  /* Start the Loop */
	  if (have_posts()) : while (have_posts()) : the_post();
	  
	  ?>
      <div class="blog-post" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <!-- check if the post has a Post Thumbnail assigned to it. -->
        
        <?php /* if ( !is_singular() ) : ?>
        <h2 class="blog-post-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php esc_attr_e( 'Permanent Link to ', 'bootstrapcanvaswp' ) . esc_attr( the_title_attribute() ); ?>">
        <?php the_title(); ?></a></h2>
        <?php else : ?>
        <h2 class="blog-post-title"><?php the_title(); ?></a></h2>
        <?php endif; */ ?>
        

        <?php
		/* Include the post format-specific template for the content. If you want to
		 * this in a child theme then include a file called called content-___.php
		 * (where ___ is the post format) and that will be used instead.
		 */
		get_template_part( 'content', get_post_format() ); ?>
        <?php
          $link_args = array(
            'before'           => '<ul class="pager">',
            'after'            => '</ul>',
            'next_or_number'   => 'next',
            'separator'        => '<li>',
            'nextpagelink'     => __( 'Next &rarr;', 'bootstrapcanvaswp' ),
            'previouspagelink' => __( '&larr; Previous', 'bootstrapcanvaswp' )
          );
          wp_link_pages( $link_args );
        ?>
        
        
        <?php //comments_template(); ?>
      </div><!-- /.blog-post -->
      <!--
      <?php trackback_rdf(); ?>
      -->
      <?php endwhile; ?>

      <?php
	  global $wp_query;
	  if ( $wp_query->max_num_pages > 1 ) :
	  ?>
      <nav>
        <ul class="pager">
          <li><?php next_posts_link( __( '<span class=\"meta-nav\">&larr;</span> Older posts', 'bootstrapcanvaswp' ) ); ?></li>
          <li><?php previous_posts_link( __( 'Newer posts <span class=\"meta-nav\">&rarr;</span>', 'bootstrapcanvaswp' ) ); ?></li>
        </ul>
      </nav>
      <?php endif; ?>

      <?php else : ?>
        <?php if ( current_user_can( 'edit_posts' ) ) :
			// Show a different message to a logged-in user who can add posts.
		?>
          <h2 class="center"><?php _e( 'No posts to display', 'bootstrapcanvaswp' ); ?></h2>
          <p class="center">
          <?php printf( __( 'Ready to publish your first post? <a href="%s">Get started here</a>.', 'bootstrapcanvaswp' ), admin_url( 'post-new.php' ) ); ?></p>
        <?php else :
			// Show the default message to everyone else.
		?>
          <h2 class="center"><?php _e( 'Nothing Found', 'bootstrapcanvaswp' ); ?></h2>
          <p class="center">
          <?php _e( 'Apologies, but no results were found. Perhaps searching will help find a related post.', 'bootstrapcanvaswp' ); ?></p>
		  <?php get_search_form(); ?>
        <?php endif; // end current_user_can() check ?>
      <?php endif; ?>