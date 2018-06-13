<?php
/**
 * Bootstrap Canvas WP functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, bootstrapcanvaswp_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' );
 * function my_child_theme_setup() {
 *     // We are providing our own filter for excerpt_length (or using the unfiltered value)
 *     remove_filter( 'excerpt_length', 'bootstrapcanvaswp_excerpt_length' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package Bootstrap Canvas WP
 * @since Bootstrap Canvas WP 1.0
 */

/*
 * Set the content width based on the theme's design and stylesheet.
 *
 * Used to set the width of images and content. Should be equal to the width the theme
 * is designed for, generally via the style.css stylesheet.
 */
global $content_width;
if ( ! isset( $content_width ) ) $content_width = 900;

/* Tell WordPress to run bootstrapcanvaswp_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'bootstrapcanvaswp_setup' );

if ( ! function_exists( 'bootstrapcanvaswp_setup' ) ):
/**
 * Set up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override bootstrapcanvaswp_setup() in a child theme, add your own bootstrapcanvaswp_setup to your child theme's
 * functions.php file.
 *
 * @uses add_theme_support()        To add support for post thumbnails, custom headers and backgrounds, and automatic feed links.
 * @uses register_nav_menus()       To add support for navigation menus.
 * @uses add_editor_style()         To style the visual editor.
 * @uses load_theme_textdomain()    For translation/localization support.
 * @uses register_default_headers() To register the default custom header images provided with the theme.
 * @uses set_post_thumbnail_size()  To set a custom post thumbnail size.
 *
 * @since Bootstrap Canvas WP 1.0
 */
function bootstrapcanvaswp_setup() {
	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style( 'editor-style.css' );

	// Post Format support.
	add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat' ) );

	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );

    // Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory
	 */
	load_theme_textdomain( 'bootstrapcanvaswp', get_template_directory() . '/languages' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
            'top-menu'    => __( 'Top Menu', 'bootstrapcanvaswp' )
	) );

	// This theme allows users to set a custom background.
	$args = array(
		// Let WordPress know what our default background color is.
		'default-color' => 'fff',
	);
	add_theme_support( 'custom-background', $args );

	// The custom header business starts here.

	$args = array(
		// The height and width of our custom header.
		'width'         => '980',
		'height'        => '170',
		// Support flexible widths and heights.
		'flex-height'    => true,
		'flex-width'    => true,
		// Let WordPress know what our default text color is.
		'default-text-color'     => '333',
	);
	add_theme_support( 'custom-header', $args );

	// This feature allows themes to add document title tag to HTML <head>.
	    add_theme_support( 'title-tag' );

	// This theme allows users to set a custom logo.
    	add_theme_support( 'custom-logo', array(
		// The height and width of our custom logo.
		'height'      => 95,
		'width'       => 380,
		// Support flexible widths and heights.
		'flex-height'    => true,
		'flex-width'    => true,
	) );
}
add_action( 'after_setup_theme', 'bootstrapcanvaswp_setup' );
endif;

/**
 * Enqueue scripts and styles for front-end.
 *
 * @since Bootstrap Canvas WP 1.0
 */
function bootstrapcanvaswp_scripts() {
    wp_enqueue_style( 'blog-css', get_template_directory_uri() . '/css/blog.css' );
    wp_enqueue_style( 'bootstrap-css', get_template_directory_uri() . '/css/bootstrap.css', '3.3.0' );
    wp_enqueue_style( 'font-awesome-css', get_template_directory_uri() . '/css/font-awesome.min.css' );
    wp_enqueue_style( 'normalize-css', get_template_directory_uri() . '/css/normalize.css');


    wp_enqueue_style( 'slick-css', get_template_directory_uri() . '/css/slick.css' );
    wp_enqueue_style( 'slick-theme-css', get_template_directory_uri() . '/css/slick-theme.css' );
    wp_enqueue_style( 'animate-css', get_template_directory_uri() . '/css/animate.min.css' );
    wp_enqueue_style( 'fancy-css', get_template_directory_uri() . '/css/jquery.fancybox.css' );
    wp_enqueue_style( 'selectbox-css', get_template_directory_uri() . '/css/nice-select.css' );
    wp_enqueue_style( 'mCustomScrollbar-css', get_template_directory_uri() . '/css/jquery.mCustomScrollbar.min.css' );
    wp_enqueue_style( 'theme-css', get_template_directory_uri() . '/css/theme.css' );
    wp_enqueue_style( 'responsive-css', get_template_directory_uri() . '/css/responsive.css' );
    /* wp_enqueue_style( 'animsition-css', get_template_directory_uri() . '/css/animsition.min.css' ); */

    if ( is_rtl() ) {
        wp_enqueue_style( 'blog-rtl-css', get_template_directory_uri() . '/css/blog-rtl.css' );
        wp_enqueue_style( 'bootstrap-rtl-css', get_template_directory_uri() . '/css/bootstrap-rtl.css', '3.3.0' );
    }
    wp_enqueue_style( 'style-css', get_stylesheet_uri() );
    wp_enqueue_script( 'jquery-js', get_template_directory_uri() . '/js/jquery.js', array( 'jquery' ), true );
    wp_enqueue_script( 'bootstrap-js', get_template_directory_uri() . '/js/bootstrap.js', array( 'jquery' ), '3.3.0', true );
    wp_enqueue_script( 'html5shiv-js', get_template_directory_uri() . '/js/html5shiv.js', array( 'jquery' ), '3.7.2' );
    wp_enqueue_script( 'modernizr-js', get_template_directory_uri() . '/js/modernizr.js', array( 'jquery' ), '', true );
    wp_enqueue_script( 'masonry-js', get_template_directory_uri() . '/js/masonry.pkgd.min.js', array( 'jquery' ), '', true );
    wp_enqueue_script( 'imagesloaded-js', get_template_directory_uri() . '/js/imagesloaded.js', array( 'jquery' ), '', true );
    wp_enqueue_script( 'classie-js', get_template_directory_uri() . '/js/classie.js', array( 'jquery' ), '', true );
    wp_enqueue_script( 'AnimOnScroll-js', get_template_directory_uri() . '/js/AnimOnScroll.js', array( 'jquery' ), '', true );
    wp_enqueue_script( 'color-js', get_template_directory_uri() . '/js/jquery.color-2.1.2.min.js', array( 'jquery' ), '', true );
    wp_enqueue_script( 'ie-10-viewport-bug-workaround-js', get_template_directory_uri() . '/js/ie10-viewport-bug-workaround.js', array( 'jquery' ), '3.3.0', true );
    wp_enqueue_script( 'respond-js', get_template_directory_uri() . '/js/respond.js', array( 'jquery' ), '1.4.2' );
    wp_enqueue_script( 'slick-js', get_template_directory_uri() . '/js/slick.js', array( 'jquery' ), '', true );
    wp_enqueue_script( 'responsive-tabs-js', get_template_directory_uri() . '/js/responsive-tabs.js', array( 'jquery' ), '', true );
    wp_enqueue_script( 'select-js', get_template_directory_uri() . '/js/jquery.nice-select.min.js', array( 'jquery' ), '', true );
    wp_enqueue_script( 'wow-min-js', get_template_directory_uri() . '/js/wow.min.js', array( 'jquery' ), '', true );
    /* wp_enqueue_script( 'parallaxImg-js', get_template_directory_uri() . '/js/parallaxImg.js', array( 'jquery' ), '', true ); */
    wp_enqueue_script( 'fancybox-js', get_template_directory_uri() . '/js/jquery.fancybox.js', array( 'jquery' ), '', true );
    wp_enqueue_script( 'masonry-js', get_template_directory_uri() . '/js/masonry.pkgd.js', array( 'jquery' ), '', true );
    wp_enqueue_script( 'mousewheel-js', get_template_directory_uri() . '/js/jquery.mousewheel.js', array( 'jquery' ), '', true );
    wp_enqueue_script( 'mCustomScrollbar-js', get_template_directory_uri() . '/js/jquery.mCustomScrollbar.concat.min.js', array( 'jquery' ), '', true );
    wp_enqueue_script( 'nicescroll-js', get_template_directory_uri() . '/js/jquery.nicescroll.min.js', array( 'jquery' ), '', true );
    wp_enqueue_script( 'hover3d-js', get_template_directory_uri() . '/js/jquery.hover3d.js', array( 'jquery' ), '', true );
    wp_enqueue_script( 'TweenMax-js', get_template_directory_uri() . '/js/TweenMax.min.js', array( 'jquery' ), '', true );
/*     wp_enqueue_script( 'animsition-js', get_template_directory_uri() . '/js/jquery.animsition.min.js', array( 'jquery' ), '', true ); */

    wp_enqueue_script( 'scripts-js', get_template_directory_uri() . '/js/scripts.js', array( 'jquery' ), '', true );


}
add_action( 'wp_enqueue_scripts', 'bootstrapcanvaswp_scripts' );

