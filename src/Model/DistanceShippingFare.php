<?php

namespace SilverShop\Shipping\Model;

use SilverStripe\ORM\DataObject;
use SilverShop\Shipping\Model\DistanceShippingMethod;

class DistanceShippingFare extends DataObject
{
    private static $db = [
        'Distance' => 'Float',
        'Cost' => 'Currency'
    ];

    private static $has_one = [
        'ShippingMethod' => DistanceShippingMethod::class
    ];

    private static $summary_fields = [
        'MinDistance',
        'Distance',
        'Cost'
    ];

    private static $field_labels = [
        'MinDistance' => 'Min Distance (km)',
        'Distance' => 'Max Distance (km)',
        'Cost' => 'Cost'
    ];

    private static $singular_name = "Fare";

    private static $default_sort = "\"Distance\" ASC";

    private static $table_name = 'SilverShop_DistanceShippingFare';

    public function getMinDistance()
    {
        $dist = 0;
        if (
            $dfare = self::get()
            ->filter("Distance:LessThan", $this->Distance)
            ->filter("ShippingMethodID", $this->ShippingMethodID)
            ->sort("Distance", "DESC")
            ->first()
        ) {
            $dist = $dfare->Distance;
        }

        return $dist;
    }
}
