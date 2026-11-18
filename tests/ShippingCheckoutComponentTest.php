<?php

namespace SilverShop\Shipping\Tests;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\Core\Validation\ValidationException;
use SilverShop\Model\Order;
use SilverShop\Shipping\Checkout\Component\ShippingCheckoutComponent;
use SilverShop\Shipping\Model\TableShippingMethod;

class ShippingCheckoutComponentTest extends SapphireTest
{
    protected static $fixture_file = 'TableShippingMethod.yml';

    public function testValidateDataRejectsShippingMethodNotOfferedForOrder(): void
    {
        $component = ShippingCheckoutComponent::create();
        $order = Order::create();

        // the "insured" method exists but is disabled, so it is never offered as an estimate
        $disabled = $this->objFromFixture(TableShippingMethod::class, 'insured');

        $this->expectException(ValidationException::class);
        $component->validateData($order, ['ShippingMethodID' => $disabled->ID]);
    }

    public function testValidateDataAcceptsOfferedShippingMethod(): void
    {
        $component = ShippingCheckoutComponent::create();
        $order = Order::create();
        $offered = $order->getShippingEstimates()->first();

        $this->assertNotNull($offered, 'an estimate should be offered');
        $this->assertTrue($component->validateData($order, ['ShippingMethodID' => $offered->ID]));
    }

    public function testSetDataIgnoresShippingMethodNotOfferedForOrder(): void
    {
        $component = ShippingCheckoutComponent::create();
        $order = Order::create();
        $order->write();

        $disabled = $this->objFromFixture(TableShippingMethod::class, 'insured');
        $component->setData($order, ['ShippingMethodID' => $disabled->ID]);

        $this->assertNotEquals($disabled->ID, (int) $order->ShippingMethodID);
    }

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
