<?php

class WarehouseTest extends SapphireTest{

    protected static $fixture_file = 'silvershop-shipping/tests/fixtures/Warehouses.yml';
    
    function testClosestWarehouse() {

        $warehouse = Warehouse::closest_to(
            $this->objFromFixture("Address", "customeraddress1")
        );
        $this->assertEquals("Main warehouse", $warehouse->Title);

        $warehouse =  Warehouse::closest_to(
            $this->objFromFixture("Address", "customeraddress2")
        );
        $this->assertEquals("NSW depot", $warehouse->Title);
    }

}