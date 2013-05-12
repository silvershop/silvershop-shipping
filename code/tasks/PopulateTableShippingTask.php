<?php

class PopulateTableShippingTask extends BuildTask{
	
	protected $title = "Populate Table Shipping Methods";
	protected $description = 'If no table shipping methods exist, it creates multiple different setups of table shipping.';
	
	function run($request = null){
		if(!DataObject::get_one('TableShippingMethod')){
			$factory = Injector::inst()->create('FixtureFactory');
			$fixture = new YamlFixture('shop_shippingframework/tests/fixtures/TableShippingMethod.yml');
			$fixture->writeInto($factory);
			DB::alteration_message('Created table shipping methods', 'created');
		}else{
			DB::alteration_message('Some table shipping methods already exist. None were created.');
		}
	}
	
}

/**
 * Makes PopulateTableShippingTask get run before PopulateShopTask is run
 */
class PopulateShopTableShippingTask extends Extension{
	
	function beforePopulate(){
		$task = new PopulateTableShippingTask();
		$task->run();
	}
	
}
