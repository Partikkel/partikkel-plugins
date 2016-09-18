<?php
namespace Craft;

class PartikkelVariable
{
    // Check if access cookie is set
    public function hasAccess($entryid)
    {
      return craft()->partikkel->checkTicket($entryid);
    }
}
