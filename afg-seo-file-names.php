<?php
/**
 * SEO File Names
 *
 * @package           SEOFileNames
 * @author            Afterglow Web Agency
 * @copyright         2021 Afterglow Web Agency
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: SEO File Names
 * Plugin URI: https://afterglow-web.agency
 * Description: Seo File Names aims to save you time and boost your SEO by automatically renaming the files you upload to the media library with SEO friendly names.
 * Version: 0.9.1
 * Author: Afterglow Web Agency
 * Author URI: https://afterglow-web.agency
 * Requires at least: 4.9.18
 * Requires PHP: 5.2.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: asf
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || exit;

if(version_compare(get_bloginfo('version'),'4.9.18', '<') ) {
    deactivate_plugins(plugin_basename(__FILE__));
    die(__('Please upgrade WordPress to version 4.9.18 or higher to use SEO File Names.','asf'));
}

define( 'AFG_ASF_PATH', plugin_dir_path( __FILE__ )  );
define( 'AFG_ASF_URL', plugin_dir_url( __FILE__ ) );
define( 'AFG_ASF_VERSION', '0.9.1' );
define( 'AFG_IS_ASF', isset($_GET['page']) && strpos($_GET['page'], 'asf-') == 0 ? true : false);

add_action( 'plugins_loaded', 'asf_init' );
add_action( 'init', 'asf_loadDomain' );
add_action( 'admin_enqueue_scripts', 'asf_adminStyle');
add_action( 'admin_enqueue_scripts', 'asf_clearUserVals');

add_action( 'plugins_loaded', 'asf_saveTagId' );
add_filter( 'wp_unique_post_slug', 'asf_preGetslug', 6, 10);

add_action( 'in_admin_header', 'asf_notices', 1000);
add_filter( 'network_admin_plugin_action_links_'.plugin_basename(__FILE__), 'asf_pluginLinks' );
add_filter( 'plugin_action_links_'.plugin_basename(__FILE__), 'asf_pluginLinks' );

add_filter( 'wp_handle_upload_prefilter', 'asf_rewriteFileName');

add_action( 'enqueue_block_editor_assets', 'asf_GutenbergScript' );
add_action( 'wp_ajax_asf_save_meta', 'asf_saveMeta' );


/**
 * Load includes
 * @hooks on 'plugins_loaded'
 * @since 0.9.0
 */
function asf_init() {
    $path = AFG_ASF_PATH . 'inc/';
    require_once $path.'class.Options.php';
    foreach ( glob( $path."*.php" ) as $file ) {
        if($file !== 'class.Options.php' ) {
            require_once $file;
        }
    }
     
}

/**
 * Load textdomain.
 * @hooks on 'init'
 * @since 0.9.0
 */
function asf_loadDomain() {
    load_plugin_textdomain( 'asf', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

/**
* Enqueues admin scripts and styles
* @hooks on 'admin_enqueue_scripts'
* @since 0.9.0
*/ 
function asf_adminStyle() {
    if(!AFG_IS_ASF) return;
    $version = rand();
    //$version = AFG_ASF_VERSION;
    wp_enqueue_style('asf-admin', AFG_ASF_URL.'assets/css/admin.css', array(), $version, 'all');
    wp_enqueue_script( 'asf-admin', AFG_ASF_URL . 'assets/js/admin.js', array('jquery','jquery-ui-accordion'), $version, 'all' );
    wp_localize_script( 'asf-admin', 'afgAjax', array(
        'ajaxurl'=> admin_url( 'admin-ajax.php' ),
        'ajax_nonce' => wp_create_nonce('asf-admin-scripts'),
    ));
}

/**
* Clear last post ajax filled datas
* if not on a Gutenberg post
* @hooks on 'admin_enqueue_scripts'
* @since 0.9.0
*/ 
function asf_clearUserVals() {
    if(!asf_isGutenbergEditor()) {
        update_option('asf_tmp_options',false);
    }
}

/**
* Plugin Links
* @hooks on '%plugin_action_links%'
* @since 0.9.0
*/
function asf_pluginLinks($links): array {
        array_unshift(
            $links,
            sprintf(
                __( '%1$sSettings%2$s', 'asf' ),
                '<a href="' . menu_page_url( 'asf-settings', false ) . '">',
                '</a>'
            )
        );
        $links[] = sprintf(
            __( '%1$sSaved time? Buy me a coffee.%2$s', 'asf' ),
            '<a href="https://www.paypal.com/biz/fund?id=6WVXD3SYG3L58" target="_blank">',
            '</a>'
        );
        return $links;
}

/**
* Remove other plugin notices on plugin page
* @hooks on 'in_admin_header'
* @since 0.9.0
*/
function asf_notices() {
    if(!AFG_IS_ASF) return;
    remove_all_actions('admin_notices');
    remove_all_actions('all_admin_notices');
}

/**
* Filename rewrite wrapper
* @hooks on 'wp_handle_upload_prefilter'
* @since 0.9.0
*/
function asf_rewriteFileName($file) {
        $filename = new asf_FileName;
        $file = $filename->rewriteFileName($file);
        return $file;
}

/**
 * Save in DB for 'wp_handle_upload_prefilter' event
 * @hooks on 'plugins_loaded'
 * @since 0.0.9
 */
function asf_saveTagId() {
    if(isset($_GET['tag_ID'])) {
        if($term = get_term($_GET['tag_ID'])) {
            $id = $term->term_id;
            update_option('asf_tmp_term',$id);
        }
    }
}

/**
 * Get Slug as soon as it exists
 * @hooks on 'wp_unique_post_slug'
 * @since 0.0.9
 */
function asf_preGetslug($slug, $postId, $postStatus, $postType, $postParent, $originalSlug) {
    update_option('asf_tmp_post',$postId);
    return $slug;
}

/**
 * Load Gutenberg Script
 * @hooks on 'enqueue_block_editor_assets'
 * @since 0.0.9
 */
function asf_GutenbergScript() {
    $version = rand();
    wp_enqueue_script( 'asf-gutenberg', AFG_ASF_URL . 'assets/js/gutenberg.js', array('wp-blocks'), $version, 'all');
    wp_localize_script( 'asf-gutenberg', 'asfAjax', array(
        'ajaxurl'=> admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce('ajax-nonce'),
    ));
}

/**
 * Ajax save latest post datas
 * @hooks on 'wp_ajax_asf_save_meta'
 * @since 0.0.9
 */
function asf_saveMeta() {
    if(isset($_POST['asf_nonce'])) {
        if( !wp_verify_nonce( $_POST['asf_nonce'], 'ajax-nonce' ) ) {
             wp_die();
        }
    }
    if(isset($_POST['asf_datas'])) {
        $array = $_POST['asf_datas'];
        $array = json_decode(str_replace('\\', '', $array));
        $options['datas'] = $array;
        update_option('asf_tmp_options',$options);
        wp_die();
    }
}

/**
* Check if current page is using Guntenberg
* @since 0.0.9
*/
function asf_isGutenbergEditor() {
    if( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) return true;
    
    global $current_screen;
    if ( ! isset( $current_screen ) ) return false;
    if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) return true;
    return false;
}

