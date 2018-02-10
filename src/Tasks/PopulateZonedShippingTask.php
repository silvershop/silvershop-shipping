<?php

namespace SilverShop\Shipping\Tasks;

use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DataObject;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\YamlFixture;
use SilverStripe\ORM\DB;
use SilverStripe\Core\Extension;

/**
 * @package silvershop-shipping
 */
class PopulateZonedShippingTask extends BuildTask
{
    protected $title = "Populate Zoned Shipping Methods";

    protected $description = 'If no zoned shipping methods exist, it creates some.';

    public function run($request = null)
    {
        if (!DataObject::get_one('ZonedShippingMethod')) {
            $factory = Injector::inst()->create('FixtureFactory');
            $fixture = new YamlFixture('silvershop/shipping:tests/ZonedShippingMethod.yml');
            $fixture->writeInto($factory);
            DB::alteration_message('Created zoned shipping methods', 'created');
        } else {
            DB::alteration_message('Some zoned shipping methods already exist. None were created.');
        }
    }
}
