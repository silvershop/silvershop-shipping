<?php

class ShippingEstimateForm extends Form{
	
	function __construct($controller, $name = "ShippingEstimateForm"){
		$countries = SiteConfig::current_site_config()->getCountriesList();
		$countryfield = (count($countries)) ? new DropdownField("Country",_t('Address.COUNTRY','Country'),$countries) : new ReadonlyField("Country",_t('Address.COUNTRY','Country'));
		$countryfield->setHasEmptyDefault(true);
		$fields = new FieldList(
			$countryfield,
			$statefield = new TextField('State', _t('Address.STATE','State')),
			$cityfield = new TextField('City', _t('Address.CITY','City')),
			$postcodefield = new TextField('PostalCode', _t('Address.POSTALCODE','Postal Code'))
		);
		$actions =  new FieldList(
			new FormAction("submit","Submit")
		);
		$validator = new RequiredFields(array(
			'Country'
		));
		parent::__construct($controller, $name, $fields, $actions, $validator);
		$this->extend('updateForm');
	}
	
	function submit($data, $form){
		if($order = ShoppingCart::singleton()->current()){
			$package = $order->createShippingPackage();
			$address = new Address(Convert::raw2sql($data)); //escape data
			$estimator = new ShippingEstimator($package, $address);
			$estimates = $estimator->getEstimates();			
			Session::set("ShippingEstimates", $estimates);
			if(Director::is_ajax()){
				return json_encode($estimates->toArray()); //TODO: replace with an AJAXResponse class that can output to different formats
			}
		}
		Controller::curr()->redirectBack();
	}
	
}