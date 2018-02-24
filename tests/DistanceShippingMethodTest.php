<?php

namespace SilverShop\Shipping\Tests;

use SilverStripe\Dev\SapphireTest;
use SilverShop\Shipping\ShippingPackage;
use SilverShop\Model\Address;
use SilverShop\Shipping\Model\DistanceShippingMethod;

class DistanceShippingMethodTest extends SapphireTest
{

    protected static $fixture_file = [
        'DistanceShippingMethod.yml',
        'Warehouses.yml'
    ];

    public function testDistanceFare() {
        $method = $this->objFromFixture(DistanceShippingMethod::class, "ds");
        $this->assertEquals(0, $method->getDistanceFare(9));
        $this->assertEquals(0, $method->getDistanceFare(0.5));
        $this->assertEquals(234, $method->getDistanceFare(150));
        $this->assertEquals(678, $method->getDistanceFare(999999));
    }

    public function testCalculateRates() {
        $method = $this->objFromFixture(DistanceShippingMethod::class, "ds");
        $this->assertEquals(234,
            $method->calculateRate(
                new ShippingPackage(),
                $this->objFromFixture(Address::class, "customeraddress1")
            )
        );
        $this->assertEquals(567,
            $method->calculateRate(
                new ShippingPackage(),
                $this->objFromFixture(Address::class, "customeraddress2")
            )
        );
    }

}
