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
	
	static $casting = array(
		'Rate' => 'Currency'
	);
	
	function calculateRate(Package $package, Address $address){
		return null;
	}
	
	function getRate(){
		return $this->CalculatedRate;
	}
	
}