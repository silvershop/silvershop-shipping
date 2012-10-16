<?php

class TableShippingOptionTest extends SapphireTest{
	
	static $fixture_file = array(
		'shop_shippingframework/tests/fixtures/TableShippingOption.yml',
		'shop/tests/fixtures/Addresses.yml'
	);
	
	function setUp(){
		parent::setUp();
		$this->weightshipping = $this->objFromFixture("TableShippingOption", "weight");
		$this->volumeshipping = $this->objFromFixture("TableShippingOption", "volume");
		$this->valueshipping = $this->objFromFixture("TableShippingOption", "value");
		$this->quantityshipping = $this->objFromFixture("TableShippingOption", "quantity");

		$this->p0 = new ShippingPackage();
		$this->p1 = new ShippingPackage(2.34, array(0.5,1,2), array('value' => 2, 'quantity' => 3));
		$this->p2 = new ShippingPackage(17, array(1,2,3), array('value' => 6, 'quantity' => 10));
		$this->p3 = new ShippingPackage(100, array(12.33,51,30.1), array('value' => 1000, 'quantity' => 55));
		$this->p4 = new ShippingPackage(1000, array(100,200,300), array('value' => 1000000, 'quantity' => 12412));
	}
	
	function testInternationalRates(){
		$address_int = $this->objFromFixture("Address", "bukhp193eq"); //international address
		
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
		$this->assertNoMatch($type, $this->p3, $address_int); //volume = 18927.783cm3
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
	
	function testLocalRates(){
		$address_loc = $this->objFromFixture("Address", "wnz6022"); //New Zealand address
		
		//weight based
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
		$this->assertNoMatch($type, $this->p3, $address_loc); //volume = 18927.783cm3
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
	
	function assertMatch($type = "weight", $package,$address,$amount){
		$rate = $this->{$type."shipping"}->calculateRate($package,$address);
		$this->assertEquals($rate,$amount,"Check rate for package $package is $amount");
	}
	
	function assertNoMatch($type = "weight", $package,$address){
		$rate = $this->{$type."shipping"}->calculateRate($package,$address);
		$this->assertNull($rate,"Check rate for package $package is not found");
	}
	
}