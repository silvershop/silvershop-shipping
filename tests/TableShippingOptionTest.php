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
	}
	
	function testInternationalRates(){
		$address = $this->objFromFixture("Address", "bukhp193eq");
		
		$p0 = new ShippingPackage();
		$p1 = new ShippingPackage(2.34, array(0.5,1,2), array('value' => 2, 'quantity' => 3)); 
		$p2 = new ShippingPackage(17, array(1,2,3), array('value' => 6, 'quantity' => 10));
		$p3 = new ShippingPackage(100, array(12.33,51,30.1), array('value' => 1000, 'quantity' => 55)); 
		$p4 = new ShippingPackage(1000, array(100,200,300), array('value' => 1000000, 'quantity' => 12412));
		
		//weight based
		$type = "weight";
		$this->assertMatch($type, $p0, $address, 8); //weight = 0kg
		$this->assertMatch($type, $p1, $address, 8); //weight = 2.34kg
		$this->assertMatch($type, $p2, $address, 96); //weight= 17kg, 
		$this->assertMatch($type, $p3, $address, 116); //weight = 100kg
		$this->assertNoMatch($type, $p4, $address);  //weight = 1000kg
		
		//volume based
		$type = "volume";
		$this->assertMatch($type, $p0, $address, 2); //volume = 0cm3
		$this->assertMatch($type, $p1, $address, 2); //volume = 1cm3
		$this->assertMatch($type, $p2, $address, 6); //volume = 6cm3
		$this->assertNoMatch($type, $p3, $address); //volume = 18927.783cm3
		$this->assertNoMatch($type, $p4, $address); //volume = 2000000cm3

		//value based
		$type = "value";
		$this->assertMatch($type, $p0, $address, 2); //value = $0
		$this->assertMatch($type, $p1, $address, 2); //value = $2
		$this->assertMatch($type, $p2, $address, 6); //value = $6
		$this->assertNoMatch($type, $p3, $address); //value = $1000
		$this->assertNoMatch($type, $p4, $address); //value = $1,000,000
		
		//quantity based
		$type = "quantity";
		$this->assertNoMatch($type, $p0, $address); //quantity = 0
		$this->assertMatch($type, $p1, $address, 11); //quantity = 3
		$this->assertMatch($type, $p2, $address, 18.6); //quantity = 10
		$this->assertNoMatch($type, $p3, $address); //quantity = 155
		$this->assertNoMatch($type, $p4, $address); //quantity = 12412
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