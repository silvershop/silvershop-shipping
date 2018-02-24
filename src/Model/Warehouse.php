<?php

namespace SilverShop\Shipping\Model;

use SilverStripe\ORM\DataObject;
use SilverShop\Model\Address;

class Warehouse extends DataObject
{
    private static $db = [
        'Title' => 'Varchar(255)'
    ];

    private static $has_one = [
        'Address' => Address::class
    ];

    private static $summary_fields = [
        'Title',
        'Address'
    ];

    private static $table_name = 'SilverShop_Warehouse';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName("AddressID");

        return $fields;
    }

    /**
     * Get the closest warehouse to an address.
     *
     * @param  Address $address
     * @return Warehouse
     */
    public static function closest_to(Address $address)
    {
        $warehouses = self::get()
            ->where("\"AddressID\" IS NOT NULL");
        $closestwarehouse = null;
        $shortestdistance = null;

        foreach ($warehouses as $warehouse) {
            $dist = $warehouse->Address()->distanceTo($address);

            if ($dist && ($shortestdistance === null || $dist < $shortestdistance)) {
                $closestwarehouse = $warehouse;
                $shortestdistance = $dist;
            }
        }

        return $closestwarehouse;
    }
}
