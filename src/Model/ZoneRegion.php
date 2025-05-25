<?php

namespace SilverShop\Shipping\Model;

/**
 * Class ZoneRegion
 *
 * @property int $ZoneID
 * @method   Zone Zone()
 */
class ZoneRegion extends RegionRestriction
{
    private static array $has_one = [
        'Zone' => Zone::class
    ];

    private static string $table_name = 'SilverShop_ZoneRegion';
}
