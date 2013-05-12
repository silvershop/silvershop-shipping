<?php
/**
 * Work out shipping rate from a pre-defined table of regions - to - weights and dimensions.
 * 
 * @package shop
 * @subpackage shipping
 */
class TableShippingMethod extends ShippingMethod{
	
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
			"\"ShippingMethodID\" = ".$this->ID,
			RegionRestriction::address_filter($address), //address restriction
			implode(" OR ",$constraintfilters) //metrics restriction
		)).")";
		if($tr = DataObject::get_one("TableShippingRate", $filter, true, "Rate ASC")){
			$rate = $tr->Rate;
		}
		$this->CalculatedRate = $rate;
		return $rate;
	}
	
	function getCMSFields(){
		$fields = parent::getCMSFields();
		
		$fieldList = array(
			"Country" => "Country",
			"State" => "State",
			"City" => "City",
			"PostalCode" => "PostCode",
			"WeightMin" => "WeightMin",
			"WeightMax" => "WeightMax",
			"VolumeMin" => "VolumeMin",
			"VolumeMax" => "VolumeMax",
			"ValueMin" => "ValueMin",
			"ValueMax" => "ValueMax",
			"QuantityMin" => "QuantityMin",
			"QuantityMax" => "QuantityMax",
			"Rate" => "Rate"
		);
		
		$fields->fieldByName('Root')->removeByName("Rates");
		if($this->isInDB()){
			$tablefield = new GridField("Rates", "TableShippingRate", $this->Rates(), new GridFieldConfig_RelationEditor());
			$fields->addFieldToTab("Root.Main", $tablefield);
		}
		return $fields;
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
		"ShippingMethod" => "TableShippingMethod"
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
	
	static $default_sort = "\"Country\" ASC, \"State\" ASC, \"City\" ASC, \"PostalCode\" ASC, \"Rate\" ASC";
	
	function getCMSFields(){
		$fields = parent::getCMSFields();
		$fields->removeByName('ShippingMethodID');
		return $fields;
	}
}