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
	 * Work out the cost of sending package to given address
	 */
	function getRate(ShippingPackage $package, Address $address){
		$rate = null;
		//search for matching: region, weight, volume, value, count
		//for each shipping constraint: (below max or max is NULL) AND (above min OR min is NULL)
		$packageconstraints = array(
			"Weight" => 'weight',
			"Volume" => 'volume',
			//"Value",
			//"Quantity"
		);
		$filters = array(
			"\"ShippingOptionID\" = ".$this->ID
		);
		foreach($packageconstraints as $db => $pakval){
			if($package->{$pakval}()){
				$mincol = "\"TableShippingRate\".\"{$db}Min\"";
				$maxcol = "\"TableShippingRate\".\"{$db}Max\"";
				$filters[] = "$mincol >= 0 AND $mincol <= " . $package->{$pakval}() .
								 " AND $maxcol >= 0 AND $maxcol >= " . $package->{$pakval}();
			}
		}
		if($tr = DataObject::get_one("TableShippingRate", "(".implode(") AND (",$filters).")", true, "Rate ASC")){
			$rate = $tr->Rate;
		}
		return $rate;
	}
	
}

class TableShippingRate extends RegionRestriction{
	
	static $db = array(
		//constraints
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
	
}