/**
 * Register widgetized areas, including main sidebar and three widget-ready columns in the footer.
 *
 * To override bootstrapcanvaswp_widgets_init() in a child theme, remove the action hook and add your own
 * function tied to the init hook.
 *
 * @since Bootstrap Canvas WP 1.0
 *
 * @uses register_sidebar()
 */
function bootstrapcanvaswp_widgets_init() {
    // Area 1, located at the top of the sidebar.
    register_sidebar( array(
        'name' => __( 'Footer - Kick Start Content Area', 'bootstrapcanvaswp' ),
        'id' => 'footer-kick-area',
        'description' => __( 'Add widgets here to appear in your sidebar.', 'bootstrapcanvaswp' ),
		'before_widget' => '<div id="%1$s" class="footer-kick-module widget %2$s">',
		'after_widget'  => '</div>',
        'before_title' => '<h4>',
        'after_title' => '</h4>',
    ) );
    // Area 2, located at the top of the sidebar.
    register_sidebar( array(
        'name' => __( 'Footer - Contact Us', 'bootstrapcanvaswp' ),
        'id' => 'footer-contact-us',
        'description' => __( 'Add widgets here to appear in your sidebar.', 'bootstrapcanvaswp' ),
		'before_widget' => '<div id="%1$s" class="footer-contact-us widget %2$s">',
		'after_widget'  => '</div>',
        'before_title' => '<h4>',
        'after_title' => '</h4>',
    ) );
    // Area 3, located at the top of the sidebar.
    register_sidebar( array(
        'name' => __( 'Footer - Social Links', 'bootstrapcanvaswp' ),
        'id' => 'footer-social-links',
        'description' => __( 'Add widgets here to appear in your sidebar.', 'bootstrapcanvaswp' ),
		'before_widget' => '<div id="%1$s" class="footer-social-links widget %2$s">',
		'after_widget'  => '</div>',
        'before_title' => '<h4>',
        'after_title' => '</h4>',
    ) );
}
add_action( 'widgets_init', 'bootstrapcanvaswp_widgets_init' );

/**
 * Use get_the_excerpt() to print an excerpt by specifying a maximium number of characters.
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 *
 * @since Bootstrap Canvas WP 1.0
 *
 * @param int $charlength The number of excerpt characters.
 * @return int The filtered number of excerpt characters.
 */
function the_excerpt_max_charlength($charlength) {
	$excerpt = get_the_excerpt();
	$charlength++;

	if ( mb_strlen( $excerpt ) > $charlength ) {
		$subex = mb_substr( $excerpt, 0, $charlength - 5 );
		$exwords = explode( ' ', $subex );
		$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
		if ( $excut < 0 ) {
			echo mb_substr( $subex, 0, $excut );
		} else {
			echo $subex;
		}
		echo '[...]';
	} else {
		echo $excerpt;
	}
}



/* to hide admin bar in front view */

show_admin_bar( false );


/**
 * Contains methods for customizing the theme customization screen.
 *
 * @link http://codex.wordpress.org/Theme_Customization_API
 * @since Bootstrap Canvas WP 1.0
 */
class Bootstrap_Canvas_WP_Customize {
   /**
    * This hooks into 'customize_register' (available as of WP 3.4) and allows
    * you to add new sections and controls to the Theme Customize screen.
    *
    * Note: To enable instant preview, we have to actually write a bit of custom
    * javascript. See live_preview() for more.
    *
    * @see add_action('customize_register',$func)
    * @param \WP_Customize_Manager $wp_customize
    * @link http://ottopress.com/2012/how-to-leverage-the-theme-customizer-in-your-own-themes/
    * @since Bootstrap Canvas WP 1.0
    */
   public static function register ( $wp_customize ) {
	  //1. Define a new section (if desired) to the Theme Customizer
      $wp_customize->add_section( 'title_tagline',
         array(
            'title' => __( 'Site Title & Tagline', 'bootstrapcanvaswp' ), //Visible title of section
            'priority' => 1, //Determines what order this appears in
            'capability' => 'edit_theme_options', //Capability needed to tweak
            'description' => __('', 'bootstrapcanvaswp'), //Descriptive tooltip
         )
      );

      //1. Define a new section (if desired) to the Theme Customizer
      $wp_customize->add_section( 'bootstrapcanvaswp_copyright',
         array(
            'title' => __( 'Copyright', 'bootstrapcanvaswp' ), //Visible title of section
            'priority' => 2, //Determines what order this appears in
            'capability' => 'edit_theme_options', //Capability needed to tweak
            'description' => __('', 'bootstrapcanvaswp'), //Descriptive tooltip
         )
      );

	  //2. Register new settings to the WP database...
      $wp_customize->add_setting( 'copyrighttext', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default' => '', //Default setting/value to save
            'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			'sanitize_callback' => 'sanitize_text_field',
         )
      );

