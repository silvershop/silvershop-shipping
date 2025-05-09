<?php

namespace SilverShop\Shipping\Extension;

use Exception;
use SilverShop\Model\Order;
use SilverShop\Shipping\Model\ShippingMethod;
use SilverShop\Shipping\Model\Zone;
use SilverShop\Shipping\ShippingEstimator;
use SilverShop\Shipping\ShippingPackage;
use SilverStripe\Core\Extension;
use SilverStripe\ORM\ArrayList;

/**
 * @property float $ShippingTotal
 * @property int $ShippingMethodID
 * @method   ShippingMethod ShippingMethod()
 * @extends  Extension<Order&static>
 */
class OrderShippingExtension extends Extension
{
    private static array $db = [
        'ShippingTotal' => 'Currency'
    ];

    private static array $has_one = [
        'ShippingMethod' => ShippingMethod::class
    ];

    private static array $casting = [
        'TotalWithoutShipping' => 'Currency'
    ];

    public function TotalWithoutShipping(): int|float
    {
        return $this->owner->Total() - $this->owner->ShippingTotal;
    }

    /**
     * Create package, with total weight, dimensions, value, etc.
     */
    public function createShippingPackage(int $value = 0): ShippingPackage
    {
        $items = $this->owner->Items();

        if (!$items->exists()) {
            $package = ShippingPackage::create();
        } else {
            $weight = $items->Sum('Weight', true); //Sum is found on OrdItemList (Component Extension)
            $width = $items->Sum('Width', true);
            $height = $items->Sum('Height', true);
            $depth = $items->Sum('Depth', true);

            if (!$value) {
                $value = $this->owner->SubTotal();
            }
            $quantity = $items->Quantity();

            $package = ShippingPackage::create(
                $weight,
                [$height,$width,$depth],
                [
                    'value' => $value,
                    'quantity' => $quantity
                ]
            );
        }

        $this->owner->extend('updateShippingPackage', $package);

        return $package;
    }

    /**
     * Get shipping estimates.
     */
    public function getShippingEstimates(): ArrayList
    {
        $address = $this->owner->getShippingAddress();
        $estimator = ShippingEstimator::create($this->owner, $address);
        return $estimator->getEstimates();
    }

    /**
     * Set shipping method and shipping cost
     *
     * @param  $option ShippingMethod shipping option to set, and calculate shipping from
     * @return boolean sucess/failure of setting
     */
    public function setShippingMethod(ShippingMethod $option): bool
    {
        $package = $this->owner->createShippingPackage();

        if (!$package) {
            throw new Exception(_t("OrderShippingExtension.NoPackage", "Shipping package information not available"));
        }

        $address = $this->owner->getShippingAddress();

        if (!$address || !$address->exists() && $option->requiresAddress()) {
            throw new Exception(_t("OrderShippingExtension.NoAddress", "No address has been set"));
        }

        $this->owner->ShippingTotal = $option->calculateRate($package, $address);
        $this->owner->ShippingMethodID = $option->ID;
        $this->owner->write();

        return true;
    }

    public function onSetBillingAddress($address): static
    {
        if ($address) {
            Zone::cache_zone_ids($address);
        }

        return $this;
    }

    public function onSetShippingAddress($address): static
    {
        if ($address) {
            Zone::cache_zone_ids($address);
        }

        return $this;
    }
}
