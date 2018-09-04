<?php

namespace SilverShop\Shipping\Tests;

use SilverShop\Shipping\Model\ZonedShippingRate;
use SilverShop\Shipping\Model\ZonedShippingMethod;
use SilverShop\Shipping\Model\Zone;

class ZonedShippingMethodTest extends TableShippingMethodTest
{
    protected static $fixture_file = 'ZonedShippingMethod.yml';

    protected $fixtureclass = ZonedShippingMethod::class;

    public function testDefaultRate() {
        $type = 'address';
        $address = $this->internationaladdress;

        $defaultrate = new ZonedShippingRate([
            "Rate" => 100,
            "ZoneID" => $this->objFromFixture(Zone::class, "int")->ID
        ]);

        $defaultrate->write();

        $this->addressshipping->Rates()->add($defaultrate);

        $this->assertMatch($type, $this->p0, $address, 100);
        $this->assertMatch($type, $this->p2, $address, 100);
        $this->assertMatch($type, $this->p4, $address, 100);
    }

}
