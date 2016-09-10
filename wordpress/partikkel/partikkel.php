<?php
/**
 * Plugin Name: Partikkel
 * Plugin URI: https://www.partikkel.io
 * Description: Partikkel Micropayments makes is super easy to add payment to your site. Use the tag [partikkel] to make your content available to payment. Example: free content  [partikkel] paid content [/partikkel].
 * Version: 1.0.0
 * Author: Partikkel
 * Author URI: http://www.partikkel.com
 * License: GPL2
 */
define( 'PARTIKKEL__MINIMUM_WP_VERSION', '4.0' );
define( 'PARTIKKEL__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

use \Firebase\JWT\JWT;
require_once (PARTIKKEL__PLUGIN_DIR.'includes/vendor/autoload.php');
require_once( PARTIKKEL__PLUGIN_DIR . 'class.partikkel.php' );

add_action('init','register_session_if_none');
add_action('wp','peekTicket');
add_action( 'init', array( 'Partikkel', 'init' ) );


function partikkel_settings_api_init() {
add_settings_section(
'partikkel_setting_section',
'Partikkel settings',
'partikkel_setting_callback_function',
'general'
);

// Add the field with the names and function to use for our new settings, put it in our new section
add_settings_field(
'partikkel_environment',
'Environment',
'partikkel_environment_callback_function',
'general',
'partikkel_setting_section'
);

register_setting( 'general', 'partikkel_environment' );
}

add_action( 'admin_init', 'partikkel_settings_api_init' );

function partikkel_environment_callback_function() {
//echo '<p>Test eller produksjon</p>';
}

function partikkel_setting_callback_function() {
$setting = esc_attr( get_option( 'partikkel_environment' ) );
$istest = true;
$testchecked=' checked ';
$prodchecked=' ';
if($setting!=='test') {
	$istest=false;
	$testchecked=' ';
	$prodchecked=' checked';
}
echo '
  Test <input type="radio" name="partikkel_environment" value="test" ' . $testchecked . '>
  Production <input type="radio" name="partikkel_environment" value="production" '  . $prodchecked . '>
';
}

function register_session_if_none(){
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

function checkTicket($ticket)
{
	$environment = get_option('partikkel_environment');
	$cert="public.pem.test";
    if($environment==='production')
      $cert="public.pem.prod";
	$jwt = base64_decode($ticket);
	$public_key = openssl_pkey_get_public(file_get_contents(PARTIKKEL__PLUGIN_DIR .$cert));
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

if ( is_admin() ) {
  require_once( PARTIKKEL__PLUGIN_DIR . 'class.partikkel-admin.php' );
  add_action( 'init', array( 'Partikkel_Admin', 'init' ) );
}

?>
