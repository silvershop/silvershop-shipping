<?php

class CartPageDecorator extends Extension{
	
	static $allowed_actions = array(
		'ShippingEstimateForm'
	);
	
	function ShippingEstimateForm(){
		return new ShippingEstimateForm($this->owner);
	}
	
	function ShippingEstimates(){
		$estimates = Session::get("ShippingEstimates");
		Session::set("ShippingEstimates",null);
		Session::clear("ShippingEstimates");
		return $estimates;
	}
	
}