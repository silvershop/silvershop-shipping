<?php
class ShippingMethodAdmin extends ModelAdmin{

	static $url_segment = "shipping-option";
	static $menu_title = "Shipping";
	static $menu_priority = 2;

	static $managed_models = array(
		'ShippingMethod'
	);

	public static $model_importers = array();

	//TODO: allow choosing what kind of shipping method to create
	
}