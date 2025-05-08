<?php

namespace SilverShop\Shipping\Model;

use SilverShop\Shipping\Model\TableShippingMethod;
use SilverShop\Shipping\Model\RegionRestriction;
use SilverStripe\Forms\FieldList;

/**
 * Adds extra metric ranges to restrict with, rather than just region.
 */
class TableShippingRate extends RegionRestriction
{
    private static array $db = [
        "WeightMin"   => "Decimal",
        "WeightMax"   => "Decimal",
        "VolumeMin"   => "Decimal",
        "VolumeMax"   => "Decimal",
        "ValueMin"    => "Currency",
        "ValueMax"    => "Currency",
        "QuantityMin" => "Int",
        "QuantityMax" => "Int",
        "Rate" => "Currency"
    ];

    private static array $has_one = [
        "ShippingMethod" => TableShippingMethod::class
    ];

    private static array $summary_fields = [
        'Country',
        'State',
        'City',
        'PostalCode',
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

    private static string $default_sort = "\"Country\" ASC, \"State\" ASC, \"City\" ASC, \"PostalCode\" ASC, \"Rate\" ASC";

    private static string $table_name = 'SilverShop_TableShippingRate';

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('ShippingMethodID');
        return $fields;
    }
}
