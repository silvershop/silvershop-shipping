<?php

namespace SilverShop\Shipping\Tests;

use SilverShop\Shipping\Model\RegionRestriction;
use SilverStripe\Dev\TestOnly;

class RegionRestrictionRate extends RegionRestriction implements TestOnly
{
    private static $db = [
        'Rate' => 'Currency',
    ];
}
