<?php

/**
 * @package silvershop-shipping
 */
class ShippingEstimateForm extends Form
{

	function __construct($controller, $name = "ShippingEstimateForm") {
		$address = new Address();  // get address to access it's getCountryField method
		$fields = new FieldList(
			$address->getCountryField(),
			TextField::create('State', _t('Address.db_State', 'State')),
			TextField::create('City', _t('Address.db_City', 'City')),
			TextField::create('PostalCode', _t('Address.db_PostalCode', 'Postal Code'))
		);
		$actions =  new FieldList(
			FormAction::create(
                "submit",
                _t('ShippingEstimateForm.FormActionTitle', 'Estimate')
            )
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
			$estimator = ShippingEstimator::create(
				$order,
				new Address(Convert::raw2sql($data))
			);
			$estimates = $estimator->getEstimates();
			if(!$estimates->exists()){
				$form->sessionMessage(
                    _t('ShippingEstimateForm.FormActionWarningMessage', 'No estimates could be found for that location.'),
                    _t('ShippingEstimateForm.FormActionWarningCode', "warning")
                );
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
