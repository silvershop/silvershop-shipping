<?php

namespace SilverShop\Shipping\Admin;

use SilverShop\Shipping\Model\Zone;
use SilverStripe\Admin\ModelAdmin;

class ZoneAdmin extends ModelAdmin
{
    private static string $menu_title = 'Zones';

    private static string $url_segment = 'zones';

    private static int $menu_priority = 2;

    private static array $managed_models = [
        Zone::class,
    ];
}
