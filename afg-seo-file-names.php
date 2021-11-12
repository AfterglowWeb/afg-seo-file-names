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
 * Plugin URI: https://afterglow-web.agency/en/seo-file-names/
 * Description: SEO File Names aims to save you time and boost your SEO by automatically renaming the files you upload to the media library with SEO friendly names.
 * Version: 0.9.3
 * Author: Afterglow Web Agency
 * Author URI: https://afterglow-web.agency
 * Requires at least: 4.9.18
 * Requires PHP: 5.3
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: seo-file-names
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || exit;

if(version_compare(get_bloginfo('version'),'4.9.18', '<') ) {
    deactivate_plugins(plugin_basename(__FILE__));
    die(__('Please upgrade WordPress to version 4.9.18 or higher to use SEO File Names.','seo-file-names'));
}

define( 'AFG_ASF_PATH', plugin_dir_path( __FILE__ )  );
define( 'AFG_ASF_URL', plugin_dir_url( __FILE__ ) );
define( 'AFG_ASF_VERSION', '0.9.3' );
define( 'AFG_IS_ASF', isset($_GET['page']) && strpos($_GET['page'], 'asf-') == 0 ? true : false);

add_action( 'plugins_loaded', 'asf_init' );
add_action( 'init', 'asf_loadDomain' );
add_action( 'admin_enqueue_scripts', 'asf_adminStyle');
add_action( 'admin_enqueue_scripts', 'asf_clearUserVals');
add_action( 'in_admin_header', 'asf_notices', 1000);
add_filter( 'network_admin_plugin_action_links_'.plugin_basename(__FILE__), 'asf_pluginLinks' );
add_filter( 'plugin_action_links_'.plugin_basename(__FILE__), 'asf_pluginLinks' );
add_action( 'wp_ajax_asf_save_meta', 'asf_saveMeta' );
add_action( 'admin_enqueue_scripts', 'asf_classicEditorScript');
add_action( 'plugins_loaded', 'asf_saveTagId' );
add_filter( 'wp_unique_post_slug', 'asf_preGetslug', 6, 10);
add_filter( 'wp_handle_upload_prefilter', 'asf_rewriteFileName');
add_action( 'enqueue_block_editor_assets', 'asf_GutenbergScript' );

/**
 * Load PHP includes from /inc/
 * @hooks on 'plugins_loaded'
 * 
 * @since 0.9.0
 * 
 * @return void
 */
function asf_init() {
    require_once realpath(AFG_ASF_PATH . 'inc/class.Sanitize.php');
    require_once realpath(AFG_ASF_PATH . 'inc/class.Options.php');
    require_once realpath(AFG_ASF_PATH . 'inc/class.OptionPage.php');
    require_once realpath(AFG_ASF_PATH . 'inc/class.FileName.php');      
}

/**
 * Load plugin textdomain
 * @hooks on 'init'
 * 
 * @since 0.9.0
 * 
 * @return void
 */
