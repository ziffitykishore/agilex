<?php
/**
 * Template Name: Page What We Do
 *
 * @package Agilex
 * @since Agilex 1.0
 */
get_header(); ?>
<script src="<?php echo get_template_directory_uri(); ?>/js/infinite-scroll.pkgd.min.js"></script>
<div class="main-banner-wrap">
<?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>
<div class="main-banner">
        <?php if ($featured_img_url){ ?>
        <img src="<?php echo $featured_img_url; ?>" class="" alt=""/>
        <?php } else  { ?>
          <img src="<?php bloginfo('template_directory'); ?>/images/placeholder_1920X450.png" class="" alt=""/>
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
                        <div class="heading-title">Quote of the week</div>
                    </div>
                    <div class="quotes-content-inner">
                        <div class="quotes-content">
                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type</p>
                        </div>
                        <div class="quote-author">Smith</div>
                    </div>
                </div>
                <div class="news-sec">
                    <div class="heading-strip well-lg text-uppercase text-center">
                        Things happening this week
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

    <script type="text/javascript" charset="utf-8">
        var postPage = jQuery("#post_per_page").val();
        var publishedPost = jQuery("#published_posts").val();
        var pageUrl = <?php echo $_SERVER['REQUEST_URI']; ?>;
        jQuery('.curated-container').infiniteScroll({
            // options
            path: function() {
                if (publishedPost < 15 ||
                        (Math.round(publishedPost/postPage) + 1) == parseInt(this.loadCount + 2)  ) {
                    return false;
                }
                if (jQuery().niceScroll) {
                    jQuery("html.no-touch").getNiceScroll().hide();
                }
                jQuery('html, body').css({overflow: 'visible'});
                var pageNumber = ( this.loadCount + 2 );
                return pageUrl+'page/' + pageNumber;
            },
            status: '.page-load-status',
            append: '.curated-content',
            history: false,
        });


    </script>
    <?php get_footer(); ?>