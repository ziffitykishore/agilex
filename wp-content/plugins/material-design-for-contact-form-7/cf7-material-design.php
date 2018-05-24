<?php

/**
 *
 * @link              https://profiles.wordpress.org/gusruss89/
 * @since             1.0.0
 * @package           Cf7_Material_Design
 *
 * @wordpress-plugin
 * Plugin Name:       Material Design for Contact Form 7
 * Plugin URI:        http://cf7materialdesign.com
 * Description:       Add Google's Material Design to your Contact Form 7 forms
 * Version:           1.7.4
 * Author:            Angus Russell
 * Author URI:        https://profiles.wordpress.org/gusruss89/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cf7-material-design
 * Domain Path:       /languages
 */
// don't load directly
if ( !defined( 'ABSPATH' ) ) {
    die( '-1' );
}
// Wrap the entire main plugin file in this check

if ( !function_exists( 'cf7md_fs' ) ) {
    /**
     * Set constants
     */
    define( 'CF7MD_VER', '1.7.4' );
    define( 'CF7MD_UPDATE_MESSAGE', '1' );
    // Increment this every time a release is made that has a 'new features' message on the plugin page
    define( 'CF7MD_UPGRADE_COST', '$1.67/month' );
    define( 'CF7MD_LIVE_PREVIEW_PLUGIN_SLUG', 'cf7-live-preview' );
    // Create a helper function for easy SDK access.
    function cf7md_fs()
    {
        global  $cf7md_fs ;
        
        if ( !isset( $cf7md_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $cf7md_fs = fs_dynamic_init( array(
                'id'             => '771',
                'slug'           => 'material-design-for-contact-form-7',
                'type'           => 'plugin',
                'public_key'     => 'pk_cd33f9241475d1c70aadf00a1710b',
                'is_premium'     => false,
                'has_addons'     => false,
                'has_paid_plans' => true,
                'menu'           => array(
                'slug'       => 'cf7md',
                'first-path' => 'admin.php?page=cf7md',
                'contact'    => false,
                'support'    => false,
                'parent'     => array(
                'slug' => 'wpcf7',
            ),
            ),
                'is_live'        => true,
            ) );
        }
        
        return $cf7md_fs;
    }
    
    // Init Freemius.
    cf7md_fs();
    // Signal that SDK was initiated.
    do_action( 'cf7md_fs_loaded' );
    /**
     * Contact form 7 dependency
     */
    require_once plugin_dir_path( __FILE__ ) . 'admin/class-tgm-plugin-activation.php';
    add_action( 'tgmpa_register', 'cf7md_register_required_plugins' );
    function cf7md_register_required_plugins()
    {
        $plugins = array( array(
            'name'     => 'Contact Form 7',
            'slug'     => 'contact-form-7',
            'required' => true,
        ), array(
            'name'     => 'Contact Form 7 Live Preview',
            'slug'     => CF7MD_LIVE_PREVIEW_PLUGIN_SLUG,
            'required' => false,
        ) );
        $config = array(
            'id'           => 'cf7md',
            'default_path' => '',
            'menu'         => 'tgmpa-install-plugins',
            'parent_slug'  => 'plugins.php',
            'capability'   => 'manage_options',
            'has_notices'  => true,
            'dismissable'  => true,
            'dismiss_msg'  => '',
            'is_automatic' => false,
            'message'      => '',
            'strings'      => array(
            'page_title'                     => __( 'Install Required Plugins', 'cf7md' ),
            'menu_title'                     => __( 'Install Plugins', 'cf7md' ),
            'notice_can_install_required'    => _n_noop(
            /* translators: 1: plugin name(s). */
            'Material Design for Contact Form 7 requires the following plugin: %1$s.',
            'Material Design for Contact Form 7 requires the following plugins: %1$s.',
            'cf7md'
        ),
            'notice_can_install_recommended' => _n_noop(
            /* translators: 1: plugin name(s). */
            'Material Design for Contact Form 7 recommends the following plugin: %1$s.',
            'Material Design for Contact Form 7 recommends the following plugins: %1$s.',
            'cf7md'
        ),
            'notice_ask_to_update'           => _n_noop(
            /* translators: 1: plugin name(s). */
            'The following plugin needs to be updated to its latest version to ensure maximum compatibility with Material Design for Contact Form 7: %1$s.',
            'The following plugins need to be updated to their latest version to ensure maximum compatibility with this Material Design for Contact Form 7: %1$s.',
            'cf7md'
        ),
        ),
        );
        tgmpa( $plugins, $config );
    }
    
    /**
     * Require files
     */
    require_once plugin_dir_path( __FILE__ ) . 'activate.php';
    require_once plugin_dir_path( __FILE__ ) . 'public/cf7-material-design-public.php';
    require_once plugin_dir_path( __FILE__ ) . 'public/cf7-material-design-custom-style.php';
    require_once plugin_dir_path( __FILE__ ) . 'admin/cf7-material-design-admin.php';
    require_once plugin_dir_path( __FILE__ ) . 'admin/cf7-material-design-page.php';
    require_once plugin_dir_path( __FILE__ ) . 'admin/cf7-material-design-customizer.php';
}
