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
	
	static function address_filter(Address $address){
		$where = array(
			"TRIM(LOWER(\"Country\")) = TRIM(LOWER('".$address->Country."')) OR \"Country\" = '*'",
			"TRIM(LOWER(\"State\")) = TRIM(LOWER('".$address->State."')) OR \"State\" = '*'",
			"TRIM(LOWER(\"City\")) = TRIM(LOWER('".$address->City."')) OR \"City\" = '*'",
			"TRIM(LOWER(\"PostalCode\")) = TRIM(LOWER('".$address->PostalCode."')) OR \"PostalCode\" = '*'"
		);
		return "(".implode(") AND (", $where).")";
	}
	
}