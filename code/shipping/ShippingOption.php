<?php
/**
 * ShippingOption is a base class for providing shipping options to customers. 
 * 
 * @package shop
 * @subpackage shipping 
 */
class ShippingOption extends DataObject{
	
	static $db = array(
		"Name" => "Varchar",
		"Description" => "Varchar",
		"Enabled" => "Boolean",
		"WeightMin" => "Decimal",
		"WeightMax" => "Decimal",
		"HandlingFee" => "Currency", //adds extra handling cost to use this method
	);
	
	static $many_many = array(
		//Groups
		//Countries / region restrictions
	);
	
	
	function getRate(Package $package,$address){
		
	}
	
}