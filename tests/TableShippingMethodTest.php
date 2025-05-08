<?php

namespace SilverShop\Shipping\Tests;

use SilverStripe\Dev\SapphireTest;
use SilverShop\Model\Address;
use SilverShop\Shipping\ShippingPackage;
use SilverShop\Shipping\Model\TableShippingRate;
use SilverShop\Shipping\Model\TableShippingMethod;

class TableShippingMethodTest extends SapphireTest
{
    protected static $fixture_file = 'TableShippingMethod.yml';

    protected $fixtureclass = TableShippingMethod::class;

    protected $addressshipping;
    protected $weightshipping;
    protected $volumeshipping;
    protected $valueshipping;
    protected $quantityshipping;
    protected $nzaddress;
    protected $internationaladdress;
    protected $p0;
    protected $p1;
    protected $p2;
    protected $p3;
    protected $p4;

    public function setup(): void
    {
        parent::setUp();

        $this->addressshipping = $this->objFromFixture($this->fixtureclass, "address");
        $this->weightshipping = $this->objFromFixture($this->fixtureclass, "weight");
        $this->volumeshipping = $this->objFromFixture($this->fixtureclass, "volume");
        $this->valueshipping = $this->objFromFixture($this->fixtureclass, "value");
        $this->quantityshipping = $this->objFromFixture($this->fixtureclass, "quantity");

        $this->nzaddress = new Address([
            "Country" =>    "NZ",
            "State" =>      "Wellington",
            "PostalCode" => "6022"
        ]);

        $this->internationaladdress = new Address([
            "Company" => 'Nildram Ltd',
            "Address" => 'Ardenham Court',
            "Address2" =>    'Oxford Road',
            "City" => 'AYLESBURY',
            "State" => 'BUCKINGHAMSHIRE',
            "PostalCode" => 'HP19 3EQ',
            "Country" => 'UK'
        ]);

        //create some package fixtures
        $this->p0 = new ShippingPackage();
        $this->p1 = new ShippingPackage(2.34, [0.5,1,2], ['value' => 2, 'quantity' => 3]);
        $this->p2 = new ShippingPackage(17, [1,2,3], ['value' => 6, 'quantity' => 10]);
        $this->p3 = new ShippingPackage(100, [12.33,51,30.1], ['value' => 1000, 'quantity' => 55]);
        $this->p4 = new ShippingPackage(1000, [100,200,300], ['value' => 1000000, 'quantity' => 12412]);
    }

    public function testAddressTable(): void
    {
        $type = "address";
        $address = Address::create([
            'Country' => 'NZ',
            'State' => 'Wellington',
            'PostalCode' => '6004'
        ]);

        $this->assertMatch($type, $this->p0, $address, 30);
        $this->assertMatch($type, $this->p2, $address, 30);
        $this->assertMatch($type, $this->p4, $address, 30);

        $address = new Address([
            'Country' => 'NZ',
            'PostalCode' => '6000'
        ]);
        $this->assertMatch($type, $this->p0, $address, 45);
        $this->assertMatch($type, $this->p2, $address, 45);
        $this->assertMatch($type, $this->p4, $address, 45);

        //empty package rate
        $address = $this->internationaladdress;

        $this->assertMatch($type, $this->p0, $address, 0);
        $this->assertMatch($type, $this->p2, $address, 0);
        $this->assertMatch($type, $this->p4, $address, 0);
    }

    public function testDefaultRate(): void
    {
        $type = "address";
        $address = $this->internationaladdress;
        $defaultrate = new TableShippingRate([
            "Rate" => 100
        ]);
        $defaultrate->write();
        $this->addressshipping->Rates()->add($defaultrate);

        $this->assertMatch($type, $this->p0, $address, 100);
        $this->assertMatch($type, $this->p2, $address, 100);
        $this->assertMatch($type, $this->p4, $address, 100);
    }

