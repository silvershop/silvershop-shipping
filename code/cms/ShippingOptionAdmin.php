<?php
class ShippingOptionAdmin extends ModelAdmin{

	static $url_segment = "shipping-option";
	static $menu_title = "Shipping Options";
	static $menu_priority = 6;

	static $managed_models = array(
		'ShippingOption' => array(
			'collection_controller' => 'ShippingOptionAdmin_CollectionController'	
		)
	);

	public static $model_importers = array();
	
}

class ShippingOptionAdmin_CollectionController extends ModelAdmin_CollectionController{
	
	function CreateForm(){
		$form = parent::CreateForm();
		$options = ClassInfo::subclassesFor("ShippingOption");
		unset($options[0]);
		$form->Fields()->push(new DropdownField("ClassName","Type",$options));
		return $form;
	}
	
	
}

