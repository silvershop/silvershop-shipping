<?php

namespace SilverShop\Shipping\Tests;

use SilverStripe\Dev\SapphireTest;
use SilverShop\Model\Order;
use SilverShop\Model\Address;
use SilverShop\Shipping\ShippingPackage;
use SilverShop\Shipping\ShippingEstimator;

class ShippingEstimatorTest extends SapphireTest
{
    protected static $fixture_file = 'TableShippingMethod.yml';

    public function testGetEstimates(): void
    {
        $order = Order::create();
        $address = Address::create();
        ShippingPackage::create(2);
        $estimator = ShippingEstimator::create($order, $address);

        $options = $estimator->getShippingMethods();
        $this->assertNotNull($options, "options found");

        $estimates = $estimator->getEstimates();
        $this->assertTrue($estimates->exists(), "estimates found");
    }
}
