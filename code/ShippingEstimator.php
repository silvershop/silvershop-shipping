<?php

/**
 * Helper class for calculating rates for available shipping options.
 * Provides a little caching, so estimates aren't calculated more than once.
 */
class ShippingEstimator{
	
	protected $package, $address, $estimates = null, $calculated = false;
	
	function __construct(ShippingPackage $package, Address $address){
		$this->package = $package;
		$this->address = $address;
	}
	
	function getEstimates(){
		if($this->calculated){
			return $this->estimates;
		}
		$output = new ArrayList();
		if($options = $this->getShippingMethods()){
			foreach($options as $option){
				$option->CalculatedRate = $option->calculateRate($this->package, $this->address);
				if($option->CalculatedRate !== null){
					$output->add($option);
				}
			}
		}
		$output->sort("CalculatedRate","ASC"); //sort by rate, lowest to highest
		// cache estimates
		$this->estimates = $output;
		$this->calculated = true;
		return $output;
	}
	
	/**
	 * get options that apply to package and location
	 */
	function getShippingMethods(){
		$options = DataObject::get("ShippingMethod","\"Enabled\" = 1");
		//TODO: restrict options to region / package specs
		return $options;
	}
	
}