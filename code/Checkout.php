<?php

/**
 * Helper class for getting an order throught the checkout process
 */
class Checkout{
	
	protected $order;
	
	function __construct(Order $order){
		$this->order = $order;
	}
	
	//save / set up addresses
	
	/**
	 * Get shipping estimates
	 * @return DataObjectSet
	 */
	function getShippingEstimates(){
		$package = $this->order->createShippingPackage();
		$address = $this->order->getShippingAddress();
		$estimator = new ShippingEstimator($package,$address);
		$estimates = $estimator->getEstimates();
		return $estimates;
	}
	
	/*
	 * Set shipping method and shipping cost
	 * @param $option - shipping option to set, and calculate shipping from
	 * @return boolean sucess/failure of setting
	 */
	function setShippingOption(ShippingOption $option){
		$package = $this->order->createShippingPackage();
		$address = $this->order->getShippingAddress();
		if($option && $package && $address && $address->exists()){
			$this->order->ShippingTotal = $option->calculateRate($package,$address);
			$this->order->ShippingOptionID = $option->ID;
			$this->order->write();
			return true;
		}
		//TODO: set error messages
		return false;
	}
	
	//set discount code
	
	//get payment methods
	
	//choose payment method
	
	//display final data
	
	//complete
	
}