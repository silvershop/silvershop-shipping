<?php

namespace SilverShop\Shipping\Extension;

use SilverShop\Page\CartPageController;
use SilverShop\Shipping\Forms\ShippingEstimateForm;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Extension;

/**
 * @extends Extension<CartPageController&static>
 */
class CartPageShippingExtension extends Extension
{
    private static array $allowed_actions = [
        'ShippingEstimateForm'
    ];

    public function ShippingEstimateForm(): ShippingEstimateForm
    {
        return ShippingEstimateForm::create($this->owner);
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
