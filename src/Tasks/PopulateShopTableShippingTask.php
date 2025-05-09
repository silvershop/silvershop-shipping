<?php

namespace SilverShop\Shipping\Tasks;

use SilverShop\Shipping\Tasks\PopulateTableShippingTask;
use SilverShop\Tasks\PopulateShopTask;
use SilverStripe\Core\Extension;

/**
 * Makes PopulateTableShippingTask get run before PopulateShopTask is run
 *
 * @package silvershop-shipping
 * @extends Extension<(PopulateShopTask & static)>
 */
class PopulateShopTableShippingTask extends Extension
{
    public function beforePopulate(): void
    {
        $task = PopulateTableShippingTask::create();
        $task->run();
    }
}
