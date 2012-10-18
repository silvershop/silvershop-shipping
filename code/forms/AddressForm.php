<?php

class AddressForm extends Form{
	
	
	function __construct($controller, $name = "AddressForm"){
		$addressSingleton = singleton("Address");
		$fields = $addressSingleton->getFormFields();
		$actions = new FieldSet(
			new FormAction("setAddress","Continue")
		);
		parent::__construct($controller, $name, $fields, $actions);
	}
	
	function setAddress($data,$form){
		
		if($order = ShoppingCart::singleton()->current()){
			$address = new Address();
			$form->saveInto($address);
			$address->write();
			$order->ShippingAddressID = $address->ID;
			$order->write();
			//TODO: either set new address, or choose matching existing member address
		}
		
		//make sure it saves to the right address
		Director::redirect($this->Controller()->Link('shippingmethod'));
	}
	
}