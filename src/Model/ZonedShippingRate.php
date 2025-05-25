<?php

namespace SilverShop\Shipping\Model;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;

/**
 * @property float $WeightMin
 * @property float $WeightMax
 * @property float $VolumeMin
 * @property float $VolumeMax
 * @property float $ValueMin
 * @property float $ValueMax
 * @property int $QuantityMin
 * @property int $QuantityMax
 * @property float $Rate
 * @property int $ZoneID
 * @property int $ZonedShippingMethodID
 * @method Zone Zone()
 * @method ZonedShippingMethod ZonedShippingMethod()
 */
class ZonedShippingRate extends DataObject
{
    private static array $db = [
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

    private static array $has_one = [
        'Zone' => Zone::class,
        'ZonedShippingMethod' => ZonedShippingMethod::class
    ];

    private static array $summary_fields = [
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

    private static string $default_sort = '"Rate" ASC';

    private static string $table_name = 'SilverShop_ZonedShippingRate';

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('ZonedShippingMethodID');

        return $fields;
    }
}
