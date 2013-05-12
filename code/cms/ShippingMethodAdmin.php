<?php
class ShippingMethodAdmin extends ModelAdmin{

	static $url_segment = "shipping-option";
	static $menu_title = "Shipping";
	static $menu_priority = 2;

	static $managed_models = array(
		'ZonedShippingMethod',
		'TableShippingMethod'
	);

	public static $model_importers = array();
	
}