    public function testInternationalRates(): void
    {
        $address_int = $this->internationaladdress;

        //weight based
        $type = "weight";
        $this->assertMatch($type, $this->p0, $address_int, 8); //weight = 0kg
        $this->assertMatch($type, $this->p1, $address_int, 8); //weight = 2.34kg
        $this->assertMatch($type, $this->p2, $address_int, 96); //weight= 17kg,
        $this->assertMatch($type, $this->p3, $address_int, 116); //weight = 100kg
        $this->assertNoMatch($type, $this->p4, $address_int);  //weight = 1000kg

        //volume based
        $type = "volume";
        $this->assertMatch($type, $this->p0, $address_int, 2); //volume = 0cm3
        $this->assertMatch($type, $this->p1, $address_int, 2); //volume = 1cm3
        $this->assertMatch($type, $this->p2, $address_int, 6); //volume = 6cm3
        $this->assertMatch($type, $this->p3, $address_int, 520); //volume = 18927.783cm3
        $this->assertNoMatch($type, $this->p4, $address_int); //volume = 2000000cm3

        //value based
        $type = "value";
        $this->assertMatch($type, $this->p0, $address_int, 2); //value = $0
        $this->assertMatch($type, $this->p1, $address_int, 2); //value = $2
        $this->assertMatch($type, $this->p2, $address_int, 6); //value = $6
        $this->assertNoMatch($type, $this->p3, $address_int); //value = $1000
        $this->assertNoMatch($type, $this->p4, $address_int); //value = $1,000,000

        //quantity based
        $type = "quantity";
        $this->assertNoMatch($type, $this->p0, $address_int); //quantity = 0
        $this->assertMatch($type, $this->p1, $address_int, 11); //quantity = 3
        $this->assertMatch($type, $this->p2, $address_int, 18.6); //quantity = 10
        $this->assertNoMatch($type, $this->p3, $address_int); //quantity = 155
        $this->assertNoMatch($type, $this->p4, $address_int); //quantity = 12412
    }

    public function testLocalRates(): void
    {
        $address_loc = $this->nzaddress;

        // weight based
        $type = "weight";
        $this->assertMatch($type, $this->p0, $address_loc, 4); //weight = 0kg
        $this->assertMatch($type, $this->p1, $address_loc, 4); //weight = 2.34kg
        $this->assertMatch($type, $this->p2, $address_loc, 48); //weight= 17kg,
        $this->assertMatch($type, $this->p3, $address_loc, 58); //weight = 100kg
        $this->assertNoMatch($type, $this->p4, $address_loc);  //weight = 1000kg

        //volume based
        $type = "volume";
        $this->assertMatch($type, $this->p0, $address_loc, 1); //volume = 0cm3
        $this->assertMatch($type, $this->p1, $address_loc, 1); //volume = 1cm3
        $this->assertMatch($type, $this->p2, $address_loc, 3); //volume = 6cm3
        $this->assertMatch($type, $this->p3, $address_loc, 260); //volume = 18927.783cm3
        $this->assertNoMatch($type, $this->p4, $address_loc); //volume = 2000000cm3

        //value based
        $type = "value";
        $this->assertMatch($type, $this->p0, $address_loc, 1); //value = $0
        $this->assertMatch($type, $this->p1, $address_loc, 1); //value = $2
        $this->assertMatch($type, $this->p2, $address_loc, 3); //value = $6
        $this->assertNoMatch($type, $this->p3, $address_loc); //value = $1000
        $this->assertNoMatch($type, $this->p4, $address_loc); //value = $1,000,000

        //quantity based
        $type = "quantity";
        $this->assertNoMatch($type, $this->p0, $address_loc); //quantity = 0
        $this->assertMatch($type, $this->p1, $address_loc, 5.5); //quantity = 3
        $this->assertMatch($type, $this->p2, $address_loc, 9.3); //quantity = 10
        $this->assertNoMatch($type, $this->p3, $address_loc); //quantity = 155
        $this->assertNoMatch($type, $this->p4, $address_loc); //quantity = 12412
    }

    protected function assertMatch(string $type, $package, $address, $amount)
    {
        $rate = $this->{$type . "shipping"}->calculateRate($package, $address);

        $this->assertEquals($amount, $rate, "Check rate for package $package is $amount");
    }

    protected function assertNoMatch(string $type, $package, $address)
    {
        $rate = $this->{$type . "shipping"}->calculateRate($package, $address);

        $this->assertNull($rate, "Check rate for package $package is not found");
    }
}
