<?php

/**
 * ShippingMethod is a base class for providing shipping options to customers.
 *
 * @package silvershop-shipping
 */
class ShippingMethod extends DataObject{

	private static $db = array(
		"Name" => "Varchar",
		"Description" => "Text",
		"Enabled" => "Boolean"
	);

	private static $casting = array(
		'Rate' => 'Currency'
	);

	public function getCalculator(Order $order) {
		return new ShippingCalculator($this, $order);
	}

	public function calculateRate(ShippingPackage $package, Address $address) {
		return null;
	}

	public function getRate() {
		return $this->CalculatedRate;
	}

	public function getTitle() {
		return implode(" - ", array_filter(array(
			$this->Rate,
			$this->Name,
			$this->Description
		)));
	}

}

/**
 * Helper class for encapsulating shipping calculation logic.
 */
class ShippingCalculator{

	protected $method;
	protected $order;

	function __construct(ShippingMethod $method, Order $order) {
		$this->method = $method;
		$this->order = $order;
	}

	function calculate($address = null) {
		return $this->method->calculateRate(
			$this->order->createShippingPackage(),
			$address ? $address : $this->order->ShippingAddress()
		);
	}

}
