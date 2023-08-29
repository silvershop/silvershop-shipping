<?php

namespace SilverShop\Shipping\Tasks;

use SilverShop\Shipping\Model\TableShippingMethod;
use SilverStripe\Core\Manifest\ModuleResourceLoader;
use SilverStripe\Dev\BuildTask;
use SilverStripe\Dev\FixtureFactory;
use SilverStripe\ORM\DataObject;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\YamlFixture;
use SilverStripe\ORM\DB;
use SilverStripe\Core\Extension;

/**
 * @package silvershop-shipping
 */
class PopulateTableShippingTask extends BuildTask
{
    protected $title = "Populate Table Shipping Methods";

    protected $description = 'If no table shipping methods exist, it creates multiple different setups of table shipping.';

    public function run($request = null)
    {
        if (!DataObject::get_one(TableShippingMethod::class)) {
            $factory = Injector::inst()->create(FixtureFactory::class);
            $fixture = new YamlFixture(
                ModuleResourceLoader::singleton()
                    ->resolvePath('silvershop/shipping:tests/TableShippingMethod.yml')
            );
            $fixture->writeInto($factory);
            DB::alteration_message('Created table shipping methods', 'created');
        } else {
            DB::alteration_message('Some table shipping methods already exist. None were created.');
        }
    }
}
