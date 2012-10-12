<?php

class TableShippingOptionTest extends SapphireTest{
	
	static $fixture_file = array(
		'shop_regionrestrict/tests/fixtures/TableShippingOption.yml',
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
		
		//weight based
		$type = "weight";
		$this->assertMatch($type, new ShippingPackage(2.34), $address, 8); //weight = 2.34kg
		$this->assertMatch($type, new ShippingPackage(17), $address, 96); //weight= 17kg
		$this->assertMatch($type, new ShippingPackage(100), $address, 116.00); //weight = 100kg
		$this->assertNoMatch($type, new ShippingPackage(1000), $address); //weight = 1000kg
		
		//volume based
		$type = "volume";
		$this->assertMatch($type, new ShippingPackage(0), $address, 2); //volume = 0cm3
		$this->assertMatch($type, new ShippingPackage(0, array(0.5,1,2)), $address, 2); //volume = 1cm3
		$this->assertMatch($type, new ShippingPackage(0, array(1,2,3)), $address, 6); //volume = 6cm3
		$this->assertNoMatch($type, new ShippingPackage(0, array(100,200,300)), $address); //volume = 2000000cm3
		
		//TODO: value based
		
		
		//TODO: combinations
		
		
	}
	
	function assertMatch($type = "weight", $package,$address,$amount){
		$rate = $this->{$type."shipping"}->getRate($package,$address);
		$this->assertEquals($rate,$amount,"Check rate for package $package is $amount");
	}
	
	function assertNoMatch($type = "weight", $package,$address){
		$rate = $this->{$type."shipping"}->getRate($package,$address);
		$this->assertNull($rate,"Check rate for package $package is not found");
	}
	
}