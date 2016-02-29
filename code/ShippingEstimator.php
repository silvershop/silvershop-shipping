<?php

/**
 * Helper class for calculating rates for available shipping options.
 * Provides a little caching, so estimates aren't calculated more than once.
 *
 * @package silvershop-shipping
 */
class ShippingEstimator {

	protected $order;
	protected $address;
	protected $estimates = null;
	protected $calculated = false;

	function __construct(Order $order, Address $address = null) {
		$this->order = $order;
		$this->address = $address ? $address : $order->getShippingAddress();
	}

	function getEstimates() {
		if($this->calculated){
			return $this->estimates;
		}
		$output = new ArrayList();
		if($options = $this->getShippingMethods()){
			foreach($options as $option){
				$rate = $option->getCalculator($this->order)->calculate($this->address);
				if($rate !== null){
					$option->CalculatedRate = $rate;
					$output->push($option);
				}
			}
		}
		$output->sort("CalculatedRate", "ASC"); //sort by rate, lowest to highest
		// cache estimates
		$this->estimates = $output;
		$this->calculated = true;

		return $output;
	}

	/**
	 * get options that apply to package and location
	 */
	function getShippingMethods() {
		//TODO: restrict options to region / package specs
		return ShippingMethod::get()->filter("Enabled", 1);
	}

}
