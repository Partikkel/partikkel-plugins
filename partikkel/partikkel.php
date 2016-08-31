<?php
/**
 * Plugin Name: Partikkel
 * Plugin URI: https://www.partikkel.io
 * Description: This plugin enables Partikkel-payment
 * Version: 1.0.0
 * Author: Partikkel	
 * Author URI: http://www.partikkel.com
 * License: GPL2
 */

require_once plugin_dir_path( __FILE__ ).'includes/vendor/autoload.php';
use \Firebase\JWT\JWT;
add_action('init','register_session');
add_action('wp','peekTicket');
add_filter($if_shortcode_filter_prefix.'paid','partikkel_evaluator');
add_shortcode( 'price', 'price_shortcode' );
add_shortcode( 'buybutton', 'buybutton_shortcode' );
/**
 * Partikkel enqueue scripts and styles.
 */
function enqueuePartikkelStyleAndScript() {
    wp_enqueue_style( 'partikkel-button-style', 'https://www.partikkel.io/external/buttons/payment-confirmation.css' );
    wp_enqueue_style( 'partikkel-buy-button-style', 'https://www.partikkel.io/external/buttons/partikkel_buy_button.css' );

    wp_enqueue_script( 'partikkel-script','https://www.partikkel.io/external/buttons/payment-confirmation.js' , array(), '1.0.0', true );
    wp_enqueue_script( 'partikkel-buy-button','https://www.partikkel.io/external/buttons/partikkel_buy_button.js' , array(), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'enqueuePartikkelStyleAndScript' );

function register_session(){
    if( !session_id() )
        session_start();
}
function peekTicket(){
    if(!empty($_GET["partikkel"])){
 checkTicket($_GET["partikkel"]);
 wp_redirect(esc_url( remove_query_arg( 'partikkel' ) ));
 exit;
} 
}

function partikkel_evaluator($value)
    {
    $haspaid= !empty( $_SESSION['paid'.get_the_ID()] ) ? $_SESSION['paid'.get_the_ID()] : false;
    return $haspaid;
    }

function checkTicket($ticket)
{
$jwt = base64_decode($ticket);
$public_key = openssl_pkey_get_public(file_get_contents(plugin_dir_path( __FILE__ ) .'public.pem'));
try{
$decoded = JWT::decode($jwt, $public_key, array('RS256'));
}
catch (Exception $e) {
    error_log('Caught exception: '.  $e->getMessage(). "\n",0);
}
if(!empty($decoded)){
$_SESSION['paid'.get_the_ID()] = 1;
}
}

function price_shortcode( $atts, $content = null ) {
	$checked= !empty( $_SESSION['checked'.get_the_ID()] ) ? $_SESSION['checked'.get_the_ID()] : false;
	if(!$checked){
		$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
		$currenturl=$protocol . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
		$queryurl='https://www.partikkel.io/api/open/article/url?url='.$currenturl;
		$json = file_get_contents($queryurl);
		$data = json_decode($json);
		$price = $data->{'price'};
		$_SESSION['checked'.get_the_ID()] = $price;
	}
	return $_SESSION['checked'.get_the_ID()];
}

function buybutton_shortcode( $atts, $content = null ) {
	return '<iframe id="purchased-check" width="0" height="0"></iframe><div id="partikkel-button-wrapper"/>';
}


?>
