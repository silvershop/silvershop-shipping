<?php
/**
 * Work out shipping rate from a pre-defined table of regions - to - weights and dimensions.
 * 
 * @package shop
 * @subpackage shipping
 */
class TableShippingOption extends ShippingOption{
	
	static $defaults = array(
		'Name' => 'Table Shipping',
		'Description' => 'Works out shipping from a pre-defined table'
	);

	static $has_many = array(
		"Rates" => "TableShippingRate"
	);
	
	/**
	 * Find the appropriate shipping rate from stored table range metrics
	 */
	function calculateRate(ShippingPackage $package, Address $address){
		$rate = null;
		$packageconstraints = array(
			"Weight" => 'weight',
			"Volume" => 'volume',
			"Value" => 'value',
			"Quantity" => 'quantity'
		);
		$constraintfilters = array();
		foreach($packageconstraints as $db => $pakval){
			$mincol = "\"TableShippingRate\".\"{$db}Min\"";
			$maxcol = "\"TableShippingRate\".\"{$db}Max\"";
			$constraintfilters[] = "(".
				"$mincol >= 0" .
				" AND $mincol <= " . $package->{$pakval}() .
				" AND $maxcol > 0". //ignore constraints with maxvalue = 0
				" AND $maxcol >= " . $package->{$pakval}() .
				" AND $mincol < $maxcol" . //sanity check
			")";
		}
		$filter = "(".implode(") AND (",array(
			"\"ShippingOptionID\" = ".$this->ID,
			RegionRestriction::address_filter($address), //address restriction
			implode(" OR ",$constraintfilters) //metrics restriction
		)).")";
		if($tr = DataObject::get_one("TableShippingRate", $filter, true, "Rate ASC")){
			$rate = $tr->Rate;
		}
		$this->CalculatedRate = $rate;
		return $rate;
	}
	
}

/**
 * Adds extra metric ranges to restrict with, rather than just region.
 */
class TableShippingRate extends RegionRestriction{
	
	static $db = array(
		//constraint values
		"WeightMin" => "Decimal",
		"WeightMax" => "Decimal",
		"VolumeMin" => "Decimal",
		"VolumeMax" => "Decimal",
		"ValueMin" => "Currency",
		"ValueMax" => "Currency",
		"QuantityMin" => "Int",
		"QuantityMax" => "Int",
		
		"Rate" => "Currency"
	);
	
	static $has_one = array(
		"ShippingOption" => "TableShippingOption"
	);
	
	static $summary_fields = array(
		'Country',
		'State',
		'City',
		'PostalCode',
		'WeightMin',
		'WeightMax',
		'VolumeMin',
		'VolumeMax',
		'ValueMin',
		'ValueMax',
		'QuantityMin',
		'QuantityMax',
		'Rate'
	);
	
}