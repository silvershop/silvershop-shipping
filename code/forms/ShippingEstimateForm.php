<?php

class ShippingEstimateForm extends Form{
	
	function __construct($controller, $name = "ShippingEstimateForm"){
		$countries = SiteConfig::current_site_config()->getCountriesList();
		$countryfield = (count($countries)) ? new DropdownField("Country",_t('Address.COUNTRY','Country'),$countries) : new ReadonlyField("Country",_t('Address.COUNTRY','Country'));
		$countryfield->setHasEmptyDefault(true);
		$fields = new FieldSet(
			$countryfield,
			$statefield = new TextField('State', _t('Address.STATE','State')),
			$cityfield = new TextField('City', _t('Address.CITY','City')),
			$postcodefield = new TextField('PostalCode', _t('Address.POSTALCODE','Postal Code'))
		);
		$actions =  new FieldSet(
			new FormAction("submit","Submit")
		);
		//TODO: required: Country
		parent::__construct($controller, $name, $fields, $actions);
	}
	
	function submit($data, $form){
		if($order = ShoppingCart::singleton()->current()){
			$package = $order->createShippingPackage();
			$address = new Address();
			$form->saveInto($address);
			$estimator = new ShippingEstimator($package, $address);
			$estimates = $estimator->getEstimates();			
			Session::set("ShippingEstimates", $estimates);
		}
		if(Director::is_ajax()){
			//TODO: return list of shipping estimates
		}else{
			Director::redirectBack();
		}
	}
	
}