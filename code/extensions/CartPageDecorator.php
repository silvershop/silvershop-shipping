<?php

class CartPageDecorator extends Extension{
	
	static $allowed_actions = array(
		'ShippingEstimateForm'
	);
	
	function ShippingEstimateForm(){
		return new ShippingEstimateForm($this->owner);
	}
	
	function ShippingEstimates(){
		return Session::get("ShippingEstimates");
	}
	
}