function asf_loadDomain() {
    load_plugin_textdomain( 'seo-file-names', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

/**
* Enqueues admin scripts and styles only on plugin option page
* @hooks on 'admin_enqueue_scripts'
* 
* @since 0.9.0
* 
* @return scripts and styles loaded
*/ 
function asf_adminStyle() {
    if(!AFG_IS_ASF) return;
    $version = AFG_ASF_VERSION;
    wp_enqueue_style('asf-admin', AFG_ASF_URL.'assets/css/admin.css', array(), $version, 'all');
    wp_enqueue_script( 'asf-admin', AFG_ASF_URL . 'assets/js/admin.js', array('jquery','jquery-ui-accordion'), $version, 'all' );
    wp_localize_script( 'asf-admin', 'asfAjax', array(
        'ajaxurl'=> admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce('ajax-admin-nonce'),
    ));
}

/**
* Plugin Links
* 
* Add plugin links in plugins list page
* @hooks on '%plugin_action_links%'
* 
* @since 0.9.0
* 
* @return html - links
*/
function asf_pluginLinks($links): array {
        array_unshift(
            $links,
            sprintf(
                esc_html(__( '%1$sSettings%2$s', 'seo-file-names' )),
                '<a href="' . menu_page_url( 'asf-settings', false ) . '">',
                '</a>'
            )
        );
        $links[] = sprintf(
            esc_html(__( '%1$sSaved time? Buy me a coffee.%2$s', 'seo-file-names' )),
            '<a href="https://www.paypal.com/biz/fund?id=6WVXD3SYG3L58" target="_blank">',
            '</a>'
        );
        return $links;
}

/**
* Remove other plugin notices on plugin option page
* @hooks on 'in_admin_header'
* 
* @since 0.9.0
* 
* @return void
* 
*/
function asf_notices() {
    if(!AFG_IS_ASF) return;
    remove_all_actions('admin_notices');
    remove_all_actions('all_admin_notices');
}

/**
* Filename rewrite wrapper
* @hooks on 'wp_handle_upload_prefilter'
* 
* Run the main plugin methods to rewrite file names
* 
* @since 0.9.0
* 
* @return string - new file name
* 
*/
function asf_rewriteFileName($file) {
        $filename = new asf_FileName;
        $file = $filename->rewriteFileName($file);
        return $file;
}


/**
* Clear last post ajax filled datas
* if not on a Gutenberg post
* @hooks on 'admin_enqueue_scripts'
* 
* Update 'asf_tmp_options' DB option
* 
* @since 0.9.0
* 
* @return void
*/ 
function asf_clearUserVals() {
    if(!asf_isGutenbergEditor()) {
        update_option('asf_tmp_options',false);
    }
}

/**
 * Save in DB for 'wp_handle_upload_prefilter' event
 * @hooks on 'plugins_loaded'
 * 
 * Update 'asf_tmp_term' DB option
 * 
 * @since 0.9.0
 * 
 * @return void
 * 
 */
function asf_saveTagId() {
    if(isset($_GET['tag_ID']) && !empty($_GET['tag_ID'])) {
        
        $sanitize = new asf_Sanitize;
        $tagId = $sanitize->sanitizeId($_GET['tag_ID']);
        if(!$tagId) return false;

        $term = get_term($tagId);
        if (is_a($term, 'WP_Term')) {
            $id = $sanitize->sanitizeTermId($term);
            update_option('asf_tmp_term',$id);
        }
    }
}

/**
 * Get Slug as soon as it exists
 * @hooks on 'wp_unique_post_slug'
 * 
 * Update 'asf_tmp_post' DB option
 * 
 * @since 0.9.0
 * 
 * @return void
 * 
 */
function asf_preGetslug($slug, $postId, $postStatus, $postType, $postParent, $originalSlug) {

    $sanitize = new asf_Sanitize;
    $postId = $sanitize->sanitizeId($postId);
    update_option('asf_tmp_post',$postId);
    return $slug;
}

/**
 * Load Gutenberg Script and sanitize.js
 * @hooks on 'enqueue_block_editor_assets'
 * 
 * Load only on posts and pages supporting Gutenberg
 * 
 * @since 0.9.0
 * 
 * @return scripts loaded
 * 
 */
function asf_GutenbergScript() {
    $version = AFG_ASF_VERSION;
    wp_enqueue_script('asf-sanitize', AFG_ASF_URL . 'assets/js/sanitize.js', array(), $version, 'all');
    wp_enqueue_script( 'asf-gutenberg', AFG_ASF_URL . 'assets/js/gutenberg.js', array('wp-blocks','asf-sanitize'), $version, 'all');
    wp_localize_script( 'asf-gutenberg', 'asfAjax', array(
        'ajaxurl'=> admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce('ajax-nonce'),
    ));
}

/**
 * Load Classic Editor Script and sanitize.js
 * @hooks on 'admin_enqueue_scripts'
 * 
 * Load only on posts and pages not supporting Gutenberg
 * 
 * @since 0.9.2
 * 
 * @return scripts loaded
 * 
 */
function asf_classicEditorScript() {
    if(asf_isGutenbergEditor()) return;
    $version = AFG_ASF_VERSION;
    wp_enqueue_script('asf-sanitize', AFG_ASF_URL . 'assets/js/sanitize.js', array(), $version, 'all');
    wp_enqueue_script( 'asf-classic-editor', AFG_ASF_URL . 'assets/js/classic-editor.js', array('jquery','asf-sanitize'), $version, 'all' );
    wp_localize_script( 'asf-classic-editor', 'asfAjax', array(
        'ajaxurl'=> admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce('ajax-nonce'),
    ));

}

/**
 * Ajax save latest post datas
 * @hooks on 'wp_ajax_asf_save_meta'
 * 
 * Update 'asf_tmp_options' DB option
 * 
 * @since 0.9.0
 * 
 * @return void
 * 
 */
function asf_saveMeta() {
    if(isset($_POST['asf_nonce'])) {
        if( !wp_verify_nonce( $_POST['asf_nonce'], 'ajax-nonce' ) ) {
             wp_die();
        }
    }
    if(isset($_POST['asf_datas'])) {
        
        $options = new asf_options;
        $options = $options->getOptions();
        
        if(!isset($options['datas']) && is_array(!$options['datas'])) wp_die();
        
        $sanitize = new asf_Sanitize;
        $string = $sanitize->sanitizeJson($_POST['asf_datas']);

        $datas = json_decode($string,true);
        if(!is_array($datas)) wp_die();
        
        if($datas = $sanitize->sanitizeTmpDatas($options,$datas)) {
            $options['datas'] = $datas;
            update_option('asf_tmp_options',$options);
        }

        wp_die();
    }
    wp_die();
}

/**
* Check if current page is using Guntenberg
* 
* @since 0.9.0
* 
* @return bol
* 
*/
function asf_isGutenbergEditor() {
    if( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) return true;
    
    global $current_screen;
    if ( ! isset( $current_screen ) ) return false;
    if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) return true;
    return false;
}