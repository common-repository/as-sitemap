<?php
/*
 * Plugin URI:
 * Description: Simple html sitemap for your website
 * Version: 1.0
 * Author: Sullivan ATATRI
 * Author URI:
 * Text Domain: as-sitemap
 * Domain Path: /languages/
 * Depends: Timber
 * Namespace : AS_Sitemap
 * License: GPL v3
 */
namespace AS_Sitemap;

use Timber;

class Sitemap {

  public function __construct() {
    // Init check plugin dependencies
    add_action('init', array( $this, 'check_dependencies' ) );

    // Load translation
    add_action('plugins_loaded', array($this, 'load_textdomain'));

    // Add checkbox meta box
    add_action('add_meta_boxes', array( $this, 'meta_boxes_page_exclude' ) );

    // Save meta box
    add_action('save_post', array( $this, 'save_metaboxes' ) );

    // Add [as_sitemap] shortcode
    add_shortcode('as_sitemap', array( $this, 'shortcode_as_sitemap' ) );
  }

  /**
   * Load plugin translation
   *
   * @since 0.1
   *
   * @return void
   *
   **/
   public function load_textdomain() {
    load_plugin_textdomain( 'as-sitemap', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
   }

  /**
   * Check plugins dependencies
   *
   * @since 0.1
   *
   * @return void
   *
   **/
  public function check_dependencies() {
    if( ! class_exists('Timber') ) {
      add_action( 'admin_notices', function() {
        print( '<div class="notice notice-error">
            <p>' . __('Timber must be installed to use AS Sitemap plugin', 'as-sitemap') . '</p>
          </div>' );
      } );
      return;
    }
  }

  /**
   * Shortcodes initialization
   *
   * @since 0.1
   *
   * @return void
   *
   **/
  public function shortcode_as_sitemap() {
    $posts = wp_list_pages( array(
      'post_type'   => 'page',
      'echo'        => false,
      'post_status' => 'publish',
      'sort_column' => 'menu_order',
      'title_li'    => '',
      'meta_key' => 'as_sitemap_page_exclude',
      'meta_value' => 0
    ) );

    return Timber::compile( 'views/Frontend/shortcode.twig', array( 'posts' => $posts ) );
  }

  /**
   * Add page exclude meta box
   *
   * @since 0.2
   *
   * @return void
   *
   **/
  public function meta_boxes_page_exclude() {
    add_meta_box(
      'as-sitemap-page-exclude',
      __( 'Sitemap', 'as-sitemap' ),
      array( $this, 'meta_boxes_page_exclude_render' ),
      'page',
      'side',
      'default'
    );
  }

  /**
   * Get backend render for page exclude meta_box
   *
   * @since 0.2
   *
   * @return void
   *
   **/
  public function meta_boxes_page_exclude_render( $post ) {
    $data = array( 'is_exclude' => get_post_meta( $post->ID, 'as_sitemap_page_exclude', true) );
    Timber::render( 'views/Backend/meta_boxes_page_exclude.twig', $data );
  }

  /**
   * Update page exclude post meta
   *
   * @since 0.2
   *
   * @return void
   *
   **/
   public function save_metaboxes( $post_id ) {
     $as_sitemap_page_exclude = isset( $_POST['as_sitemap_page_exclude'] ) ? intval( $_POST['as_sitemap_page_exclude'] ) : 0;
     update_post_meta( $post_id, 'as_sitemap_page_exclude', $as_sitemap_page_exclude );
   }

  /**
   * Update all pages on plugin activation
   *
   * @since 0.2
   * @return void
   *
  */
  public static function on_plugin_activation() {
    $pages = get_posts( array('post_type' => 'page', 'numberposts' => -1 ) );

    foreach( $pages as $page ) {
      wp_update_post( $page );
    }
  }

}
?>
