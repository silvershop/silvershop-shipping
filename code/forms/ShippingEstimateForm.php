<?php

class ShippingEstimateForm extends Form{
	
	function __construct($controller, $name = "ShippingEstimateForm") {
		$address = new Address();  // get address to access it's getCountryField method
		$fields = new FieldList(
			$address->getCountryField(),
			TextField::create('State', _t('Address.STATE', 'State')),
			TextField::create('City', _t('Address.CITY', 'City')),
			TextField::create('PostalCode', _t('Address.POSTALCODE', 'Postal Code'))
		);
		$actions =  new FieldList(
			FormAction::create("submit", "Estimate")
		);
		$validator = new RequiredFields(array(
			'Country'
		));
		parent::__construct($controller, $name, $fields, $actions, $validator);
		$this->extend('updateForm');
	}
	
	function submit($data, $form) {
		if($country = SiteConfig::current_site_config()->getSingleCountry()){  // Add Country if missing due to ReadonlyField in form
			$data['Country'] = $country;
		}
		if($order = ShoppingCart::singleton()->current()){
			$estimator = new ShippingEstimator(
				$order,
				new Address(Convert::raw2sql($data))
			);
			$estimates = $estimator->getEstimates();
			if(!$estimates->exists()){
				$form->sessionMessage("No estimates could be found for that location.","warning");
			}
			Session::set("ShippingEstimates", $estimates);
			if(Director::is_ajax()){
				//TODO: replace with an AJAXResponse class that can output to different formats
				return json_encode($estimates->toArray());
			}
		}
		$this->controller->redirectBack();
	}
	
}