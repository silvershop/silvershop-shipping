<?php

class CheckoutPageControllerExtension extends Extension{
	
	static $allowed_actions = array(
		'shipping',
		'ShippingAddressForm',
		'billing',
		'BillingAddressForm',
		'shippingmethod',
		'ShippingMethodForm',
		'payment',
		'PaymentForm'
	);
	
	function shipping(){
		return array(
			'Form' => $this->ShippingAddressForm()
		);	
	}
	
	function ShippingAddressForm(){
		$form = new AddressForm($this->owner, 'ShippingAddressForm');
		return $form;
	}
	
	function shippingmethod(){
		
		return array(
			'Form' => $this->ShippingMethodForm()
		);
	}
	
	function ShippingMethodForm(){
		$checkout = new Checkout($this->owner->Cart());
		$estimates = $checkout->getShippingEstimates();
		
		$fields = new FieldSet(
			new OptionsetField("ShippingMethodID","Choose Shipping Method",$estimates->toDropDownMap())
		);
		$actions = new FieldSet(
			new FormAction("setShippingMethod","Continue")
		);
		return new Form($this->owner,"ShippingMethodForm",$fields,$actions);
	}
	
	function setShippingMethod($data, $form){
		$cart = $this->owner->Cart();
		$option = null;
		if(isset($data['ShippingMethodID'])){
			$option = DataObject::get_by_id("ShippingOption",(int)$data['ShippingMethodID']);
		}
		//assign option to order / modifier
		if($option){
			$checkout = new Checkout($cart);
			$checkout->setShippingOption($option);
		}
		Director::redirect($this->owner->Link());
	}
	
}