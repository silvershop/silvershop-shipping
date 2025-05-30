<?php

namespace SilverShop\Shipping;

use SilverStripe\Core\Injector\Injectable;

/**
 * Encapsulation of shipping package data
 */
class ShippingPackage
{
    use Injectable;

    protected $currency;
    protected $depth;
    protected $diameter;
    protected $height;
    protected $quantity;
    protected $shape;
    protected $value;
    protected $weight;
    protected $weightunit;
    protected $width;
    protected $widthunit;

    protected $defaultdimensions = [
        'height' => 0,
        'width' => 0,
        'depth' => 0,
        'diameter' => 0
    ];

    protected $defaultoptions = [
        'value' => 0,
        'quantity' => 0,
        'shape' => 'box',
        'weightunit' => 'kg',
        'widthunit' => 'cm',
    ];

    protected $dimensionaliases = [
        0 => 'height',
        1 => 'width',
        2 => 'depth',
        'h' => 'height',
        'w' => 'width',
        'd' => 'depth'
    ];

    public function __construct($weight = 0, array $dimensions = [], $options = [])
    {
        $this->weight = $weight;
        //set via aliases
        foreach ($dimensions as $key => $dimension) {
            if (isset($this->dimensionaliases[$key])) {
                $dimensions[$this->dimensionaliases[$key]] = $dimension;
            }
        }
        $d = array_merge($this->defaultdimensions, $dimensions);
        foreach ($this->defaultdimensions as $name => $dimension) {
            if (isset($d[$name])) {
                $this->$name = (float)$d[$name]; //force float type for dimensions
            }
        }
        $o = array_merge($this->defaultoptions, $options);
        foreach ($this->defaultoptions as $name => $option) {
            if (isset($o[$name])) {
                $this->$name = $o[$name];
            }
        }
        //force 0 values for anything below 0
        $zerochecks = array_merge(
            $this->defaultdimensions,
            ['value' => null, 'quantity' => null]
        );
        foreach (array_keys($zerochecks) as $dimension) {
            if ($this->$dimension < 0) {
                $this->$dimension = 0;
            }
        }
    }

    public function toArray(): array
    {
        $data = [
            "weight" => $this->weight,
            "height" => $this->height,
            "width" => $this->width,
            "depth" => $this->depth,
            "volume" => $this->volume(),
            "diameter" => $this->diameter,
            "value" => $this->value,
            "currency" => $this->currency,
            "quantity" => $this->quantity
        ];
        return array_filter($data);
    }

    public function __toString(): string
    {
        $out = "";
        foreach ($this->toArray() as $key => $value) {
            $out .= strtoupper($key) . $value;
        }
        return $out;
    }

    /**
     * Calculate total volume, based on given dimensions
     */
    public function volume(): int|float
    {
        return $this->height * $this->width * $this->depth;
    }

    public function weight()
    {
        return $this->weight;
    }

    public function height()
    {
        return $this->height;
    }

    public function width()
    {
        return $this->width;
    }

    public function depth()
    {
        return $this->depth;
    }

    public function value()
    {
        return $this->value;
    }

    public function quantity()
    {
        return $this->quantity;
    }
}
