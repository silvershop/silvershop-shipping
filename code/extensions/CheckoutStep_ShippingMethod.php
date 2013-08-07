<?php
/**
 * Gives methods to ship by, based on previously given address and order items.
 * 
 */
class CheckoutStep_ShippingMethod extends CheckoutStep{

	static $cheapest_first = true;
	
	static $allowed_actions = array(
		'shippingmethod',
		'ShippingMethodForm'
	);
	
	function shippingmethod(){
		$form = $this->ShippingMethodForm();
		$cart = ShoppingCart::singleton()->current();
		if($cart->ShippingMethodID){
			$form->loadDataFrom($cart);
		}
		return array(
			'Form' => $form
		);
	}
	
	function ShippingMethodForm(){
		$checkout = new Checkout($this->owner->Cart());
		$estimates = $checkout->getShippingEstimates();
		$fields = new FieldSet();
		if($estimates->exists()){
			$default = self::$cheapest_first ? $estimates->First()->ID : $estimates->Last()->ID;
			$fields->push(new OptionsetField("ShippingMethodID","",$estimates->toDropDownMap(),$default));
		}else{
			$fields->push(new LiteralField("NoShippingMethods", "<p class=\"message warning\">There are no shipping methods available</p>"));
		}
		$actions = new FieldSet(
			new FormAction("setShippingMethod","Continue")
		);
		$form = new Form($this->owner,"ShippingMethodForm",$fields,$actions);
		$this->owner->extend('updateShippingMethodForm',$form);
		return $form;
	}
	
	function setShippingMethod($data, $form){
		$cart = $this->owner->Cart();
		$option = null;
		if(isset($data['ShippingMethodID'])){
			$option = DataObject::get_by_id("ShippingMethod",(int)$data['ShippingMethodID']);
		}
		//assign option to order / modifier
		if($option){
			$checkout = new Checkout($cart);
			$checkout->setShippingMethod($option);
		}
		Director::redirect($this->NextStepLink('paymentmethod'));
	}
	
}