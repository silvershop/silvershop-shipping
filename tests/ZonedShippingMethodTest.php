<?php

class ZonedShippingMethodTest extends TableShippingMethodTest{
    
    static $fixture_file = array(
        'silvershop-shipping/tests/fixtures/ZonedShippingMethod.yml'
    );

    protected $fixtureclass = "ZonedShippingMethod";

    //This test suite shares tests with TableShippingMethod

    public function testDefaultRate() {
        $type = "address";
        $address = $this->internationaladdress;
        $defaultrate = new ZonedShippingRate(array(
            "Rate" => 100,
            "ZoneID" => $this->objFromFixture("Zone", "int")->ID
        ));
        $defaultrate->write();
        $this->addressshipping->Rates()->add($defaultrate);
        
        $this->assertMatch($type, $this->p0, $address, 100);
        $this->assertMatch($type, $this->p2, $address, 100);
        $this->assertMatch($type, $this->p4, $address, 100);
    }
    
}