<?php

class Warehouse extends DataObject{
	
	private static $db = array(
		'Title' => 'Varchar'
	);

	private static $has_one = array(
		'Address' => 'Address'
	);

}
