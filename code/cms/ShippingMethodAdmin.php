<?php
class ShippingMethodAdmin extends ModelAdmin{

	private static $url_segment = "shipping-option";
	private static $menu_title = "Shipping";
	private static $menu_priority = 2;
	private static $menu_icon = 'shop_shipping/images/shipping.png';

	private static $managed_models = array(
		'ZonedShippingMethod',
		'TableShippingMethod'
	);

	public static $model_importers = array();
	
}