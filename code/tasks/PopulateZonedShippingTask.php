<?php

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
            $fixture = new YamlFixture('silvershop-shipping/tests/fixtures/ZonedShippingMethod.yml');
            $fixture->writeInto($factory);
            DB::alteration_message('Created zoned shipping methods', 'created');
        } else {
            DB::alteration_message('Some zoned shipping methods already exist. None were created.');
        }
    }
}

/**
 * Makes PopulateZonedShippingTask get run before PopulateShopTask is run
 *
 * @package silvershop-shipping
 */
class PopulateShopZonedShippingTask extends Extension
{
    public function beforePopulate()
    {
        $task = new PopulateZonedShippingTask();
        $task->run();
    }
}
