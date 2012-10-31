<?php
class ShippingMethodAdmin extends ModelAdmin{

	static $url_segment = "shipping-option";
	static $menu_title = "Shipping Options";
	static $menu_priority = 6;

	static $managed_models = array(
		'ShippingMethod' => array(
			'collection_controller' => 'ShippingMethodAdmin_CollectionController'	
		)
	);

	public static $model_importers = array();
	
}

class ShippingMethodAdmin_CollectionController extends ModelAdmin_CollectionController{
	
	function CreateForm(){
		$form = parent::CreateForm();
		$options = ClassInfo::subclassesFor("ShippingMethod");
		unset($options[0]);
		$form->Fields()->push(new DropdownField("ClassName","Type",$options));
		return $form;
	}
	
	
}

