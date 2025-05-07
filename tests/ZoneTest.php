<?php

namespace SilverShop\Shipping\Tests;

use SilverShop\Model\Address;
use SilverShop\Shipping\Model\Zone;
use SilverStripe\Dev\SapphireTest;

class ZoneTest extends SapphireTest
{
    public static $fixture_file = [
        'ZonedShippingMethod.yml',
        'Addresses.yml',
    ];

    public function testMatchingZones()
    {
        $this->assertZoneMatch($this->objFromFixture(Address::class, "wnz6012"), "Wellington NZ");
        $this->assertZoneMatch($this->objFromFixture(Address::class, "wnz6012"), "Local");
        $this->assertZoneMatch($this->objFromFixture(Address::class, "sau5024"), "TransTasman");
        $this->assertZoneMatch($this->objFromFixture(Address::class, "sau5024"), "South Australia");
        $this->assertZoneMatch($this->objFromFixture(Address::class, "scn266033"), "Asia");
        $this->assertZoneMatch($this->objFromFixture(Address::class, "zch1234"), "International");
    }

    public function assertZoneMatch($address, $zonename)
    {
        $zones = Zone::get_zones_for_address($address);
        $this->assertNotNull($zones);
        $this->assertListContains(
            [
                ['Name' => $zonename],
            ],
            $zones
        );
    }
}
