<?php
class Partikkel {

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
    add_shortcode( 'partikkel', array( 'Partikkel', 'partikkel_shortcode' ));
  }

  public static function partikkel_shortcode($atts, $content = null) {
    $p = !empty( $_SESSION['paid'.get_the_ID()] ) ? $_SESSION['paid'.get_the_ID()] : false;
    if ($p) {
      return do_shortcode( $content );
    }
    $environment = get_option('partikkel_environment');
    $buyurl="https://test.partikkel.io/buy";
    if($environment==='production')
      $buyurl="https://www.partikkel.io/buy";
    return '<a href="' . $buyurl . '"> Partikkel</a>';
  }

}
