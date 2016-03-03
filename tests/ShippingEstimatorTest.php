<?php 

class ShippingEstimatorTest extends SapphireTest{
    
    protected static $fixture_file = array(
        'silvershop-shipping/tests/fixtures/TableShippingMethod.yml'
    );
    
    function testGetEstimates() {
        $order = new Order();
        $address = new Address();
        $package = new ShippingPackage(2);
        $estimator = new ShippingEstimator($order, $address);

        $options = $estimator->getShippingMethods();
        $this->assertNotNull($options, "options found");
        
        $estimates = $estimator->getEstimates();
        $this->assertNotNull($estimates, "estimates found");
    }
    
}