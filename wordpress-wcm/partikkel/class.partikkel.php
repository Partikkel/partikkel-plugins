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
    add_shortcode( 'p_wcm_nonmember', array( 'Partikkel', 'p_wcm_nonmember_shortcode' ));
    add_shortcode( 'p_wcm_restrict', array( 'Partikkel', 'p_wcm_restricted_shortcode' ));
      
      
    add_action( 'wp_enqueue_scripts', array( 'Partikkel', 'enqueuePartikkelStyleAndScript' ) );
  }

  public static function partikkel_shortcode($atts, $content = null) {
    $p = !empty( $_SESSION['paid'.get_the_ID()] ) ? $_SESSION['paid'.get_the_ID()] : false;
    if ($p) {
      return do_shortcode( $content . '<div id="partikkel-paid"/>' );
    }
    return '<p><div id="partikkel-button-wrapper"/></p>';
  }

    /**
     * WooCommerce Membership-integration: Nonmember content shortcode
     *
     * @internal
     *
     * @since 1.1.0
     * @param array $atts Shortcode attributes
     * @param string|null $content
     * @return string Shortcode result
     */
    public static function p_wcm_nonmember_shortcode( $atts, $content = null ) {

        $partikkel_access = !empty( $_SESSION['paid'.get_the_ID()] ) ? $_SESSION['paid'.get_the_ID()] : false;

        // Hide non-member messages for super users
        if ( current_user_can( 'wc_memberships_access_all_restricted_content' ) ) {
            return '';
        }

        $plans         = wc_memberships_get_membership_plans();
        $active_member = array();

        foreach ( $plans as $plan ) {
            $active_member[] = wc_memberships_is_user_active_member( get_current_user_id(), $plan );
        }

        ob_start();

        if ( ! in_array( true, $active_member, true ) && !$partikkel_access) {
            echo do_shortcode( $content );
        }

        return ob_get_clean();
    }
    
    
    
	/**
	 * Restricted content messages
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 * @param array $atts Shortcode attributes
	 * @param string|null $content
	 * @return string Shortcode result
	 */
	public static function p_wcm_restricted_shortcode( $atts, $content = null ) {
        
        $partikkel_access = !empty( $_SESSION['paid'.get_the_ID()] ) ? $_SESSION['paid'.get_the_ID()] : false;
		
        if($partikkel_access){
            return do_shortcode( $content . '<div id="partikkel-paid"/>');
        } 
		return restrict($attrs,$content);
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
