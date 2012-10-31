<?php

class ShippingFrameworkModifier extends ShippingModifier{
	
	function value($incoming){
		$order = $this->Order();
		if($order && $order->exists() && $shipping = $order->ShippingMethod()){
			return $shipping->calculateRate($order->createShippingPackage(),$order->ShippingAddress());
		}
	}
	
}