<?php

namespace SilverShop\Shipping\Tests;

use SilverShop\Shipping\Model\RegionRestriction;
use SilverStripe\Dev\TestOnly;

class RegionRestrictionRate extends RegionRestriction implements TestOnly
{
    private static array $db = [
        'Rate' => 'Currency',
    ];

    private static string $table_name = 'SilverShop_RegionRestrictionRate_TestOnly';
}
