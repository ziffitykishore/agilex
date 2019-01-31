<?php
/**
 * Template Name: Page Curated News
 *
 * @package Agilex
 * @since Agilex 1.0
 */
get_header(); ?>
<div class="main-banner-wrap">
<?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full');
        $thumbnail_ID = get_post_thumbnail_id( get_the_ID() );
        $alt_text = get_post_meta( $thumbnail_ID, '_wp_attachment_image_alt', true ); ?>       
        <div class="main-banner bg-image" data-src="<?php echo $featured_img_url; ?>">
                <?php if($featured_img_url){?>
                <img src="<?php bloginfo('template_directory'); ?>/images/blog-header.jpg" data-src="<?php echo $featured_img_url; ?>" class="lazy" alt="<?php echo $alt_text; ?>"/>
                <?php } else  { ?>
                <img src="<?php bloginfo('template_directory'); ?>/images/blog-header.jpg" class="" alt="<?php echo the_Title(); ?>"/>
                <?php }?>
            </div>
        <div class="page-header-content">
       <div class="container">
         <h1><?php echo the_Title(); ?></h1>
         <p><?php echo wp_strip_all_tags( get_the_excerpt(), true ); ?></p>
        </div>

 </div>
        </div>


    <div class="curated-news-wrap" id="curated-news">
        <div class="container">
            <div class="margin-top--70 pad-70 white-bg curated-news-inner">
                <div class="quotes-sec alice-blue-bg  text-center">
                    <div class="heading bor-bot">
                        <h2 class="heading-title"><?php echo wprss_get_general_setting( 'quote-heading' ); ?></h2>
                    </div>
                    <div class="quotes-content-inner">
                        <div class="quotes-content">
                            <p><?php echo wprss_get_general_setting( 'quote-content' ) ?></p>
                        </div>
                        <div class="quote-author"><?php echo wprss_get_general_setting( 'quote-author' ) ?></div>
                    </div>
                </div>
                <div class="news-sec">
                    <div class="heading-strip well-lg text-uppercase text-center">
                    <?php echo wprss_get_general_setting( 'feed-heading' ); ?>
                    </div>

                    <?php /** Rss Aggregator **/
                        $rssFeeds = do_shortcode('[wp-rss-aggregator]');

                        if($rssFeeds){
                        echo $rssFeeds;
                        }
                    ?>
                    <?php if($rssFeeds){ ?>
                    <div class="page-load-status load-section text-center text-uppercase">
                      <p class="infinite-scroll-request">Loading...</p>
                      <p class="infinite-scroll-last">End of content</p>
                      <p class="infinite-scroll-error">No more pages to load</p>
                    </div>
                    <?php }?>
            </div>
        </div>
      </div>
    </div>
    <?php get_footer(); ?>