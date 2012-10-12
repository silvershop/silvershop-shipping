<?php
class ShippingOptionAdmin extends ModelAdmin{

	static $url_segment = "shipping-option";
	static $menu_title = "Shipping Options";
	static $menu_priority = 6;

	static $managed_models = array(
		'ShippingOption'
	);

}