      //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Control( //Instantiate the text control class
         $wp_customize, //Pass the $wp_customize object (required)
         'bootstrapcanvaswp_copyrighttext', //Set a unique ID for the control
         array(
            'label' => __( 'Copyright', 'bootstrapcanvaswp' ), //Admin-visible name of the control
            'section' => 'bootstrapcanvaswp_copyright', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'copyrighttext', //Which setting to load and manipulate (serialized is okay)
            'priority' => 1, //Determines the order this control appears in for the specified section
			'type' => 'text',
         )
      ) );

	  //1. Define a new section (if desired) to the Theme Customizer
      $wp_customize->add_section( 'bootstrapcanvaswp_fonts',
         array(
            'title' => __( 'Fonts', 'bootstrapcanvaswp' ), //Visible title of section
            'priority' => 3, //Determines what order this appears in
            'capability' => 'edit_theme_options', //Capability needed to tweak
            'description' => __('', 'bootstrapcanvaswp'), //Descriptive tooltip
         )
      );

	  //2. Register new settings to the WP database...
      $wp_customize->add_setting( 'body_fontfamily', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default' => 'georgia, serif', //Default setting/value to save
            'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			'sanitize_callback' => 'bootstrapcanvaswp_sanitize_font_selection',
         )
      );

      //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Control( //Instantiate the text control class
         $wp_customize, //Pass the $wp_customize object (required)
         'bootstrapcanvaswp_body_fontfamily', //Set a unique ID for the control
         array(
            'label' => __( 'Text Font', 'bootstrapcanvaswp' ), //Admin-visible name of the control
            'section' => 'bootstrapcanvaswp_fonts', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'body_fontfamily', //Which setting to load and manipulate (serialized is okay)
            'priority' => 1, //Determines the order this control appears in for the specified section
			'type'     => 'select',
			'choices'  => array(
			  'arial, helvetica, sans-serif'                     => 'Arial',
			  'arial black, gadget, sans-serif'                  => 'Arial Black',
			  'comic sans ms, cursive, sans-serif'               => 'Comic Sans MS',
			  'courier new, courier, monospace'                  => 'Courier New',
			  'georgia, serif'                                   => 'Georgia',
			  'impact, charcoal, sans-serif'                     => 'Impact',
			  'lucida console, monaco, monospace'                => 'Lucida Console',
			  'lucida sans unicode, lucida grande, sans-serif'   => 'Lucida Sans Unicode',
			  'palatino linotype, book antiqua, palatino, serif' => 'Palatino Linotype',
			  'tahoma, geneva, sans-serif'                       => 'Tahoma',
			  'times new roman, times, serif'                    => 'Times New Roman',
			  'trebuchet ms, helvetica, sans-serif'              => 'Trebuchet MS',
			  'verdana, geneva, sans-serif'                      => 'Verdana',
		    )
         )
      ) );

	  //2. Register new settings to the WP database...
      $wp_customize->add_setting( 'headings_fontfamily', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default' => 'arial, helvetica, sans-serif', //Default setting/value to save
            'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			'sanitize_callback' => 'bootstrapcanvaswp_sanitize_font_selection',
         )
      );

      //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Control( //Instantiate the text control class
         $wp_customize, //Pass the $wp_customize object (required)
         'bootstrapcanvaswp_headings_fontfamily', //Set a unique ID for the control
         array(
            'label' => __( 'Headings Font', 'bootstrapcanvaswp' ), //Admin-visible name of the control
            'section' => 'bootstrapcanvaswp_fonts', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'headings_fontfamily', //Which setting to load and manipulate (serialized is okay)
            'priority' => 2, //Determines the order this control appears in for the specified section
			'type'     => 'select',
			'choices'  => array(
			  'arial, helvetica, sans-serif'                     => 'Arial',
			  'arial black, gadget, sans-serif'                  => 'Arial Black',
			  'comic sans ms, cursive, sans-serif'               => 'Comic Sans MS',
			  'courier new, courier, monospace'                  => 'Courier New',
			  'georgia, serif'                                   => 'Georgia',
			  'impact, charcoal, sans-serif'                     => 'Impact',
			  'lucida console, monaco, monospace'                => 'Lucida Console',
			  'lucida sans unicode, lucida grande, sans-serif'   => 'Lucida Sans Unicode',
			  'palatino linotype, book antiqua, palatino, serif' => 'Palatino Linotype',
			  'tahoma, geneva, sans-serif'                       => 'Tahoma',
			  'times new roman, times, serif'                    => 'Times New Roman',
			  'trebuchet ms, helvetica, sans-serif'              => 'Trebuchet MS',
			  'verdana, geneva, sans-serif'                      => 'Verdana',
		    )
         )
      ) );

	  //2. Register new settings to the WP database...
      $wp_customize->add_setting( 'menu_fontfamily', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default' => 'georgia, serif', //Default setting/value to save
            'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			'sanitize_callback' => 'bootstrapcanvaswp_sanitize_font_selection',
         )
      );

      //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Control( //Instantiate the text control class
         $wp_customize, //Pass the $wp_customize object (required)
         'bootstrapcanvaswp_menu_fontfamily', //Set a unique ID for the control
         array(
            'label' => __( 'Menu Font', 'bootstrapcanvaswp' ), //Admin-visible name of the control
            'section' => 'bootstrapcanvaswp_fonts', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'menu_fontfamily', //Which setting to load and manipulate (serialized is okay)
            'priority' => 3, //Determines the order this control appears in for the specified section
			'type'     => 'select',
			'choices'  => array(
			  'arial, helvetica, sans-serif'                     => 'Arial',
			  'arial black, gadget, sans-serif'                  => 'Arial Black',
			  'comic sans ms, cursive, sans-serif'               => 'Comic Sans MS',
			  'courier new, courier, monospace'                  => 'Courier New',
			  'georgia, serif'                                   => 'Georgia',
			  'impact, charcoal, sans-serif'                     => 'Impact',
			  'lucida console, monaco, monospace'                => 'Lucida Console',
			  'lucida sans unicode, lucida grande, sans-serif'   => 'Lucida Sans Unicode',
			  'palatino linotype, book antiqua, palatino, serif' => 'Palatino Linotype',
			  'tahoma, geneva, sans-serif'                       => 'Tahoma',
			  'times new roman, times, serif'                    => 'Times New Roman',
			  'trebuchet ms, helvetica, sans-serif'              => 'Trebuchet MS',
			  'verdana, geneva, sans-serif'                      => 'Verdana',
		    )
         )
      ) );

	  //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
         $wp_customize, //Pass the $wp_customize object (required)
         'header_textcolor', //Set a unique ID for the control
         array(
            'label' => __( 'Header Text Color', 'bootstrapcanvaswp' ), //Admin-visible name of the control
            'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'header_textcolor', //Which setting to load and manipulate (serialized is okay)
            'priority' => 1, //Determines the order this control appears in for the specified section
         )
      ) );

	  //2. Register new settings to the WP database...
      $wp_customize->add_setting( 'body_textcolor', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default' => '#555', //Default setting/value to save
            'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			'sanitize_callback' => 'sanitize_hex_color',
         )
      );

      //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
         $wp_customize, //Pass the $wp_customize object (required)
         'bootstrapcanvaswp_body_textcolor', //Set a unique ID for the control
         array(
            'label' => __( 'Text Color', 'bootstrapcanvaswp' ), //Admin-visible name of the control
            'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'body_textcolor', //Which setting to load and manipulate (serialized is okay)
            'priority' => 2, //Determines the order this control appears in for the specified section
         )
      ) );

	  //2. Register new settings to the WP database...
      $wp_customize->add_setting( 'link_textcolor', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default' => '#428bca', //Default setting/value to save
            'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			'sanitize_callback' => 'sanitize_hex_color',
         )
      );

      //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
         $wp_customize, //Pass the $wp_customize object (required)
         'bootstrapcanvaswp_link_textcolor', //Set a unique ID for the control
         array(
            'label' => __( 'Link Color', 'bootstrapcanvaswp' ), //Admin-visible name of the control
            'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'link_textcolor', //Which setting to load and manipulate (serialized is okay)
            'priority' => 3, //Determines the order this control appears in for the specified section
         )
      ) );

	  //2. Register new settings to the WP database...
      $wp_customize->add_setting( 'hover_textcolor', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default' => '#23527c', //Default setting/value to save
            'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            'transport' => 'refresh', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			'sanitize_callback' => 'sanitize_hex_color',
         )
      );

      //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
         $wp_customize, //Pass the $wp_customize object (required)
         'bootstrapcanvaswp_hover_textcolor', //Set a unique ID for the control
         array(
            'label' => __( 'Hover Link Color', 'bootstrapcanvaswp' ), //Admin-visible name of the control
            'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'hover_textcolor', //Which setting to load and manipulate (serialized is okay)
            'priority' => 4, //Determines the order this control appears in for the specified section
         )
      ) );

	  //2. Register new settings to the WP database...
      $wp_customize->add_setting( 'headings_textcolor', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default' => '#333', //Default setting/value to save
            'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			'sanitize_callback' => 'sanitize_hex_color',
         )
      );

      //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
         $wp_customize, //Pass the $wp_customize object (required)
         'bootstrapcanvaswp_headings_textcolor', //Set a unique ID for the control
         array(
            'label' => __( 'Headings Color', 'bootstrapcanvaswp' ), //Admin-visible name of the control
            'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'headings_textcolor', //Which setting to load and manipulate (serialized is okay)
            'priority' => 5, //Determines the order this control appears in for the specified section
         )
      ) );

	  //2. Register new settings to the WP database...
      $wp_customize->add_setting( 'primary_menucolor', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default' => '#428bca', //Default setting/value to save
            'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			'sanitize_callback' => 'sanitize_hex_color',
         )
      );

      //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
         $wp_customize, //Pass the $wp_customize object (required)
         'bootstrapcanvaswp_primary_menucolor', //Set a unique ID for the control
         array(
            'label' => __( 'Primary Menu Color', 'bootstrapcanvaswp' ), //Admin-visible name of the control
            'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'primary_menucolor', //Which setting to load and manipulate (serialized is okay)
            'priority' => 6, //Determines the order this control appears in for the specified section
         )
      ) );

	  //2. Register new settings to the WP database...
      $wp_customize->add_setting( 'primary_linkcolor', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default' => '#cdddeb', //Default setting/value to save
            'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			'sanitize_callback' => 'sanitize_hex_color',
         )
      );

      //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
         $wp_customize, //Pass the $wp_customize object (required)
         'bootstrapcanvaswp_primary_linkcolor', //Set a unique ID for the control
         array(
            'label' => __( 'Primary Link Color', 'bootstrapcanvaswp' ), //Admin-visible name of the control
            'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'primary_linkcolor', //Which setting to load and manipulate (serialized is okay)
            'priority' => 7, //Determines the order this control appears in for the specified section
         )
      ) );

	  //2. Register new settings to the WP database...
      $wp_customize->add_setting( 'primary_hovercolor', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default' => '#fff', //Default setting/value to save
            'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            'transport' => 'refresh', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			'sanitize_callback' => 'sanitize_hex_color',
         )
      );

      //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
         $wp_customize, //Pass the $wp_customize object (required)
         'bootstrapcanvaswp_primary_hovercolor', //Set a unique ID for the control
         array(
            'label' => __( 'Primary Hover Link Color', 'bootstrapcanvaswp' ), //Admin-visible name of the control
            'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'primary_hovercolor', //Which setting to load and manipulate (serialized is okay)
            'priority' => 8, //Determines the order this control appears in for the specified section
         )
      ) );

	  //2. Register new settings to the WP database...
      $wp_customize->add_setting( 'primary_activecolor', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default' => '#fff', //Default setting/value to save
            'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			'sanitize_callback' => 'sanitize_hex_color',
         )
      );

      //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
         $wp_customize, //Pass the $wp_customize object (required)
         'bootstrapcanvaswp_primary_activecolor', //Set a unique ID for the control
         array(
            'label' => __( 'Primary Active Link Color', 'bootstrapcanvaswp' ), //Admin-visible name of the control
            'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'primary_activecolor', //Which setting to load and manipulate (serialized is okay)
            'priority' => 9, //Determines the order this control appears in for the specified section
         )
      ) );

	  //2. Register new settings to the WP database...
      $wp_customize->add_setting( 'primary_activebackground', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default' => '#428bca', //Default setting/value to save
            'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			'sanitize_callback' => 'sanitize_hex_color',
         )
      );

      //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
         $wp_customize, //Pass the $wp_customize object (required)
         'bootstrapcanvaswp_primary_activebackground', //Set a unique ID for the control
         array(
            'label' => __( 'Primary Active Background Color', 'bootstrapcanvaswp' ), //Admin-visible name of the control
            'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'primary_activebackground', //Which setting to load and manipulate (serialized is okay)
            'priority' => 10, //Determines the order this control appears in for the specified section
         )
      ) );

	  //2. Register new settings to the WP database...
      $wp_customize->add_setting( 'dropdown_menucolor', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default' => '#fff', //Default setting/value to save
            'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			'sanitize_callback' => 'sanitize_hex_color',
         )
      );

      //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
         $wp_customize, //Pass the $wp_customize object (required)
         'bootstrapcanvaswp_dropdown_menucolor', //Set a unique ID for the control
         array(
            'label' => __( 'Dropdown Menu Color', 'bootstrapcanvaswp' ), //Admin-visible name of the control
            'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'dropdown_menucolor', //Which setting to load and manipulate (serialized is okay)
            'priority' => 11, //Determines the order this control appears in for the specified section
         )
      ) );

	  //2. Register new settings to the WP database...
      $wp_customize->add_setting( 'dropdown_linkcolor', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default' => '#333', //Default setting/value to save
            'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			'sanitize_callback' => 'sanitize_hex_color',
         )
      );

      //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
         $wp_customize, //Pass the $wp_customize object (required)
         'bootstrapcanvaswp_dropdown_linkcolor', //Set a unique ID for the control
         array(
            'label' => __( 'Dropdown Link Color', 'bootstrapcanvaswp' ), //Admin-visible name of the control
            'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'dropdown_linkcolor', //Which setting to load and manipulate (serialized is okay)
            'priority' => 12, //Determines the order this control appears in for the specified section
         )
      ) );

	  //2. Register new settings to the WP database...
      $wp_customize->add_setting( 'dropdown_hovercolor', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default' => '#333', //Default setting/value to save
            'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            'transport' => 'refresh', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			'sanitize_callback' => 'sanitize_hex_color',
         )
      );

      //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
         $wp_customize, //Pass the $wp_customize object (required)
         'bootstrapcanvaswp_dropdown_hovercolor', //Set a unique ID for the control
         array(
            'label' => __( 'Dropdown Hover Link Color', 'bootstrapcanvaswp' ), //Admin-visible name of the control
            'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'dropdown_hovercolor', //Which setting to load and manipulate (serialized is okay)
            'priority' => 13, //Determines the order this control appears in for the specified section
         )
      ) );

	  //2. Register new settings to the WP database...
      $wp_customize->add_setting( 'dropdown_hoverbackground', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default' => '#f5f5f5', //Default setting/value to save
            'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            'transport' => 'refresh', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			'sanitize_callback' => 'sanitize_hex_color',
         )
      );

      //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
         $wp_customize, //Pass the $wp_customize object (required)
         'bootstrapcanvaswp_dropdown_hoverbackground', //Set a unique ID for the control
         array(
            'label' => __( 'Dropdown Hover Background Color', 'bootstrapcanvaswp' ), //Admin-visible name of the control
            'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'dropdown_hoverbackground', //Which setting to load and manipulate (serialized is okay)
            'priority' => 14, //Determines the order this control appears in for the specified section
         )
      ) );

	  //2. Register new settings to the WP database...
      $wp_customize->add_setting( 'dropdown_activecolor', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default' => '#fff', //Default setting/value to save
            'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			'sanitize_callback' => 'sanitize_hex_color',
         )
      );

      //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
         $wp_customize, //Pass the $wp_customize object (required)
         'bootstrapcanvaswp_dropdown_activecolor', //Set a unique ID for the control
         array(
            'label' => __( 'Dropdown Active Link Color', 'bootstrapcanvaswp' ), //Admin-visible name of the control
            'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'dropdown_activecolor', //Which setting to load and manipulate (serialized is okay)
            'priority' => 15, //Determines the order this control appears in for the specified section
         )
      ) );

	  //2. Register new settings to the WP database...
      $wp_customize->add_setting( 'dropdown_activebackground', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default' => '#080808', //Default setting/value to save
            'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			'sanitize_callback' => 'sanitize_hex_color',
         )
      );

      //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
         $wp_customize, //Pass the $wp_customize object (required)
         'bootstrapcanvaswp_dropdown_activebackground', //Set a unique ID for the control
         array(
            'label' => __( 'Dropdown Active Background Color', 'bootstrapcanvaswp' ), //Admin-visible name of the control
            'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'dropdown_activebackground', //Which setting to load and manipulate (serialized is okay)
            'priority' => 16, //Determines the order this control appears in for the specified section
         )
      ) );

	  //2. Register new settings to the WP database...
      $wp_customize->add_setting( 'footer_textcolor', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default' => '#999', //Default setting/value to save
            'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			'sanitize_callback' => 'sanitize_hex_color',
         )
      );

      //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
         $wp_customize, //Pass the $wp_customize object (required)
         'bootstrapcanvaswp_footer_textcolor', //Set a unique ID for the control
         array(
            'label' => __( 'Footer Text Color', 'bootstrapcanvaswp' ), //Admin-visible name of the control
            'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'footer_textcolor', //Which setting to load and manipulate (serialized is okay)
            'priority' => 17, //Determines the order this control appears in for the specified section
         )
      ) );

	  //2. Register new settings to the WP database...
      $wp_customize->add_setting( 'footer_linkcolor', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default' => '#428bca', //Default setting/value to save
            'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			'sanitize_callback' => 'sanitize_hex_color',
         )
      );

      //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
         $wp_customize, //Pass the $wp_customize object (required)
         'bootstrapcanvaswp_footer_linkcolor', //Set a unique ID for the control
         array(
            'label' => __( 'Footer Link Color', 'bootstrapcanvaswp' ), //Admin-visible name of the control
            'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'footer_linkcolor', //Which setting to load and manipulate (serialized is okay)
            'priority' => 18, //Determines the order this control appears in for the specified section
         )
      ) );

	  //2. Register new settings to the WP database...
      $wp_customize->add_setting( 'footer_hovercolor', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default' => '#23527c', //Default setting/value to save
            'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            'transport' => 'refresh', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			'sanitize_callback' => 'sanitize_hex_color',
         )
      );

      //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
         $wp_customize, //Pass the $wp_customize object (required)
         'bootstrapcanvaswp_footer_hovercolor', //Set a unique ID for the control
         array(
            'label' => __( 'Footer Hover Link Color', 'bootstrapcanvaswp' ), //Admin-visible name of the control
            'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'footer_hovercolor', //Which setting to load and manipulate (serialized is okay)
            'priority' => 19, //Determines the order this control appears in for the specified section
         )
      ) );

	  //2. Register new settings to the WP database...
      $wp_customize->add_setting( 'footer_backgroundcolor', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default' => '#f9f9f9', //Default setting/value to save
            'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
			'sanitize_callback' => 'sanitize_hex_color',
         )
      );

      //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
         $wp_customize, //Pass the $wp_customize object (required)
         'bootstrapcanvaswp_footer_backgroundcolor', //Set a unique ID for the control
         array(
            'label' => __( 'Footer Background Color', 'bootstrapcanvaswp' ), //Admin-visible name of the control
            'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'footer_backgroundcolor', //Which setting to load and manipulate (serialized is okay)
            'priority' => 20, //Determines the order this control appears in for the specified section
         )
      ) );

	  //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
         $wp_customize, //Pass the $wp_customize object (required)
         'bootstrapcanvaswp_background_color', //Set a unique ID for the control
         array(
            'label' => __( 'Background Color', 'bootstrapcanvaswp' ), //Admin-visible name of the control
            'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'background_color', //Which setting to load and manipulate (serialized is okay)
            'priority' => 21, //Determines the order this control appears in for the specified section
         )
      ) );

      //4. We can also change built-in settings by modifying properties. For instance, let's make some stuff use live preview JS...
      $wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
      $wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
	  $wp_customize->add_setting( 'display_header_text' , array( 'default' => true, 'sanitize_callback' => 'bootstrapcanvaswp_sanitize_checkbox' ) );
	  $wp_customize->get_setting( 'display_header_text' )->transport = 'postMessage';
      $wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
      $wp_customize->get_setting( 'background_color' )->transport = 'postMessage';
   }

   /**
    * This will output the custom WordPress settings to the live theme's WP head.
    *
    * Used by hook: 'wp_head'
    *
    * @see add_action('wp_head',$func)
    * @since Bootstrap Canvas WP 1.0
    */
   public static function header_output() {
      ?>
      <!--Customizer CSS-->
      <style type="text/css">
         <?php self::generate_css('.blog-title, .blog-description', 'color', 'header_textcolor', '#'); ?>
         <?php self::generate_css('body', 'background-color', 'background_color', '#'); ?>
		 <?php self::generate_css('.blog-nav .active', 'color', 'background_color', '#'); ?>
		 <?php self::generate_css('body', 'font-family', 'body_fontfamily'); ?>
		 <?php self::generate_css('body', 'color', 'body_textcolor'); ?>
         <?php self::generate_css('a', 'color', 'link_textcolor'); ?>
		 <?php self::generate_css('a:hover, a:focus', 'color', 'hover_textcolor'); ?>
		 <?php self::generate_css('h1, .h1, h2, .h2, h3, .h3, h4, .h4, h5, .h5, h6, .h6', 'font-family', 'headings_fontfamily'); ?>
		 <?php self::generate_css('h1, .h1, h2, .h2, h3, .h3, h4, .h4, h5, .h5, h6, .h6', 'color', 'headings_textcolor'); ?>
		 <?php self::generate_css('.navbar-inverse', 'font-family', 'menu_fontfamily'); ?>
		 <?php self::generate_css('.navbar-inverse', 'background-color', 'primary_menucolor'); ?>
		 <?php self::generate_css('.navbar-inverse .navbar-brand, .navbar-inverse .navbar-nav > li > a', 'color', 'primary_linkcolor'); ?>
		 <?php self::generate_css('.navbar-inverse .navbar-nav > li > a:hover, .navbar-inverse .navbar-nav > li > a:focus', 'color', 'primary_hovercolor'); ?>
		 <?php self::generate_css('.navbar-inverse .navbar-nav > .active > a, .navbar-inverse .navbar-nav > .active > a:hover, .navbar-inverse .navbar-nav > .active > a:focus', 'color', 'primary_activecolor'); ?>
		 <?php self::generate_css('.navbar-inverse .navbar-nav > .active > a, .navbar-inverse .navbar-nav > .active > a:hover, .navbar-inverse .navbar-nav > .active > a:focus, .navbar-inverse .navbar-nav > .open > a, .navbar-inverse .navbar-nav > .open > a:hover, .navbar-inverse .navbar-nav > .open > a:focus', 'background-color', 'primary_activebackground'); ?>
		 <?php self::generate_css('.dropdown-menu', 'background-color', 'dropdown_menucolor'); ?>
		 <?php self::generate_css('.dropdown-menu > li > a, .navbar-inverse .navbar-nav .open .dropdown-menu > li > a', 'color', 'dropdown_linkcolor'); ?>
		 <?php self::generate_css('.dropdown-menu > li > a:hover, .dropdown-menu > li > a:focus, .navbar-inverse .navbar-nav .open .dropdown-menu > li > a:hover, .navbar-inverse .navbar-nav .open .dropdown-menu > li > a:focus', 'color', 'dropdown_hovercolor'); ?>
		 <?php self::generate_css('.dropdown-menu > li > a:hover, .dropdown-menu > li > a:focus, .navbar-inverse .navbar-nav .open .dropdown-menu > li > a:hover, .navbar-inverse .navbar-nav .open .dropdown-menu > li > a:focus', 'background-color', 'dropdown_hoverbackground'); ?>
		 <?php self::generate_css('.dropdown-menu > .active > a, .dropdown-menu > .active > a:hover, .dropdown-menu > .active > a:focus, .navbar-inverse .navbar-nav .open .dropdown-menu > .active > a, .navbar-inverse .navbar-nav .open .dropdown-menu > .active > a:hover, .navbar-inverse .navbar-nav .open .dropdown-menu > .active > a:focus', 'color', 'dropdown_activecolor'); ?>
		 <?php self::generate_css('.dropdown-menu > .active > a, .dropdown-menu > .active > a:hover, .dropdown-menu > .active > a:focus, .navbar-inverse .navbar-nav .open .dropdown-menu > .active > a, .navbar-inverse .navbar-nav .open .dropdown-menu > .active > a:hover, .navbar-inverse .navbar-nav .open .dropdown-menu > .active > a:focus', 'background-color', 'dropdown_activebackground'); ?>
		 <?php self::generate_css('.blog-footer', 'color', 'footer_textcolor'); ?>
		 <?php self::generate_css('.blog-footer a', 'color', 'footer_linkcolor'); ?>
		 <?php self::generate_css('.blog-footer a:hover, .blog-footer a:focus', 'color', 'footer_hovercolor'); ?>
		 <?php self::generate_css('.blog-footer', 'background-color', 'footer_backgroundcolor'); ?>
      </style>
      <!--/Customizer CSS-->
      <?php
   }

   /**
    * This outputs the javascript needed to automate the live settings preview.
    * Also keep in mind that this function isn't necessary unless your settings
    * are using 'transport'=>'postMessage' instead of the default 'transport'
    * => 'refresh'
    *
    * Used by hook: 'customize_preview_init'
    *
    * @see add_action('customize_preview_init',$func)
    * @since Bootstrap Canvas WP 1.0
    */
   public static function live_preview() {
      wp_enqueue_script(
           'bootstrapcanvaswp-themecustomizer', // Give the script a unique ID
           get_template_directory_uri() . '/js/theme-customizer.js', // Define the path to the JS file
           array(  'jquery', 'customize-preview' ), // Define dependencies
           '', // Define a version (optional)
           true // Specify whether to put in footer (leave this true)
      );
   }

    /**
     * This will generate a line of CSS for use in header output. If the setting
     * ($mod_name) has no defined value, the CSS will not be output.
     *
     * @uses get_theme_mod()
     * @param string $selector CSS selector
     * @param string $style The name of the CSS *property* to modify
     * @param string $mod_name The name of the 'theme_mod' option to fetch
     * @param string $prefix Optional. Anything that needs to be output before the CSS property
     * @param string $postfix Optional. Anything that needs to be output after the CSS property
     * @param bool $echo Optional. Whether to print directly to the page (default: true).
     * @return string Returns a single line of CSS with selectors and a property.
     * @since Bootstrap Canvas WP 1.0
     */
    public static function generate_css( $selector, $style, $mod_name, $prefix='', $postfix='', $echo=true ) {
      $return = '';
      $mod = get_theme_mod($mod_name);
      if ( ! empty( $mod ) ) {
         $return = sprintf('%s { %s:%s; }',
            $selector,
            $style,
            $prefix.$mod.$postfix
         );
         if ( $echo ) {
            echo $return;
         }
      }
      return $return;
    }
}

