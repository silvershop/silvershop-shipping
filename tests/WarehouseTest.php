<?php

namespace SilverShop\Shipping\Tests;

use SilverStripe\Dev\SapphireTest;
use SilverShop\Shipping\Model\Warehouse;
use SilverShop\Model\Address;

class WarehouseTest extends SapphireTest
{
    protected static $fixture_file = 'Warehouses.yml';

    public function testClosestWarehouse()
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
