<?php

namespace SilverShop\Shipping\Model;

use SilverStripe\ORM\DataObject;
use SilverShop\Model\Address;
use SilverStripe\Forms\FieldList;

class Warehouse extends DataObject
{
    private static array $db = [
        'Title' => 'Varchar(255)'
    ];

    private static array $has_one = [
        'Address' => Address::class
    ];

    private static array $summary_fields = [
        'Title',
        'Address.Title' => 'Address'
    ];

    private static string $table_name = 'SilverShop_Warehouse';

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();
        $fields->removeByName("AddressID");
        return $fields;
    }

    /**
     * Get the closest warehouse to an address.
     */
    public static function closest_to(Address $address): Warehouse
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
