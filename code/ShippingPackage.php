<?php
/*
 * Encapsulation of shipping package data
 * 
 * @todo: unit conversion support
 * @todo: different shape support (eg cylinder) ...with ability to roll back to 'box' if need be
 */
class ShippingPackage{
	
	protected $weight;
	protected $height, $width, $depth, $diameter;
	protected $value, $currency;
	protected $quantity;
	
	protected $defaultdimensions = array(
		'height' => 0,
		'width' => 0,
		'depth' => 0,
		'diameter' => 0
	);
	
	protected $defaultoptions = array(
		'value' => 0,
		'quantity' => 0,
		'shape' => 'box',
		'weightunit' => 'kg',
		'widthunit' => 'cm',
	);
	
	protected $dimensionaliases = array(
		0 => 'height',
		1 => 'width',
		2 => 'depth',
		'h' => 'height',
		'w' => 'width',
		'd' => 'depth'
	);
	
	function __construct($weight = 0, $dimensions = array(), $options = array()){
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
		//force 0 values for anything below 0
		$zerochecks = array_merge($this->defaultdimensions,array('value' => null, 'quantity' => null));
		foreach($zerochecks as $dimension => $value){
			if($this->$dimension < 0){
				$this->$dimension = 0;
			}
		}
	}
	
	function toArray(){
		$data = array(
			"weight" => $this->weight,
			"height" => $this->height,
			"width" => $this->width,
			"depth" => $this->depth,
			"diameter" => $this->diameter,
			"value" => $this->value,
			"currency" => $this->currency,
			"quantity" => $this->quantity
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
		return $this->height * $this->width * $this->depth;
	}
	
	function weight(){
		return $this->weight;
	}

	function height(){
		return $this->height;
	}
	
	function width(){
		return $this->width;
	}
	
	function depth(){
		return $this->depth;
	}
	
	function value(){
		return $this->value;
	}
	
	function quantity(){
		return $this->quantity;
	}

}