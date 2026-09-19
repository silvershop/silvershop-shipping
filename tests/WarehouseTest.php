<?php

namespace SilverShop\Shipping\Tests;

use SilverStripe\Dev\SapphireTest;
use SilverShop\Shipping\Model\Warehouse;
use SilverShop\Model\Address;
use SilverStripe\Core\Config\Config;

class WarehouseTest extends SapphireTest
{
    protected static $fixture_file = 'Warehouses.yml';

    protected function setup(): void
    {
        Config::modify()->set(Address::class, 'enable_geocoding', false);
        parent::setUp();
    }

    public function testClosestWarehouse(): void
    {
        if (!method_exists(Address::class, 'distanceTo')) {
            $this->markTestSkipped('Warehouse distance tests require geocoding support on addresses.');
        }

        $warehouse = Warehouse::closest_to(
            $this->objFromFixture(Address::class, "customeraddress1")
        );
        $this->assertInstanceOf(Warehouse::class, $warehouse);
        $this->assertEquals("Main warehouse", $warehouse->Title);

        $warehouse =  Warehouse::closest_to(
            $this->objFromFixture(Address::class, "customeraddress2")
        );
        $this->assertInstanceOf(Warehouse::class, $warehouse);
        $this->assertEquals("NSW depot", $warehouse->Title);
    }
}
