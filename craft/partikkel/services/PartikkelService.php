<?php
namespace Craft;

define( 'PARTIKKEL__PLUGIN_DIR', '../craft/plugins/partikkel/' );

use \Firebase\JWT\JWT;
require_once (PARTIKKEL__PLUGIN_DIR.'includes/vendor/autoload.php');

class PartikkelService extends BaseApplicationComponent
{
	public function checkTicket($entryid)
	{
    if($entryid ==0){
      // failsafe
      return false;
    }

    $paidSession = craft()->httpSession->get('paid'.$entryid);
    if(!empty($paidSession) && $paidSession == 1) {
      return true;
    }

    $cookie = craft()->request->getQuery("partikkel");
    if(empty($cookie)){
      return false;
    }
    return self::validateTicket($cookie, $entryid);
	}

  function validateTicket($ticket, $entryid)
  {
    $environment = self::getEnvironment();
  	$cert = "public.pem.test";
    if($environment==='production')
      $cert="public.pem.prod";
  	$jwt = base64_decode($ticket);
  	$public_key = openssl_pkey_get_public(file_get_contents(PARTIKKEL__PLUGIN_DIR .$cert));
  	try{
  		$decoded = JWT::decode($jwt, $public_key, array('RS256'));
  	}
  	catch (Exception $e) {
  		//error_log('Caught exception: '.  $e->getMessage(). "\n",0);
  	}
  	if(!empty($decoded)){
      craft()->httpSession->add('paid'.$entryid, 1);
      return true;
  	}
    return false;
  }
  
  public function getEnvironment()
  {
      $plugin = craft()->plugins->getPlugin('partikkel');
      $settings = $plugin->getSettings();
      return $settings->environment;
  }
}
