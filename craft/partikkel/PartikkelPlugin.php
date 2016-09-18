<?php
namespace Craft;

class PartikkelPlugin extends BasePlugin
{

    function init() {
        // the includeJs method lets us add js to the bottom of the page
        $phost = self::pHost();

        // the includeJsResource method will add a js file to the bottom of the page
        $paymentJs = $phost.'/external/buttons/payment-confirmation.js';
        $buyButtonJs = $phost.'/external/buttons/partikkel_buy_button2.js';
        craft()->templates->includeJsFile($paymentJs);
        craft()->templates->includeJsFile($buyButtonJs);

        $paymentCss = $phost.'/external/buttons/payment-confirmation.css';
        $buyButtonCss = $phost.'/external/buttons/partikkel_buy_button.css';

        // the includeCssResource method will add a link in the head
        craft()->templates->includeCssFile($paymentCss);
        craft()->templates->includeCssFile($buyButtonCss);
    }

    function pHost(){
      $environment = self::getEnvironment();
      $host="https://test.partikkel.io";
      if($environment==='production')
        $host="https://www.partikkel.io";
      return $host;
    }

    public function getEnvironment()
    {
        $plugin = craft()->plugins->getPlugin('partikkel');
        $settings = $plugin->getSettings();
        return $settings->environment;
    }

    protected function defineSettings()
    {
       return array(
           'environment' => array(AttributeType::String, 'required' => true),
       );
     }

    public function getSettingsHtml()
    {
       return craft()->templates->render('partikkel/_settings', array(
                      'settings' => $this->getSettings()));
    }

    function getName()
    {
         return Craft::t('Partikkel mikrobetaling');
    }

    function getVersion()
    {
        return '0.8';
    }

    function getDocumentationUrl()
    {
        return "https://partikkel.wordpress.com/";
    }

    function getDeveloper()
    {
        return 'Partikkel mikrobetaling AS';
    }

    function getDescription()
    {
        return 'Partikkel makes it super easy for you to make your content payable.';
    }

    function getDeveloperUrl()
    {
        return 'http://www.partikkel.io';
    }
}
