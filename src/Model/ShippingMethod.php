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
 *
 * @property ?string $Name
 * @property ?string $Description
 * @property bool $Enabled
 */
class ShippingMethod extends DataObject
{
    private static array $db = [
        "Name" => "Varchar",
        "Description" => "Text",
        "Enabled" => "Boolean"
    ];

    private static array $casting = [
        'Rate' => 'Currency'
    ];

    private static string $table_name = 'SilverShop_ShippingMethod';

    /**
     * @var array Checked in ShippingMethodAdmin when adding methods
     */
    private static array $disable_methods = [];

    protected float|int|null $CalculatedRate = null;

    public function getCalculator(Order $order): ShippingCalculator
    {
        return new ShippingCalculator($this, $order);
    }

    public function calculateRate(ShippingPackage $package, Address $address): float|int|null
    {
        return $this->CalculatedRate = null;
    }

    public function getRate(): float|int|null
    {
        return $this->CalculatedRate;
    }

    public function getTitle(): string
    {
        $rate = number_format(
            $this->Rate ?? 0,
            2,
            ShopCurrency::config()->decimal_delimiter,
            ShopCurrency::config()->thousand_delimiter
        );

        if (ShopCurrency::config()->append_symbol) {
            $rate = $rate . ' ' . ShopCurrency::config()->currency_symbol;
        } else {
            $rate = ShopCurrency::config()->currency_symbol . $rate;
        }

        $title = implode(
            " - ",
            array_filter(
                [
                $rate,
                $this->Name,
                $this->Description
                ]
            )
        );

        $this->extend('updateTitle', $title);
        return $title;
    }

    /**
     * Some shipping methods might require an address present on the order.
     */
    public function requiresAddress(): bool
    {
        return false;
    }
}
