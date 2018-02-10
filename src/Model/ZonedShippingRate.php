<?php

namespace SilverShop\Shipping\Model;

use SilverStripe\ORM\DataObject;

class ZonedShippingRate extends DataObject
{
    private static $db = [
        "WeightMin" => "Decimal",
        "WeightMax" => "Decimal",
        "VolumeMin" => "Decimal",
        "VolumeMax" => "Decimal",
        "ValueMin" => "Currency",
        "ValueMax" => "Currency",
        "QuantityMin" => "Int",
        "QuantityMax" => "Int",
        "Rate" => "Currency"
    ];

    private static $has_one = [
        'Zone' => 'Zone',
        'ZonedShippingMethod' => ZonedShippingMethod::class
    ];

    private static $summary_fields = [
        'Zone.Name' => 'Zone',
        'WeightMin',
        'WeightMax',
        'VolumeMin',
        'VolumeMax',
        'ValueMin',
        'ValueMax',
        'QuantityMin',
        'QuantityMax',
        'Rate'
    ];

    private static $default_sort = "\"Rate\" ASC";

    private static $table_name = 'SilverShop_ZonedShippingRate';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('ZonedShippingMethodID');

        return $fields;
    }
}
