<?php
/**
 * Gives methods to ship by, based on previously given address and order items.
 *
 * @package silvershop-shipping
 */
class CheckoutStep_ShippingMethod extends CheckoutStep
{

	private static $allowed_actions = array(
		'shippingmethod',
		'ShippingMethodForm'
	);

	function shippingmethod() {
		$form = $this->ShippingMethodForm();
		$cart = ShoppingCart::singleton()->current();
		if($cart->ShippingMethodID){
			$form->loadDataFrom($cart);
		}
		return array(
			'OrderForm' => $form
		);
	}

	function ShippingMethodForm() {
		$order = $this->owner->Cart();
		$estimates = $order->getShippingEstimates();
		$fields = new FieldList();
		if($estimates->exists()){
			$fields->push(
				OptionsetField::create(
					"ShippingMethodID",
                    _t('CheckoutStep_ShippingMethod.ShippingOptions', 'Shipping Options'),
					$estimates->map(),
					$estimates->First()->ID
				)
			);
		}else{
			$fields->push(
				LiteralField::create(
					"NoShippingMethods",
                    _t('CheckoutStep_ShippingMethod.NoShippingMethods',
                        '<p class=\"message warning\">There are no shipping methods available</p>'
                    )
				)
			);
		}
		$actions = new FieldList(
			new FormAction("setShippingMethod", "Continue")
		);
		$form = new Form($this->owner, "ShippingMethodForm", $fields, $actions);
		$this->owner->extend('updateShippingMethodForm', $form);
		return $form;
	}

	function setShippingMethod($data, $form) {
		$order = $this->owner->Cart();
		$option = null;
		if(isset($data['ShippingMethodID'])){
			$option = ShippingMethod::get()
						->byID((int)$data['ShippingMethodID']);
		}
		//assign option to order / modifier
		if($option){
			$order->setShippingMethod($option);
		}
		$this->owner->redirect($this->NextStepLink());
	}

}
