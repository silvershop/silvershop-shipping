<?php

namespace SilverShop\Shipping\Tests;

use SilverStripe\Dev\SapphireTest;
use SilverShop\Shipping\Model\Warehouse;
use SilverShop\Model\Address;
use SilverStripe\Core\Config\Config;

class WarehouseTest extends SapphireTest
{
    protected static $fixture_file = 'Warehouses.yml';

    public function setup(): void
    {
        Config::inst()->update(Address::class, 'enable_geocoding', false);

        parent::setUp();
    }

    public function testClosestWarehouse(): void
    {
        $warehouse = Warehouse::closest_to(
            $this->objFromFixture(Address::class, "customeraddress1")
        );

        $this->assertEquals("Main warehouse", $warehouse->Title);

        $warehouse =  Warehouse::closest_to(
            $this->objFromFixture(Address::class, "customeraddress2")
        );

        $this->assertEquals("NSW depot", $warehouse->Title);
    }
}
