<?php

namespace SilverShop\Shipping\Model;

use SilverStripe\ORM\DataObject;
use SilverShop\Model\Order;
use SilverShop\ORM\FieldType\ShopCurrency;
use SilverShop\Shipping\ShippingPackage;
use SilverShop\Model\Address;
use SilverShop\Shipping\ShippingCalculator;

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

    /**
     * @var array Checked in ShippingMethodAdmin when adding methods
     */
    private static $disable_methods = [];

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
        $rate = number_format(
            $this->Rate,
            2,
            ShopCurrency::config()->decimal_delimiter,
            ShopCurrency::config()->thousand_delimiter
        );

        if (ShopCurrency::config()->append_symbol) {
            $rate = $rate . ' ' . ShopCurrency::config()->currency_symbol;
        } else {
            $rate = ShopCurrency::config()->currency_symbol . $rate;
        }

        $title = implode(" - ", array_filter([
            $rate,
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
