<?php

/**
 * @package silvershop-shipping
 */
class Warehouse extends DataObject {

	private static $db = array(
		'Title' => 'Varchar'
	);

	private static $has_one = array(
		'Address' => 'Address'
	);

	public static $summary_fields = array(
		'Title', 'Address'
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->removeByName("AddressID");
		$fields->addFieldToTab("Root.Main",
			HasOneButtonField::create("Address", "Address", $this)
		);

		return $fields;
	}

	/**
	 * Get the closest warehouse to an address.
	 *
	 * @param  Address $address
	 * @return Warehouse
	 */
	public static function closest_to(Address $address) {
		$warehouses = self::get()
			->where("\"AddressID\" IS NOT NULL");
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
