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
    add_action( 'wp_enqueue_scripts', array( 'Partikkel', 'enqueuePartikkelStyleAndScript' ) );
  }

  public static function partikkel_shortcode($atts, $content = null) {
    $p = !empty( $_SESSION['paid'.get_the_ID()] ) ? $_SESSION['paid'.get_the_ID()] : false;
    if ($p) {
      return do_shortcode( $content . '<div id="partikkel-paid"/>' );
    }
    return '<p><div id="partikkel-button-wrapper"/></p>';
  }

  public static function pHost(){
    $environment = get_option('partikkel_environment');
    $host="https://test.partikkel.io";
    if($environment==='production')
      $host="https://www.partikkel.io";
    return $host;
  }

  public static function enqueuePartikkelStyleAndScript() {
    $phost = self::pHost();
    wp_enqueue_style( 'partikkel-button-style', $phost . '/external/buttons/payment-confirmation.css' );
    wp_enqueue_script( 'partikkel-script', $phost . '/external/buttons/payment-confirmation.js' , array(), '1.0.0', true );
    wp_enqueue_script( 'partikkel-buy-button', $phost . '/external/buttons/partikkel_buy_button2<.js' , array(), '1.0.0', true );
    wp_enqueue_style( 'partikkel-buy-button-style', $phost . '/external/buttons/partikkel_buy_button.css' );
  }

}
