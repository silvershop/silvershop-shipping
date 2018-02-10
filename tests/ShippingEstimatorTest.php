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

    function testGetEstimates() {
        $order = new Order();
        $address = new Address();
        $package = new ShippingPackage(2);
        $estimator = new ShippingEstimator($order, $address);

        $options = $estimator->getShippingMethods();
        $this->assertNotNull($options, "options found");

        $estimates = $estimator->getEstimates();
        $this->assertNotNull($estimates, "estimates found");
    }

}