// Setup the Theme Customizer settings and controls...
add_action( 'customize_register' , array( 'Bootstrap_Canvas_WP_Customize' , 'register' ) );

// Output custom CSS to live site
add_action( 'wp_head' , array( 'Bootstrap_Canvas_WP_Customize' , 'header_output' ) );

// Enqueue live preview javascript in Theme Customizer admin screen
add_action( 'customize_preview_init' , array( 'Bootstrap_Canvas_WP_Customize' , 'live_preview' ) );

/**
 * Sanitize Customizer Font Selections
 *
 * @link http://codex.wordpress.org/Theme_Customization_API
 * @since Bootstrap Canvas WP 1.0
 */
function bootstrapcanvaswp_sanitize_font_selection( $input ) {
  $valid = array(
	'arial, helvetica, sans-serif'                     => 'Arial',
	'arial black, gadget, sans-serif'                  => 'Arial Black',
	'comic sans ms, cursive, sans-serif'               => 'Comic Sans MS',
	'courier new, courier, monospace'                  => 'Courier New',
	'georgia, serif'                                   => 'Georgia',
	'impact, charcoal, sans-serif'                     => 'Impact',
	'lucida console, monaco, monospace'                => 'Lucida Console',
	'lucida sans unicode, lucida grande, sans-serif'   => 'Lucida Sans Unicode',
	'palatino linotype, book antiqua, palatino, serif' => 'Palatino Linotype',
	'tahoma, geneva, sans-serif'                       => 'Tahoma',
	'times new roman, times, serif'                    => 'Times New Roman',
	'trebuchet ms, helvetica, sans-serif'              => 'Trebuchet MS',
	'verdana, geneva, sans-serif'                      => 'Verdana',
  );

  if ( array_key_exists( $input, $valid ) ) {
	return $input;
  } else {
	return '';
  }
}

