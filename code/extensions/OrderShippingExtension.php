<?php

/**
 * @package silvershop-shipping
 */
class OrderShippingExtension extends DataExtension
{

	private static $db = array(
		'ShippingTotal' => 'Currency'
	);
	private static $has_one = array(
		'ShippingMethod' => 'ShippingMethod'
	);

	public function createShippingPackage() {
		//create package, with total weight, dimensions, value, etc
		$weight = $width = $height = $depth = $value = $quantity = 0;

		$items = $this->owner->Items();
		if(!$items->exists()){
			return new ShippingPackage();
		}

		$weight = $items->Sum('Weight', true); //Sum is found on OrdItemList (Component Extension)
		$width = $items->Sum('Width', true);
		$height = $items->Sum('Height', true);
		$depth = $items->Sum('Depth', true);

		$value = $this->owner->SubTotal();
		$quantity = $items->Quantity();

		$package = new ShippingPackage($weight,
			array($height,$width,$depth),
			array(
				'value' => $value,
				'quantity' => $quantity
			)
		);
		return $package;
	}

	/**
	 * Get shipping estimates
	 * @return DataList
	 */
	public function getShippingEstimates() {
		//$package = $this->order->createShippingPackage();
		$address = $this->owner->getShippingAddress();
		$estimator = new ShippingEstimator($this->owner, $address);
		$estimates = $estimator->getEstimates();
		return $estimates;
	}

	/*
	 * Set shipping method and shipping cost
	 * @param $option - shipping option to set, and calculate shipping from
	 * @return boolean sucess/failure of setting
	 */
	public function setShippingMethod(ShippingMethod $option) {
		$package = $this->owner->createShippingPackage();
		if(!$package){
			return $this->error(
				_t("Checkout.NOPACKAGE", "Shipping package information not available")
			);
		}
		$address = $this->owner->getShippingAddress();
		if(!$address || !$address->exists()){
			return $this->error(
				_t("Checkout.NOADDRESS", "No address has been set")
			);
		}
		$this->owner->ShippingTotal = $option->calculateRate($package, $address);
		$this->owner->ShippingMethodID = $option->ID;
		$this->owner->write();
		return true;
	}


}
