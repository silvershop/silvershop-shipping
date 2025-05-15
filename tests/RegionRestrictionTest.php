<?php

namespace SilverShop\Shipping\Tests;

use SilverShop\Model\Address;
use SilverStripe\Dev\SapphireTest;
use SilverShop\Shipping\Tests\RegionRestrictionRate;

class RegionRestrictionTest extends SapphireTest
{
    protected static $fixture_file = [
        'RegionRestriction.yml',
        'Addresses.yml',
    ];

    protected static $extra_dataobjects = [
        RegionRestrictionRate::class
    ];

    public function testMatchLocal(): void
    {
        $address = $this->objFromFixture(Address::class, "wnz6012");
        $rate = $this->getRate($address);
        $this->assertTrue((boolean) $rate);
        $this->assertEquals(2, $rate->Rate);
    }

    public function testMatchRegional(): void
    {
        $address = $this->objFromFixture(Address::class, "wnz6022");
        $rate = $this->getRate($address);
        $this->assertTrue((boolean)$rate);
        $this->assertEquals(10, $rate->Rate);
    }

    public function testMatchNational(): void
    {
        $address = $this->objFromFixture(Address::class, "anz1010");
        $rate = $this->getRate($address);
        $this->assertTrue((boolean)$rate);
        $this->assertEquals(50, $rate->Rate);
    }

    public function testMatchDefault(): void
    {
        //add default rate
        $default = RegionRestrictionRate::create([
            'Rate' => 100,
        ]);
        $default->write();

        $address = $this->objFromFixture(Address::class, "bukhp193eq");
        $rate = $this->getRate($address);
        $this->assertTrue((boolean)$rate);
        $this->assertEquals(100, $rate->Rate);
    }

    public function testNoMatch(): void
    {
        $address = $this->objFromFixture(Address::class, "bukhp193eq");
        $rate = $this->getRate($address);
        $this->assertNull($rate);
    }

    public function testMatchSQLEscaping(): void
    {
        $address = Address::create()->update(
            [
                "Country" => "IT",
                "State" => "Valle d'Aosta",
            ]
        );

        $rate = $this->getRate($address);
        $this->assertFalse((boolean)$rate, "Can't find rate with unescaped data");

        $address = Address::create()->update(
            [
                "Country" => "NZ",
                "State" => "Hawke's Bay",
            ]
        );
        $rate = $this->getRate($address);
        $this->assertTrue((boolean)$rate, "Rate with unescaped data found");
    }

    public function getRate(Address $address)
    {
        return RegionRestrictionRate::filteredByAddress($address)->sort('Rate', 'ASC')->first();
    }
}
