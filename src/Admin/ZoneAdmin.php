<?php

namespace SilverShop\Shipping\Admin;

use SilverShop\Shipping\Model\Zone;
use SilverStripe\Admin\ModelAdmin;

class ZoneAdmin extends ModelAdmin
{
    private static $menu_title = 'Zones';

    private static $url_segment = 'zones';

    private static $menu_priority = 2;

    private static $managed_models = [
        Zone::class,
    ];
}