/**
 * Sanitize Customizer Checkbox
 *
 * @link http://codex.wordpress.org/Theme_Customization_API
 * @since Bootstrap Canvas WP 1.0
 */
function bootstrapcanvaswp_sanitize_checkbox( $input ) {
  if ( $input == 1 ) {
	return 1;
  } else {
	return '';
  }
}

if ( ! function_exists( 'bootstrapcanvaswp_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own bootstrapcanvaswp_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Bootstrap Canvas WP 1.0
 */
function bootstrapcanvaswp_comment( $comment, $args, $depth ) {
  $GLOBALS['comment'] = $comment;
  switch ( $comment->comment_type ) :
	case 'pingback' :
	case 'trackback' :
	// Display trackbacks differently than normal comments.
  ?>
  <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
	<p><?php _e( 'Pingback:', 'bootstrapcanvaswp' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( 'Edit', 'bootstrapcanvaswp' ), '<span class="comment-meta edit-link"><span class="glyphicon glyphicon-pencil"></span> ', '</span>' ); ?></p>
  <?php
	break;
	default :
	// Proceed with normal comments.
	global $post;
  ?>
  <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
	<article id="comment-<?php comment_ID(); ?>" class="comment">
      <header class="comment-meta comment-author vcard">
        <?php
            echo get_avatar( $comment, 44 );
            printf( ' <cite><b class="fn">%1$s</b> %2$s</cite>',
                get_comment_author_link(),
                // If current post author is also comment author, make it known visually.
                ( $comment->user_id === $post->post_author ) ? '<span>' . __( 'Post author', 'bootstrapcanvaswp' ) . '</span>' : ''
            );
            printf( '<span class="glyphicon glyphicon-calendar"></span> <a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
                esc_url( get_comment_link( $comment->comment_ID ) ),
                get_comment_time( 'c' ),
                /* translators: 1: date, 2: time */
                sprintf( __( '%1$s at %2$s', 'bootstrapcanvaswp' ), get_comment_date(), get_comment_time() )
            );
        ?>
      </header><!-- .comment-meta -->

      <?php if ( '0' == $comment->comment_approved ) : ?>
        <p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'bootstrapcanvaswp' ); ?></p>
      <?php endif; ?>

      <section class="comment-content comment">
        <?php comment_text(); ?>
        <?php edit_comment_link( __( 'Edit', 'bootstrapcanvaswp' ), '<p class="comment-meta edit-link"><span class="glyphicon glyphicon-pencil"></span> ', '</p>' ); ?>
      </section><!-- .comment-content -->

      <div class="reply">
		<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'bootstrapcanvaswp' ), 'after' => ' <span>&darr;</span>', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
	  </div><!-- .reply -->
      <hr />
	</article><!-- #comment-## -->
<?php
    break;
  endswitch; // end comment_type check
}
endif;

