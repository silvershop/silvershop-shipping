<?php

/**
 * Zoned shipping is a variant of TableShipping that regionalises using zones,
 * which are collections of regions, rather than regionalising using specific
 * locations / wildcards.
 */
class ZonedShippingMethod extends ShippingMethod{
	
	static $defaults = array(
		'Name' => 'Zoned Shipping',
		'Description' => 'Works out shipping from a pre-defined zone rates'
	);
	
	static $has_many = array(
		"Rates" => "ZonedShippingRate"
	);
	
	function calculateRate(ShippingPackage $package, Address $address){
		$rate = null;
		$ids = Zone::get_zone_ids();
		if(!$ids){
			return $rate;
		}
		$packageconstraints = array(
			"Weight" => 'weight',
			"Volume" => 'volume',
			"Value" => 'value',
			"Quantity" => 'quantity'
		);
		$constraintfilters = array();
		foreach($packageconstraints as $db => $pakval){
			$mincol = "\"ZonedShippingRate\".\"{$db}Min\"";
			$maxcol = "\"ZonedShippingRate\".\"{$db}Max\"";
			$constraintfilters[] = "(".
				"$mincol >= 0" .
				" AND $mincol <= " . $package->{$pakval}() .
				" AND $maxcol > 0". //ignore constraints with maxvalue = 0
				" AND $maxcol >= " . $package->{$pakval}() .
				" AND $mincol < $maxcol" . //sanity check
			")";
		}
		
		$filter = "(".implode(") AND (",array(
			"\"ZonedShippingMethodID\" = ".$this->ID,
			"\"ZoneID\" IN(".implode(",", $ids).")", //zone restriction
			implode(" OR ",$constraintfilters) //metrics restriction
		)).")";
		//order by zone specificity
		$orderby = "";
		if(count($ids) > 1){
			$orderby = "CASE \"ZonedShippingRate\".\"ZoneID\"";
			$count = 1;
			foreach($ids as $id){
				$orderby .= " WHEN $id THEN $count ";
				$count ++;
			}
			$orderby .= "ELSE $count END ASC,";
		}
		$orderby .= "\"ZonedShippingRate\".\"Rate\" ASC";
		if($sr = DataObject::get_one("ZonedShippingRate", $filter, true, $orderby)){
			$rate = $sr->Rate;
		}
		$this->CalculatedRate = $rate;
		return $rate;
	}
	
	function getCMSFields(){
		$fields = parent::getCMSFields();
	
		$fieldList = array(
			"ZoneID" =>  "Zone",
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
	
		$fieldTypes = array(
			"ZoneID" => "ZoneSelectField",
			"WeightMin" => "TextField",
			"WeightMax" => "TextField",
			"VolumeMin" => "TextField",
			"VolumeMax" => "TextField",
			"ValueMin" => "TextField",
			"ValueMax" => "TextField",
			"QuantityMin" => "TextField",
			"QuantityMax" => "TextField",
			"Rate" => "TextField"
		);
	
		$fields->fieldByName('Root')->removeByName("Rates");
		if($this->isInDB()){
			$tablefield = new TableField("Rates", "ZonedShippingRate", $fieldList, $fieldTypes);
			$tablefield->setCustomSourceItems($this->Rates());
			$fields->addFieldToTab("Root.Main", $tablefield);
		}
		return $fields;
	}
	
}

class ZonedShippingRate extends DataObject{
	
	static $db = array(
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
		'Zone' => 'Zone',
		'ZonedShippingMethod' => 'ZonedShippingMethod'
	);
	
	static $summary_fields = array(
		'Zone.Name' => 'Zone',
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
	
	static $default_sort = "\"Rate\" ASC";
	
}