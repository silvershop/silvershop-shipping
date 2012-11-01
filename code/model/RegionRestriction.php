<?php

class RegionRestriction extends DataObject{
	
	static $db = array(
		"Country" => "ShopCountry",
		"State" => "Varchar",
		"City" => "Varchar",
		"PostalCode" => "Varchar(10)"
	);
	
	static $defaults = array(
		"Country" => "*",
		"State" => "*",
		"City" => "*",
		"PostalCode" => "*"
	);
	
	static $default_sort = "\"Country\" ASC, \"State\" ASC, \"City\" ASC, \"PostalCode\" ASC, \"Rate\" ASC";
	
	
	/**
	 * Produce a SQL filter to get matching RegionRestrictions to a given address
	 * @param Address $address
	 */
	static function address_filter(Address $address){	
		$restrictables = array(
			"Country",
			"State",
			"City",
			"PostalCode"
		);
		$where = array();
		foreach($restrictables as $field){
			$where[] = "TRIM(LOWER(\"$field\")) = TRIM(LOWER('".$address->$field."')) OR \"$field\" = '*' OR \"$field\" = ''";
		}
		return "(".implode(") AND (", $where).")";
	}
	
	function onBeforeWrite(){
		foreach(self::$defaults as $field => $value){
			if(empty($this->$field)){
				$this->$field = $value;
			}
		}
		parent::onBeforeWrite();
	}
	
}