<?php

namespace SilverShop\Shipping\Model;

use SilverStripe\ORM\DataObject;
use SilverShop\Shipping\Model\DistanceShippingMethod;

/**
 * @property float $Distance
 * @property float $Cost
 * @property int $ShippingMethodID
 * @method DistanceShippingMethod ShippingMethod()
 */
class DistanceShippingFare extends DataObject
{
    private static array $db = [
        'Distance' => 'Float',
        'Cost' => 'Currency'
    ];

    private static array $has_one = [
        'ShippingMethod' => DistanceShippingMethod::class
    ];

    private static array $summary_fields = [
        'MinDistance',
        'Distance',
        'Cost'
    ];

    private static array $field_labels = [
        'MinDistance' => 'Min Distance (km)',
        'Distance' => 'Max Distance (km)',
        'Cost' => 'Cost'
    ];

    private static string $singular_name = "Fare";

    private static string $default_sort = '"Distance" ASC';

    private static string $table_name = 'SilverShop_DistanceShippingFare';

    public function getMinDistance()
    {
        if ($dfare = self::get()
            ->filter("Distance:LessThan", $this->Distance)
            ->filter("ShippingMethodID", $this->ShippingMethodID)
            ->sort("Distance", "DESC")
            ->first()) {
            return $dfare->Distance;
        }
        return 0;
    }
}
