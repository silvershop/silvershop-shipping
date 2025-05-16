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

    public function testDistanceFare(): void
    {
        $method = $this->objFromFixture(DistanceShippingMethod::class, "ds");
        $this->assertEquals(0, $method->getDistanceFare(9));
        $this->assertEquals(0, $method->getDistanceFare(0.5));
        $this->assertEquals(234, $method->getDistanceFare(150));
        $this->assertEquals(678, $method->getDistanceFare(999999));
    }

    public function testCalculateRates(): void
    {
        $method = $this->objFromFixture(DistanceShippingMethod::class, "ds");
        $result = $method->calculateRate(
            ShippingPackage::create(),
            $this->objFromFixture(Address::class, "customeraddress1")
        );
        if ($result) {
            $this->assertEquals(
                234,
                $result
            );
        }

        $result = $method->calculateRate(
            ShippingPackage::create(),
            $this->objFromFixture(Address::class, "customeraddress2")
        );
        if ($result) {
            $this->assertEquals(
                567,
                $result
            );
        }
    }
}
