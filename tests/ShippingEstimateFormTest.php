<?php

class ShippingEstimateFormTest extends FunctionalTest{
	
	static $fixturefile = array(
		"shop_shippingframework/tests/fixtures/TableShippingOption.yml"
	);
	
	function testGetEstimates(){
		
		$form = new ShippingEstimateForm(new Controller());
		$data = array(
			'Country' => 'NZ',
			'State' => 'Auckland',
			'City' => 'Auckland',
			'PostalCode' => 1010
		);
		$form->loadDataFrom($data);
		//$form->submit($data,$form); //can't handle redirects
		
	}
	
	//test estimate from address?
	
}