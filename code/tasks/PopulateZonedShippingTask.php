<?php

class PopulateZonedShippingTask extends BuildTask{

	protected $title = "Populate Zoned Shipping Methods";
	protected $description = 'If no zoned shipping methods exist, it creates some.';

	function run($request = null){
		if(!DataObject::get_one('ZonedShippingMethod')){
			$fixture = new YamlFixture('shop_shippingframework/tests/fixtures/ZonedShippingMethod.yml');
			$fixture->saveIntoDatabase();
			DB::alteration_message('Created zoned shipping methods', 'created');
		}else{
			DB::alteration_message('Some zoned shipping methods already exist. None were created.');
		}
	}

}

/**
 * Makes PopulateZonedShippingTask get run before PopulateShopTask is run
 */
class PopulateShopZonedShippingTask extends Extension{

	function beforePopulate(){
		$task = new PopulateZonedShippingTask();
		$task->run();
	}

}