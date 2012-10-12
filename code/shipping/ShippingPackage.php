<?php
/*
 * Encapsulation of shipping package data
 * 
 * @todo: unit conversion support
 * @todo: different shape support (eg cylinder) ...with ability to roll back to 'box' if need be
 */
class ShippingPackage{
	
	protected $weight;
	protected $height, $length, $thickness, $diameter;
	protected $value, $currency;
	protected $itemcount;
	
	protected $defaultdimensions = array(
		'height' => null,
		'length' => null,
		'thickness' => null,
		'diameter' => null
	);
	
	protected $defaultoptions = array(
		'value' => null,
		'itemcount' => 1,
		'shape' => 'box',
		'weightunit' => 'kg',
		'lengthunit' => 'cm',
	);
	
	protected $dimensionaliases = array(
		0 => 'height',
		1 => 'length',
		2 => 'thickness',
		'h' => 'height',
		'l' => 'length',
		't' => 'thickness'
	);
	
	function __construct($weight = 1, $dimensions = array(), $options = array()){
		$this->weight = $weight;
		//set via aliases
		foreach($dimensions as $key => $dimension){
			if(isset($this->dimensionaliases[$key])){
				$dimensions[$this->dimensionaliases[$key]] = $dimension;
			}
		}
		$d = array_merge($this->defaultdimensions, $dimensions);
		foreach($this->defaultdimensions as $name => $dimension){
			if(isset($d[$name]))
				$this->$name = (float)$d[$name]; //force float type for dimensions
		}
		$o = array_merge($this->defaultoptions, $options);
		foreach($this->defaultoptions as $name => $option){
			if(isset($o[$name]))
				$this->$name = $o[$name];
		}
	}
	
	function toArray(){
		$data = array(
			"w" => $this->weight,
			"h" => $this->height,
			"l" => $this->length,
			"t" => $this->thickness,
			"d" => $this->diameter,
			"v" => $this->value,
			"c" => $this->currency,
			"i" => $this->itemcount
		);
		return array_filter($data);
	}
	
	function __toString(){
		$out = "";
		foreach($this->toArray() as $key => $value){
			$out .= strtoupper($key).$value;
		}
		return $out;
	}
	
	/**
	 * Calculate total volume, based on given dimensions
	 */
	function volume(){
		return $this->height * $this->length * $this->thickness;
	}
	
	function weight(){
		return $this->weight;
	}

	function height(){
		return $this->height;
	}
	
	function length(){
		return $this->length;
	}
	
	function thickness(){
		return $this->thickness;
	}
	
	function value(){
		return $this->value;
	}
	
}