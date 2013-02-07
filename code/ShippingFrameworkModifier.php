<?php

class ShippingFrameworkModifier extends ShippingModifier{
	
	function value($incoming){
		$order = $this->Order();
		if($order && $order->exists() && $shipping = $order->ShippingMethod()){
			return $shipping->calculateRate($order->createShippingPackage(),$order->ShippingAddress());
		}
		return 0;
	}
	
	function TableTitle(){
		$title = $this->i18n_singular_name();
		if($this->Order() && $this->Order()->ShippingMethod()->exists()){
			$title .= " (".$this->Order()->ShippingMethod()->Name.")";
		}
		return $title;
	}
	
}