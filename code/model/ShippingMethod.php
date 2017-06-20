<?php

/**
 * ShippingMethod is a base class for providing shipping options to customers.
 *
 * @package silvershop-shipping
 */
class ShippingMethod extends DataObject
{
    private static $db = array(
        "Name" => "Varchar",
        "Description" => "Text",
        "Enabled" => "Boolean"
    );

    private static $casting = array(
        'Rate' => 'Currency'
    );

    public function getCalculator(Order $order)
    {
        return new ShippingCalculator($this, $order);
    }

    public function calculateRate(ShippingPackage $package, Address $address)
    {
        return null;
    }

    public function getRate()
    {
        return $this->CalculatedRate;
    }

    public function getTitle()
    {
        $title = implode(" - ", array_filter(array(
            $this->Rate,
            $this->Name,
            $this->Description
        )));

        $this->extend('updateTitle', $title);
        return $title;
    }
}

/**
 * Helper class for encapsulating shipping calculation logic.
 */
class ShippingCalculator
{
    protected $method;
    protected $order;

    public function __construct(ShippingMethod $method, Order $order)
    {
        $this->method = $method;
        $this->order = $order;
    }

    public function calculate($address = null)
    {
        return $this->method->calculateRate(
            $this->order->createShippingPackage(),
            $address ? $address : $this->order->getShippingAddress()
        );
    }
}