function bootstrap_canvas_wp_menu_fallback() { ?>
    <li class="menu-item current-menu-item"><a href="<?php echo admin_url('nav-menus.php'); ?>"><?php _e( 'Add Menu', 'bootstrapcanvaswp' ); ?></a></li>
<?php }
//Banner Slider Custom Post Type
function banner_slider_init() {
    // set up Banner Slider labels
    $labels = array(
        'name' => 'Banner Slider',
        'singular_name' => 'Banner Slider',
        'add_new' => 'Add New Banner Slider',
        'add_new_item' => 'Add New Banner Slider',
        'edit_item' => 'Edit Banner Slider',
        'new_item' => 'New Banner Slider',
        'all_items' => 'All Banner Sliders',
        'view_item' => 'View Banner Slider',
        'search_items' => 'Search Banner Sliders',
        'not_found' =>  'No Banner Slider Found',
        'not_found_in_trash' => 'No Banner Slider found in Trash',
        'parent_item_colon' => '',
        'menu_name' => 'Banner Slider',
    );

    // register post type
    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'rewrite' => array('slug' => 'banner_slider'),
        'query_var' => true,
        'menu_icon' => 'dashicons-randomize',
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'author',
            'excerpt'
        )
    );
    register_post_type( 'banner_slider', $args );
}
add_action( 'init', 'banner_slider_init' );

// Makes Agilex Unique Custom Post Type
function makes_agilex_unique_init() {
    // set up Makes Agilex Unique labels
    $labels = array(
        'name' => 'Makes Agilex Unique',
        'singular_name' => 'Makes Agilex Unique',
        'add_new' => 'Add New Makes Agilex Unique',
        'add_new_item' => 'Add New Makes Agilex Unique',
        'edit_item' => 'Edit Makes Agilex Unique',
        'new_item' => 'New Makes Agilex Unique',
        'all_items' => 'All Makes Agilex Uniques',
        'view_item' => 'View Makes Agilex Unique',
        'search_items' => 'Search Makes Agilex Uniques',
        'not_found' =>  'No Makes Agilex Unique Found',
        'not_found_in_trash' => 'No Makes Agilex Unique found in Trash',
        'parent_item_colon' => '',
        'menu_name' => 'Makes Agilex Unique',
    );

    // register post type
    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'rewrite' => array('slug' => 'makes-agilex-unique'),
        'query_var' => true,
        'menu_icon' => 'dashicons-randomize',
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'author',
            'excerpt'
        )
    );
    register_post_type( 'makes_agilex_unique', $args );
}
add_action( 'init', 'makes_agilex_unique_init' );
//Increase the upload file size
@ini_set( 'upload_max_size' , '64M' );
//Increase the Execution Time
@ini_set( 'max_execution_time', '300' );

// What We Do Custom Post Type
function what_we_do_init() {
    // set up What We Do labels
    $labels = array(
        'name' => 'What We Do',
        'singular_name' => 'What We Do',
        'add_new' => 'Add New What We Do',
        'add_new_item' => 'Add New What We Do',
        'edit_item' => 'Edit What We Do',
        'new_item' => 'New What We Do',
        'all_items' => 'All What We Do',
        'view_item' => 'View What We Do',
        'search_items' => 'Search What We Do',
        'not_found' =>  'No What We Do Found',
        'not_found_in_trash' => 'No What We Do found in Trash',
        'parent_item_colon' => '',
        'menu_name' => 'What We Do',
    );

    // register post type
    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'rewrite' => array('slug' => 'what_we_do'),
        'query_var' => true,
        'menu_icon' => 'dashicons-randomize',
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'author',
            'excerpt'
        )
    );
    register_post_type( 'what_we_do', $args );
}
add_action( 'init', 'what_we_do_init' );


