<?php

class ShippingPackageTest extends SapphireTest{
	
	function testPackages(){
		
		$p = new ShippingPackage(25, array('thickness' => 4, 'height' => 23,'length' => 12));
		$this->assertEquals($p->height(),23);
		$this->assertEquals($p->length(),12);
		$this->assertEquals($p->thickness(),4);
		$this->assertEquals($p->weight(),25);
		
		$p = new ShippingPackage(25.3, array('h' => 23.7, 't' => 4, 'l' => 12.344,));
		$this->assertEquals($p->height(),23.7);
		$this->assertEquals($p->length(),12.344);
		$this->assertEquals($p->thickness(),4);
		$this->assertEquals($p->weight(),25.3);
		
		$p = new ShippingPackage(1, array(3,4,5));
		$this->assertEquals($p->height(),3);
		$this->assertEquals($p->length(),4);
		$this->assertEquals($p->thickness(),5);
		$this->assertEquals($p->volume(),60);
		
		$p = new ShippingPackage(13, array(1,1,2.5), array('shape' => 'cylinder'));
		$this->assertEquals($p->height(),1);
		$this->assertEquals($p->thickness(),2.5);
		$this->assertEquals($p->weight(),13);
		$this->assertEquals($p->volume(),2.5);
		
	}
	
}