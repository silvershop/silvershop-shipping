<?php

if(class_exists("AddressGeocoding")){

class DistanceShippingMethodTest extends SapphireTest{
	
	protected static $fixture_file = array(
		'shop_shipping/tests/fixtures/DistanceShippingMethod.yml',
		'shop_shipping/tests/fixtures/Warehouses.yml'
	);

	function testDistanceFare() {
		$method = $this->objFromFixture("DistanceShippingMethod", "ds");
		$this->assertEquals(0, $method->getDistanceFare(9));
		$this->assertEquals(0, $method->getDistanceFare(0.5));
		$this->assertEquals(234, $method->getDistanceFare(150));
		$this->assertEquals(678, $method->getDistanceFare(999999));

		//TODO: what if distane can't be calculated?
		//what if warehouse can't be found?
		//what if address is invalid?
	}

	function testCalculateRates() {
		$method = $this->objFromFixture("DistanceShippingMethod", "ds");
		$this->assertEquals(234,
			$method->calculateRate(
				new ShippingPackage(), 
				$this->objFromFixture("Address", "customeraddress1")
			)
		);
		$this->assertEquals(567,
			$method->calculateRate(
				new ShippingPackage(), 
				$this->objFromFixture("Address", "customeraddress2")
			)
		);
	}

}

}