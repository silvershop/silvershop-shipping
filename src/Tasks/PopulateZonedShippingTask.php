<?php

namespace SilverShop\Shipping\Tasks;

use SilverShop\Shipping\Model\ZonedShippingMethod;
use SilverStripe\Core\Manifest\ModuleResourceLoader;
use SilverStripe\Dev\BuildTask;
use SilverStripe\Dev\FixtureFactory;
use SilverStripe\Dev\YamlFixture;
use SilverStripe\ORM\DB;
use SilverStripe\PolyExecution\PolyOutput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

/**
 * @package silvershop-shipping
 */
class PopulateZonedShippingTask extends BuildTask
{
    protected string $title = "Populate Zoned Shipping Methods";

    protected static string $description = 'If no zoned shipping methods exist, it creates some.';

    protected function execute(InputInterface $input, PolyOutput $output): int
    {
        if (!ZonedShippingMethod::get()->first()) {
            $factory = FixtureFactory::create();
            $fixture = YamlFixture::create(
                ModuleResourceLoader::singleton()
                    ->resolvePath('silvershop/shipping:tests/ZonedShippingMethod.yml')
            );
            $fixture->writeInto($factory);
            DB::alteration_message('Created zoned shipping methods', 'created');
        } else {
            DB::alteration_message('Some zoned shipping methods already exist. None were created.');
        }

        return Command::SUCCESS;
    }
}
