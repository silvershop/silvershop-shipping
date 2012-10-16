<?php

class PopulateTableShippingTask extends Extension{
	
	function beforePopulate(){
		if(!DataObject::get_one('TableShippingOption')){
			$fixture = new YamlFixture('shop_shippingframework/tests/fixtures/TableShippingOption.yml');
			$fixture->saveIntoDatabase();
			DB::alteration_message('Created shipping options', 'created');
		}
	}
	
}