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
	
}