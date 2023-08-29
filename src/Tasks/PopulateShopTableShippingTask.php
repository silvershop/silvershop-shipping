<?php

namespace SilverShop\Shipping\Tasks;

use SilverStripe\Core\Extension;
use SilverShop\Shipping\Tasks\PopulateTableShippingTask;

/**
 * Makes PopulateTableShippingTask get run before PopulateShopTask is run
 *
 * @package silvershop-shipping
 */
class PopulateShopTableShippingTask extends Extension
{
    public function beforePopulate()
    {
        $task = new PopulateTableShippingTask();
        $task->run();
    }
}
