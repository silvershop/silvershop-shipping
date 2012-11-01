<?php

class OrderShippingDecorator extends DataObjectDecorator{
	
	function extraStatics(){
		return array(
			'db' => array(
				'ShippingTotal' => 'Currency'
			),
			'has_one' => array(
				'ShippingMethod' => 'ShippingMethod'
			)
		);
	}
	
	function createShippingPackage(){
		//create package, with total weight, dimensions, value, etc
		$weight = $width = $height = $depth = $value = $quantity = 0;
		
		$items = $this->owner->Items();
		
		$weight = $items->Sum('Weight',true); //Sum is found on OrdItemList (Component Extension)
		$width = $items->Sum('Width',true);
		$height = $items->Sum('Height',true);
		$depth = $items->Sum('Depth',true);
		
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