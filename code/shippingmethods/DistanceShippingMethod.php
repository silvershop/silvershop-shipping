<?php

if(class_exists("AddressGeocoding")){

//requires shop_geocoding submodule
class DistanceShippingMethod extends ShippingMethod{
	
	private static $defaults = array(
		'Name' => 'Distance Shipping',
		'Description' => 'Per product shipping'
	);

	private static $has_many = array(
		"DistanceFares" => "DistanceShippingFare"
	);

	function calculateRate(ShippingPackage $package, Address $address) {
		$warehouse = $this->closestWarehouse($address);
		$distance = $warehouse->Address()->distanceTo($address);

		return $this->getDistanceFare($distance);
	}

	public function getDistanceFare($distance) {
		$cost = 0;
		$fare = $this->DistanceFares()
			->filter("Distance:GreaterThan", 0)
			->filter("Distance:GreaterThan", $distance)
			->sort("Distance", "ASC")
			->first();
		if(!$fare){
			$fare = $this->DistanceFares()
				->sort("Cost", "DESC")
				->first();
		}
		if($fare->exists()){
			$cost = $fare->Cost;
		}
		return $cost;
	}

	public function closestWarehouse(Address $address) {
		$warehouses = Warehouse::get()->filter('AddressID:not', 'NULL');
		$closestwarehouse = null;
		$shortestdistance = null;
		foreach($warehouses as $warehouse) {
			$dist = $warehouse->Address()->distanceTo($address);
			if($dist && ($shortestdistance === null || $dist < $shortestdistance)){
				$closestwarehouse = $warehouse;
				$shortestdistance = $dist;
			}
		}

		return $closestwarehouse;
	}

}

class DistanceShippingFare extends DataObject{

	private static $db = array(
		'Distance' => 'Float',
		'Cost' => 'Currency'
	);

	private static $has_one = array(
		'ShippingMethod' => 'DistanceShippingMethod'
	);
	
}

}