// Testimonial Custom Post Type
function testimonial_init() {
    // set up Testimonial labels
    $labels = array(
        'name' => 'Testimonial',
        'singular_name' => 'Testimonial',
        'add_new' => 'Add New Testimonial',
        'add_new_item' => 'Add New Testimonial',
        'edit_item' => 'Edit Testimonial',
        'new_item' => 'New Testimonial',
        'all_items' => 'All Testimonial',
        'view_item' => 'View Testimonial',
        'search_items' => 'Search Testimonial',
        'not_found' =>  'No Testimonial Found',
        'not_found_in_trash' => 'No Testimonial found in Trash',
        'parent_item_colon' => '',
        'menu_name' => 'Testimonial',
    );

    // register post type
    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'rewrite' => array('slug' => 'testimonial'),
        'query_var' => true,
        'menu_icon' => 'dashicons-randomize',
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'author',
            'excerpt'
        )
    );
    register_post_type( 'testimonial', $args );
}
add_action( 'init', 'testimonial_init' );


/**Executive Leadership */
function member_init() {
    // set up Executive Leadership labels
    $labels = array(
        'name' => 'Executive Members',
        'singular_name' => 'Executive Member',
        'add_new' => 'Add New Executive Member',
        'add_new_item' => 'Add New Executive Member',
        'edit_item' => 'Edit Executive Member',
        'new_item' => 'New Executive Member',
        'all_items' => 'All Executive Members',
        'view_item' => 'View Executive Member',
        'search_items' => 'Search Members',
        'not_found' =>  'No Member Found',
        'not_found_in_trash' => 'No Member found in Trash',
        'parent_item_colon' => '',
        'menu_name' => 'Executive Members',
    );

    // register post type
    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'rewrite' => array('slug' => 'member'),
        'query_var' => true,
        'menu_icon' => 'dashicons-randomize',
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'author',
            'excerpt'
        )
    );
    register_post_type( 'member', $args );
}
add_action( 'init', 'member_init' );



/**Affiliation  */
function affiliation_init() {
    // set up Executive Leadership labels
    $labels = array(
        'name' => 'Affiliations',
        'singular_name' => 'Affiliation',
        'add_new' => 'Add New Affiliation',
        'add_new_item' => 'Add New Affiliation',
        'edit_item' => 'Edit Affiliation',
        'new_item' => 'New Affiliation',
        'all_items' => 'All Affiliation',
        'view_item' => 'View Affiliation',
        'search_items' => 'Search Affiliation',
        'not_found' =>  'No affiliation Found',
        'not_found_in_trash' => 'No affiliation found in Trash',
        'parent_item_colon' => '',
        'menu_name' => 'Our Affiliations',
    );

    // register post type
    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'rewrite' => array('slug' => 'affiliation'),
        'query_var' => true,
        'menu_icon' => 'dashicons-randomize',
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'author',
            'excerpt'
        )
    );
    register_post_type( 'affiliation', $args );
}
add_action( 'init', 'affiliation_init' );


/**Affiliation  */
function ourHistory_init() {
    // set up Executive Leadership labels
    $labels = array(
        'name' => 'Our History',
        'singular_name' => 'History',
        'add_new' => 'Add New History',
        'add_new_item' => 'Add New History',
        'edit_item' => 'Edit History',
        'new_item' => 'New History',
        'all_items' => 'All History',
        'view_item' => 'View History',
        'search_items' => 'Search History',
        'not_found' =>  'No history Found',
        'not_found_in_trash' => 'No history found in Trash',
        'parent_item_colon' => '',
        'menu_name' => 'Our History',
    );

    // register post type
    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'rewrite' => array('slug' => 'our_history'),
        'query_var' => true,
        'menu_icon' => 'dashicons-randomize',
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'author',
            'excerpt'
        )
    );
    register_post_type( 'our_history', $args );
}
add_action( 'init', 'ourHistory_init' );

/* -----------------------------------------
 * Put excerpt meta-box before editor
 * ----------------------------------------- */
function my_add_excerpt_meta_box( $post_type ) {
    if ( in_array( $post_type, array( 'post', 'page' ) ) ) {
         add_meta_box(
            'postexcerpt', __( 'Excerpt' ), 'post_excerpt_meta_box', $post_type, 'test', // change to something other then normal, advanced or side
            'high'
        );
    }
}
add_action( 'add_meta_boxes', 'my_add_excerpt_meta_box' );

function my_run_excerpt_meta_box() {
    # Get the globals:
    global $post, $wp_meta_boxes;

    # Output the "advanced" meta boxes:
    do_meta_boxes( get_current_screen(), 'test', $post );

}

add_action( 'edit_form_after_title', 'my_run_excerpt_meta_box' );

function my_remove_normal_excerpt() { /*this added on my own*/
    remove_meta_box( 'postexcerpt' , 'post' , 'normal' );
}

add_action( 'admin_menu' , 'my_remove_normal_excerpt' );

/**
 * To make repeatable fields in a meta box
 */

add_action( 'admin_init', 'add_post_gallery' );
add_action( 'admin_head-post.php', 'print_scripts' );
add_action( 'admin_head-post-new.php', 'print_scripts' );
add_action( 'save_post', 'update_post_gallery', 10, 2 );

/**
 * Add custom Meta Box to Posts post type
 */
function add_post_gallery()
{
    add_meta_box(
        'post_gallery',
        'Custom Uploader',
        'post_gallery_options',
        'makes_agilex_unique',
        'normal',
        'core'
    );
}

/**
 * Print the Meta Box content
 */
function post_gallery_options()
{
    global $post;
    $gallery_data = get_post_meta( $post->ID, 'gallery_data', true );

    // Use nonce for verification
    wp_nonce_field( plugin_basename( __FILE__ ), 'noncename' );
?>

<div id="dynamic_form">

    <div id="field_wrap">
    <?php
    if ( isset( $gallery_data['image_url'] ) )
    {
        for( $i = 0; $i < count( $gallery_data['image_url'] ); $i++ )
        {
        ?>

        <div class="field_row">

          <div class="field_left">
            <div class="form_field">
              <label>Image URL</label>
              <input type="text"
                     class="meta_image_url"
                     name="gallery[image_url][]"
                     value="<?php esc_html_e( $gallery_data['image_url'][$i] ); ?>"
              />
            </div>
            <div class="form_field">
              <label>Description</label>
              <input type="text"
                     class="meta_image_desc"
                     name="gallery[image_desc][]"
                     value="<?php esc_html_e( $gallery_data['image_desc'][$i] ); ?>"
              />
            </div>
          </div>

          <div class="field_right image_wrap">
            <img src="<?php esc_html_e( $gallery_data['image_url'][$i] ); ?>" height="48" width="48" />
          </div>

          <div class="field_right">
            <input class="button" type="button" value="Choose File" onclick="add_image(this)" /><br />
            <input class="button" type="button" value="Remove" onclick="remove_field(this)" />
          </div>

          <div class="clear" /></div>
        </div>
        <?php
        } // endif
    } // endforeach
    ?>
    </div>

    <!-- <div style="display:none" id="master-row"> -->
    <div  id="master-row">
    <div class="field_row">
        <div class="field_left">
            <div class="form_field">
                <label>Image URL</label>
                <input class="meta_image_url" value="" type="text" name="gallery[image_url][]" />
            </div>
            <div class="form_field">
                <label>Image Link</label>
                <input class="meta_image_desc" value="" type="text" name="gallery[image_desc][]" />
            </div>
        </div>
        <div class="field_right image_wrap">
        </div>
        <div class="field_right">
            <input type="button" class="button" value="Choose File" onclick="add_image(this)" />
            <br />
            <input class="button" type="button" value="Remove" onclick="remove_field(this)" />
        </div>
        <div class="clear"></div>
    </div>
    </div>

    <!--<div id="add_field_row">
      <input class="button" type="button" value="Add Field" onclick="add_field_row();" />
    </div>-->

</div>

  <?php
}

/**
 * Print styles and scripts
 */
