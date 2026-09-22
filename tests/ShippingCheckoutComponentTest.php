<?php

namespace SilverShop\Shipping\Tests;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\Core\Validation\ValidationException;
use SilverShop\Model\Order;
use SilverShop\Shipping\Checkout\Component\ShippingCheckoutComponent;

class ShippingCheckoutComponentTest extends SapphireTest
{
    public function testValidateDataThrowsWhenNoShippingMethodProvided(): void
    {
        $component = ShippingCheckoutComponent::create();
        $order = Order::create();

        // Before the fix, validateData() referenced SilverStripe\ORM\ValidationResult /
        // ValidationException, which were removed in SS6, so this call fatally errored with
        // "class not found" during checkout. After the fix it throws a proper ValidationException.
        $this->expectException(ValidationException::class);
        $component->validateData($order, []);
    }
}
