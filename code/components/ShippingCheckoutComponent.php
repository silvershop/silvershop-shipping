<?php

class ShippingCheckoutComponent extends CheckoutComponent{

	public function getFormFields(Order $order) {
		$fields = new FieldList();
		$estimates = $order->getShippingEstimates();
		
		$fields->push(
			OptionsetField::create(
				"ShippingMethodID",
				"Shipping Options",
				$estimates->map(),
				$estimates->First()->ID
			)
		);

		return $fields;
	}

	public function getRequiredFields(Order $order) {
		
		return array();
		
	}

	public function validateData(Order $order, array $data) {
		$result = new ValidationResult();
		if(!isset($data['ShippingMethodID'])){
			$result->error("Shipping method not provided", "ShippingMethod");
			throw new ValidationException($result);
		}		
		
		if(!ShippingMethod::get()->byID($data['ShippingMethodID'])){
		 	$result->error("Shipping Method does not exist", "ShippingMethod");
		 	throw new ValidationException($result);
		}
	}

	public function getData(Order $order) {

		$estimates = $order->getShippingEstimates();
		$method = count($estimates) === 1 ? $estimates->First() : Session::get("Checkout.ShippingMethod");

		return array(
			'ShippingMethod' => $method
		);
	}

	public function setData(Order $order, array $data) {
		
		$option = null;
		if(isset($data['ShippingMethodID'])){
			$option = ShippingMethod::get()
				->byID((int)$data['ShippingMethodID']);
		}
		//assign option to order / modifier
		if($option){
			$order->setShippingMethod($option);
			Session::set("Checkout.ShippingMethod", $option);
		}
	}

}
