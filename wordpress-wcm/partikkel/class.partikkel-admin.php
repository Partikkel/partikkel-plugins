<?php
class Partikkel_Admin {

  private static $initiated = false;

  public static function init() {
    if ( ! self::$initiated ) {
      self::init_hooks();
    }
  }

  /**
   * Initializes WordPress hooks
   */
  private static function init_hooks() {
    self::$initiated = true;
    add_action( 'admin_enqueue_scripts', array( 'Partikkel_Admin', 'load_resources' ) );
  }


  public static function load_resources() {
    global $hook_suffix;

    if ( in_array( $hook_suffix, array(
      'post.php',
      'post-new.php',
      'page-new.php',
      'page.php',
    ) ) ) {
      wp_register_script( 'partikkel_quicktags.js', plugin_dir_url( __FILE__ ) . '_inc/quicktags.js', array('jquery','quicktags'), '1.0' );
      wp_enqueue_script( 'partikkel_quicktags.js' );
    }
  }




}
