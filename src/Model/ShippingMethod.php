<?php

namespace SilverShop\Shipping\Model;

use SilverStripe\ORM\DataObject;
use SilverShop\Model\Order;
use SilverShop\Shipping\ShippingPackage;
use SilverShop\Model\Address;

/**
 * ShippingMethod is a base class for providing shipping options to customers.
 */
class ShippingMethod extends DataObject
{
    private static $db = [
        "Name" => "Varchar",
        "Description" => "Text",
        "Enabled" => "Boolean"
    ];

    private static $casting = [
        'Rate' => 'Currency'
    ];

    private static $table_name = 'SilverShop_ShippingMethod';

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
        $title = implode(" - ", array_filter([
            $this->Rate,
            $this->Name,
            $this->Description
        ]));

        $this->extend('updateTitle', $title);
        return $title;
    }

    /**
     * Some shipping methods might require an address present on the order.
     *
     * @return bool
     */
    public function requiresAddress()
    {
        return false;
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
