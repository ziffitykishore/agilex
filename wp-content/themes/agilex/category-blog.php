<?php
/**
 * A Simple Category Template
 *
 * @package Agilex
 * @since Agilex 1.0
 */
get_header(); ?>
    <?php
    $pageargs = array(

            'post_type' => 'page',
            'name' => 'blog'
        );
        $postValues = get_posts($pageargs);
    ?>
    <div class="main-banner-wrap">
        <?php foreach($postValues as $postValue): ?>
        <?php $featured_img_url = get_the_post_thumbnail_url($postValue->ID,'full'); ?>
        <div class="main-banner">
            <?php if ($featured_img_url){ ?>
                <img src="<?php echo $featured_img_url; ?>" class="" alt=""/>
            <?php } else  { ?>
                <img src="<?php bloginfo('template_directory'); ?>/images/placeholder_1920X450.png" class="" alt=""/>
            <?php }?>
        </div>
        <div class="page-header-content">
            <div class="container">
                <h1><?php echo $postValue->post_title ?></h1>
                <p><?php echo $postValue->post_excerpt ?></p>
            </div>
        </div><?php endforeach;?>
    </div>
    <!-- Loop the post of categories blog -->
    <?php get_template_part( 'loop_category', 'category' ); ?>

<?php get_footer(); ?>