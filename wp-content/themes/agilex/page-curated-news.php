<?php
/**
 * Template Name: Page What We Do
 *
 * @package Agilex
 * @since Agilex 1.0
 */
get_header(); ?>
    <?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); ?>
    <div class="main-banner" style="background: url('<?php echo $featured_img_url; ?>') no-repeat center center; background-size: cover;">
        <img src="<?php echo $featured_img_url; ?>" class="hidden" alt="" />
        <div class="page-header-content">
            <div class="container">
                <h1>
                    <?php echo the_Title(); ?>
                </h1>
                <p>
                    <?php echo wp_strip_all_tags( get_the_excerpt(), true ); ?>
                </p>
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

                    <div class="news-sec-inner">
                        <div class="news-sec-blk flex-sec border-efx effect-milo">
                            <div class="image-sec  flex-xs-100 flex-50"><div class="border-ani"></div><img src="/wp-content/uploads/2018/05/curated_news_01.jpg" alt=""/></div>
                            
                            <div class="news-content flex-xs-100 flex-50 alice-blue-bg">
                                <div class="news-title"><a href="#">Mimosa - Fragrances</a></div>
                                <div class="news-desc">
                                  <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s,</p>
                                </div>
                                <div class="news-info-details text-uppercase row">
                                    <div class="col-sm-6 news-date">Published Date: April 23, 2018</div>
                                    <div class="col-sm-6 news-author">Author: Micael Jordan</div>
                                    <div class="col-sm-12 news-website">Website: <span class="website-url">http://www.fragrance.org/fftv/</span></div>
                                </div>
                                <a href="#" class="btn btn-sm btn-blue btn-door text-uppercase">Learn More</a>
                            </div>
                            
                        </div>
                        <div class="news-sec-blk flex-sec border-efx effect-milo">
                            <div class="image-sec flex-xs-100 flex-50"><div class="border-ani"></div><img src="/wp-content/uploads/2018/05/curated_news_02.jpg" alt=""/></div>
                            
                            <div class="news-content flex-xs-100 flex-50 alice-blue-bg">
                                <div class="news-title"><a href="#">Mimosa - Fragrances</a></div>
                                <div class="news-desc">
                                  <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s,</p>
                                </div>
                                <div class="news-info-details text-uppercase row">
                                    <div class="col-sm-6 news-date">Published Date: April 23, 2018</div>
                                    <div class="col-sm-6 news-author">Author: Micael Jordan</div>
                                    <div class="col-sm-12 news-website">Website: <span class="website-url">http://www.fragrance.org/fftv/</span></div>
                                </div>
                                <a href="#" class="btn btn-sm btn-blue btn-door text-uppercase">Learn More</a>
                            </div>
                            
                        </div>
                        <div class="news-sec-blk flex-sec border-efx effect-milo">
                            <div class="image-sec flex-xs-100 flex-50"><div class="border-ani"></div><img src="/wp-content/uploads/2018/05/curated_news_03.jpg" alt=""/></div>
                            
                            <div class="news-content flex-xs-100 flex-50 alice-blue-bg">
                                <div class="news-title"><a href="#">Mimosa - Fragrances</a></div>
                                <div class="news-desc">
                                  <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s,</p>
                                </div>
                                <div class="news-info-details text-uppercase row">
                                    <div class="col-sm-6 news-date">Published Date: April 23, 2018</div>
                                    <div class="col-sm-6 news-author">Author: Micael Jordan</div>
                                    <div class="col-sm-12 news-website">Website: <span class="website-url">http://www.fragrance.org/fftv/</span></div>
                                </div>
                                <a href="#" class="btn btn-sm btn-blue btn-door text-uppercase">Learn More</a>
                            </div>
                            
                        </div>
                        <div class="news-sec-blk flex-sec border-efx effect-milo">
                            <div class="image-sec flex-xs-100 flex-50"><div class="border-ani"></div>
                              <img src="/wp-content/uploads/2018/05/curated_news_04.jpg" alt="" /></div>
                            
                            <div class="news-content flex-xs-100 flex-50 alice-blue-bg">
                                <div class="news-title"><a href="#">Mimosa - Fragrances</a></div>
                                <div class="news-desc">
                                  <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s,</p>
                                </div>
                                <div class="news-info-details text-uppercase row">
                                    <div class="col-sm-6 news-date">Published Date: April 23, 2018</div>
                                    <div class="col-sm-6 news-author">Author: Micael Jordan</div>
                                    <div class="col-sm-12 news-website">Website: <span class="website-url">http://www.fragrance.org/fftv/</span></div>
                                </div>
                                <a href="#" class="btn btn-sm btn-blue btn-door text-uppercase">Learn More</a>
                            </div>
                            
                        </div>
                        
                        
                    </div>
                    <div class="load-section text-center">
                      <a href="#" class="btn text-uppercase btn-load">Load More</a>
                    </div>
                </div>

            </div>
        </div>
      </div>
    </div>

    <?php get_footer(); ?>