function print_scripts()
{
    // Check for correct post_type
    global $post;
    if( 'makes_agilex_unique' != $post->post_type )
        return;
    ?>
    <style type="text/css">
      .field_left {
        float:left;
      }

      .field_right {
        float:left;
        margin-left:10px;
      }

      .clear {
        clear:both;
      }

      #dynamic_form {
        width:580px;
      }

      #dynamic_form input[type=text] {
        width:300px;
      }

      #dynamic_form .field_row {
        border:1px solid #999;
        margin-bottom:10px;
        padding:10px;
      }

      #dynamic_form label {
        padding:0 6px;
      }
    </style>

    <script type="text/javascript">
        function add_image(obj) {
            var parent=jQuery(obj).parent().parent('div.field_row');
            var inputField = jQuery(parent).find("input.meta_image_url");

            tb_show('', 'media-upload.php?TB_iframe=true');

            window.send_to_editor = function(html) {
                var url = jQuery(html).find('img').attr('src');
                inputField.val(url);
                jQuery(parent)
                .find("div.image_wrap")
                .html('<img src="'+url+'" height="48" width="48" />');

                // inputField.closest('p').prev('.awdMetaImage').html('<img height=120 width=120 src="'+url+'"/><p>URL: '+ url + '</p>');

                tb_remove();
            };

            return false;
        }

        function remove_field(obj) {
            var parent=jQuery(obj).parent().parent();
            //console.log(parent)
            parent.remove();
        }

        function add_field_row() {
            var row = jQuery('#master-row').html();
            jQuery(row).appendTo('#field_wrap');
        }
    </script>
    <?php
}

/**
 * Save post action, process fields
 */
function update_post_gallery( $post_id, $post_object )
{
    // Doing revision, exit earlier **can be removed**
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

    // Doing revision, exit earlier
    if ( 'revision' == $post_object->post_type )
        return;

    // Verify authenticity
    if ( !wp_verify_nonce( $_POST['noncename'], plugin_basename( __FILE__ ) ) )
        return;

    // Correct post type
    if ( 'makes_agilex_unique' != $_POST['post_type'] )
        return;

    if ( $_POST['gallery'] )
    {
        // Build array for saving post meta
        $gallery_data = array();
        for ($i = 0; $i < count( $_POST['gallery']['image_url'] ); $i++ )
        {
            if ( '' != $_POST['gallery']['image_url'][ $i ] )
            {
                $gallery_data['image_url'][]  = $_POST['gallery']['image_url'][ $i ];
                $gallery_data['image_desc'][] = $_POST['gallery']['image_desc'][ $i ];
            }
        }

        if ( $gallery_data )
            update_post_meta( $post_id, 'gallery_data', $gallery_data );
        else
            delete_post_meta( $post_id, 'gallery_data' );
    }
    // Nothing received, all fields are empty, delete option
    else
    {
        delete_post_meta( $post_id, 'gallery_data' );
    }
}


function m1_customize_register( $wp_customize ) {
    $wp_customize->add_setting( 'm1_logo' ); // Add setting for logo uploader

    // Add control for logo uploader (actual uploader)
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'm1_logo', array(
        'label'    => __( 'White Logo', 'm1' ),
        'section'  => 'title_tagline',
        'settings' => 'm1_logo',
    ) ) );
}
add_action( 'customize_register', 'm1_customize_register' );

function fav_customize_register( $wp_customize ) {
    $wp_customize->add_setting( 'fav_icon' ); // Add setting for logo uploader

    // Add control for logo uploader (actual uploader)
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'fav_icon', array(
        'label'    => __( 'Fav Icon', 'm1' ),
        'section'  => 'title_tagline',
        'settings' => 'fav_icon',
    ) ) );
}
add_action( 'customize_register', 'fav_customize_register' );


function menu_customize_register( $wp_customize ) {
    $wp_customize->add_setting( 'menu_bg' ); // Add setting for logo uploader

    // Add control for logo uploader (actual uploader)
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'menu_bg', array(
        'label'    => __( 'Main Menu Pattern', 'm1' ),
        'section'  => 'header_image',
        'settings' => 'menu_bg',
    ) ) );
}
add_action( 'customize_register', 'menu_customize_register' );


/**
 * Custom Search Form of Blog section
 * @param string $form
 * @param string $value
 * @param string $post_type
 * @return string
 */
function customSearchForm( $form, $value = "Search", $post_type = 'post', $cat = null ) {
    $form_value = (isset($value)) ? $value : attribute_escape(apply_filters('the_search_query', get_search_query()));
    $form = '<form method="get" id="searchform" autocomplete="off" action="' . get_option('home') . '/" >
    <div class="input-group">
        <input type="hidden" name="post_type" value="'.$post_type.'" />
        <input type="hidden" name="cat" value="'.$cat.'" />
        <input type="text" class="search_input" value="" name="s" id="s" placeholder="Search Blog" />
        <span class="input-group-btn">
            <button type="submit" id="searchsubmit"><i class="fa fa-search"></i></button>
        </span>
    </div>
    </form>';
    return $form;
}


// Register the three useful image sizes for use in Add Media modal
add_filter( 'image_size_names_choose', 'wpshout_custom_sizes' );
function wpshout_custom_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'medium-width' => __( 'Medium Width' ),
        'medium-height' => __( 'Medium Height' ),
        'medium-something' => __( 'Medium Something' ),
    ) );
}

add_image_size( 'featured-small', 50, 50, true );



function customArchievesLink($cat_id, $args = '') {
    global $wpdb, $wp_locale;
    $defaults = array(
        'type' => 'monthly', 'limit' => '',
	'format' => 'html', 'before' => '',
	'after' => '', 'show_post_count' => false,
	'echo' => 1, 'order' => 'DESC',
	'post_type' => 'post'
    );
    $r = wp_parse_args( $args, $defaults );
    $post_type_object = get_post_type_object( $r['post_type'] );
    if (!is_post_type_viewable($post_type_object)) {
        return;
    }
    $r['post_type'] = $post_type_object->name;

    if ('' == $r['type']) {
        $r['type'] = 'monthly';
    }

    if (!empty($r['limit'])) {
        $r['limit'] = absint($r['limit']);
        $r['limit'] = ' LIMIT ' . $r['limit'];
    }

    $order = strtoupper($r['order']);
    if ($order !== 'ASC') {
        $order = 'DESC';
    }
    $sql_where = $wpdb->prepare( "WHERE post_type = %s AND post_status = 'publish'", $r['post_type']);
    /**
     * Filters the SQL WHERE clause for retrieving archives.
     *
     * @since 2.2.0
     *
     * @param string $sql_where Portion of SQL query containing the WHERE clause.
     * @param array  $r         An array of default arguments.
     */
    $where = apply_filters('getarchives_where', $sql_where, $r);

    /**
     * Filters the SQL JOIN clause for retrieving archives.
     *
     * @since 2.2.0
     *
     * @param string $sql_join Portion of SQL query containing JOIN clause.
     * @param array  $r        An array of default arguments.
     */
    $join = apply_filters('getarchives_join', '', $r);
    $output = '';
    $limit = $r['limit'];
    $query = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, count(ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date $order $limit";
    $results = $wpdb->get_results($query);
    if ($results) {
        $after = $r['after'];
        foreach ((array) $results as $result) {
            $url = get_month_link($result->year, $result->month);
            if ('post' !== $r['post_type']) {
                $url = add_query_arg('post_type', $r['post_type'], $url);
            }
            /* translators: 1: month name, 2: 4-digit year */
            $text = sprintf(__('%1$s %2$d'), $wp_locale->get_month($result->month), $result->year);
            if ($r['show_post_count']) {
                $r['after'] = '&nbsp;(' . $result->posts . ')' . $after;
            }
            $url = $url.'?cat='.$cat_id;
            $output .= get_archives_link($url, $text, $r['format'], $r['before'], $r['after']);
        }
    }
    return $output;
}





function highlight_results($text){
    if(is_search()){
		$keys = implode('|', explode(' ', get_search_query()));
		$text = preg_replace('/(' . $keys .')/iu', '<span class="search-highlight">\0</span>', $text);
    }
    return $text;
}
add_filter('the_content', 'highlight_results');
add_filter('the_excerpt', 'highlight_results');
add_filter('the_title', 'highlight_results');
 
function highlight_results_css() {
	?>
	<style>
	.search-highlight { background-color:#FF0; font-weight:bold; }
	</style>
	<?php
}
add_action('wp_head','highlight_results_css');



add_filter('the_content', 'highlight_results');
add_filter('the_excerpt', 'highlight_results');
add_filter('the_title', 'highlight_results');