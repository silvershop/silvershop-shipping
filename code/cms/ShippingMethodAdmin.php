<?php
class ShippingMethodAdmin extends ModelAdmin{

	private static $url_segment = "shipping";
	private static $menu_title = "Shipping";
	private static $menu_priority = 2;
	private static $menu_icon = 'shop_shipping/images/shipping.png';

	private static $managed_models = array(
		'ShippingMethod'
	);

	public static $model_importers = array();

	public function getEditForm($id = null, $fields = null) {
		$form = parent::getEditForm($id, $fields);
		if($this->modelClass === "ShippingMethod"){
			$gridfield = $form->Fields()->fieldByName($this->sanitiseClassName($this->modelClass));
			$config = $gridfield->getConfig();
			$config->removeComponentsByType("GridFieldAddNewButton");
			$config->removeComponentsByType("GridFieldPrintButton");
			$config->removeComponentsByType("GridFieldExportButton");
			$config->addComponent($multiclass = new GridFieldAddNewMultiClass());
			$classes = ClassInfo::subclassesFor($this->modelClass);
			unset($classes[$this->modelClass]);
			$multiclass->setClasses($classes);
		}
		return $form;
	}
	
}