<?php

namespace SilverShop\Shipping\Extension;

use SilverStripe\Core\Extension;
use SilverShop\Shipping\Forms\ShippingEstimateForm;
use SilverStripe\Control\Controller;

class CartPageShippingExtension extends Extension
{
    private static array $allowed_actions = [
        'ShippingEstimateForm'
    ];

    public function ShippingEstimateForm(): ShippingEstimateForm
    {
        return new ShippingEstimateForm($this->owner);
    }

    public function ShippingEstimates()
    {
        $session = Controller::curr()->getRequest()->getSession();

        $estimates = $session->get("ShippingEstimates");
        $session->set("ShippingEstimates", null);
        $session->clear("ShippingEstimates");

        return $estimates;
    }
}
