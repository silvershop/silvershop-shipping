<?php

class OrderShippingDecorator extends DataExtension{
	
	static $db = array(
		'ShippingTotal' => 'Currency'
	);
	
	static $has_one = array(
			'ShippingMethod' => 'ShippingMethod'
	);

	
	function createShippingPackage(){
		//create package, with total weight, dimensions, value, etc
		$weight = $width = $height = $depth = $value = $quantity = 0;
		
		$items = $this->owner->Items();
		
		$weight = $items->orderItemsSum('Weight',true); //Sum is found on OrdItemList (Component Extension)
		$width = $items->orderItemsSum('Width',true);
		$height = $items->orderItemsSum('Height',true);
		$depth = $items->orderItemsSum('Depth',true);
		
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
	
}