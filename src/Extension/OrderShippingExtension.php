<?php

namespace SilverShop\Shipping\Extension;

use SilverStripe\ORM\DataExtension;
use SilverShop\Shipping\ShippingPackage;
use SilverShop\Shipping\ShippingEstimator;
use SilverShop\Shipping\Model\ShippingMethod;
use SilverShop\Shipping\Model\Zone;
use Exception;

class OrderShippingExtension extends DataExtension
{
    private static $db = [
        'ShippingTotal' => 'Currency'
    ];

    private static $has_one = [
        'ShippingMethod' => ShippingMethod::class
    ];

    public function createShippingPackage()
    {
        //create package, with total weight, dimensions, value, etc
        $weight = $width = $height = $depth = $value = $quantity = 0;

        $items = $this->owner->Items();

        if (!$items->exists()) {
            $package = ShippingPackage::create();
        } else {

            $weight = $items->Sum('Weight', true); //Sum is found on OrdItemList (Component Extension)
            $width = $items->Sum('Width', true);
            $height = $items->Sum('Height', true);
            $depth = $items->Sum('Depth', true);

            $value = $this->owner->Total();
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
     *
     * @return DataList
     */
    public function getShippingEstimates()
    {
        $address = $this->owner->getShippingAddress();
        $estimator = ShippingEstimator::create($this->owner, $address);
        $estimates = $estimator->getEstimates();

        return $estimates;
    }

    /**
     * Set shipping method and shipping cost
     *
     * @param $option - shipping option to set, and calculate shipping from
     * @return boolean sucess/failure of setting
     */
    public function setShippingMethod(ShippingMethod $option)
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

    public function onSetBillingAddress($address)
    {
        if ($address) {
            Zone::cache_zone_ids($address);
        }

        return $this;
    }

    public function onSetShippingAddress($address)
    {
        if ($address) {
            Zone::cache_zone_ids($address);
        }

        return $this;
    }
}
