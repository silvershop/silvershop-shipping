<?php 

class ShippingEstimatorTest extends SapphireTest{
	
	static $fixture_file = array(
		'shop_shippingframework/tests/fixtures/TableShippingMethod.yml',
		//'shop/tests/fixtures/Addresses.yml'
	);
	
	function testGetEstimates(){
		$address = new Address();
		$package = new ShippingPackage(2);
		$estimator = new ShippingEstimator($package, $address);

		$options = $estimator->getShippingMethods();
		$this->assertNotNull($options, "options found");
		
		$estimates = $estimator->getEstimates();
		$this->assertNotNull($estimates, "estimates found");
	}
	
}