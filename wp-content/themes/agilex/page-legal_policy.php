<?php
/**
 * Template Name: Page legal policy
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







 <section class="tab-section " >


        <div class="container">

        

            <div class="legal-inner margin-top--70 white-bg pad-tb-50 pad-30">
            <?php
            $args = array(
                'post_type' => 'legal_policy',
                'posts_per_page' => -1,
                'post_status'     => 'publish',
                'order_by' => 'date',
                'order' => 'ASC'
            );
            $agilex_unique_query = new WP_Query($args); $i = 1; ?>
            <ul class="nav nav-tabs responsive ord-tab" id="myTabs" role="tablist">
                <?php while ($agilex_unique_query->have_posts()) {
                $agilex_unique_query->the_post();
                $slug = get_post_field( 'post_name', get_post() );  ?>
                <li role="presentation" class="<?php if($i == 1) { echo "active"; } ?> ">
                    <a href="#<?php echo $slug; ?>" class="text-uppercase btn-ripple" id="tab-link_<?php echo $i; ?>" role="tab" rel="<?php echo the_field('tab_color'); ?>" data-toggle="tab" aria-controls="<?php echo $slug; ?>" aria-expanded="true">
                        <?php echo the_Title(); $i++; ?>
                    </a>
                </li>
                <?php } ?>
            </ul>
            <div class="tab-content" id="myTabContent">
                <?php $i = 1; while ($agilex_unique_query->have_posts()) {
                        $agilex_unique_query->the_post();  $slug = get_post_field( 'post_name', get_post() );?>
                <div class="tab-pane fade <?php if($i == 1) { echo "active in"; } ?> tab-pane_<?php echo $i; ?>" role="tabpanel" id="<?php echo $slug; ?>" aria-labelledby="<?php echo $slug; ?>">
                   
                        <div class="blue-strip">
                            <div class="heading-sec">
                                <?php $file = get_field('icon_sec'); ?>
                                <?php if( $file ): ?>
                                <div class="icon-sec">
                                    <img src="<?php echo $file[url]; ?>" />
                                </div>
                                <?php endif; ?>
                                <div class="legal-heading-inner">
                                    <h2><?php echo the_Title(); ?></h2>
                                    <span>Updated on <?php the_time('F, Y') ?></span>
                                </div>
                            </div>  

                                <?php $file = get_field('file_download');

                                if( $file ): ?>
                                    <div class="download-link">
                                        <a href="<?php echo $file['url']; ?>" class="btn btn-sm btn-door btn-door_white text-uppercase" download>Download PDF</a>
                                    </div>

                                <?php endif; ?>

                            
                            
                        </div>
                        <div class="content-wrap">
                        
                            
                            <?php echo the_content(); ?>
                        </div>
                    
                </div>
            <?php  $i++; } ?>
            </div></div>
    <?php /* Restore original Post Data */
        wp_reset_postdata();?>
        </div>




     
    </section>



	  
                                                                                                                                                                                                                            
<?php get_footer(); ?>                                                                        