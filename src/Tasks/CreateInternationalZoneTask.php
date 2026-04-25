<?php

namespace SilverShop\Shipping\Tasks;

use SilverShop\Extension\ShopConfigExtension;
use SilverShop\Shipping\Model\Zone;
use SilverShop\Shipping\Model\ZoneRegion;
use SilverStripe\Dev\BuildTask;
use SilverStripe\PolyExecution\PolyOutput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

class CreateInternationalZoneTask extends BuildTask
{
    protected string $title = 'Create International Zone';

    protected static string $description = 'Quickly creates an international zone, based on all available countries.';

    protected function execute(InputInterface $input, PolyOutput $output): int
    {
        $zone = Zone::create();
        $zone->Name = 'International';
        $zone->Description = 'All countries';
        $zone->write();

        $countries = ShopConfigExtension::current()->getCountriesList();

        foreach ($countries as $code => $country) {
            ZoneRegion::create()->update(
                [
                    'ZoneID' => $zone->ID,
                    'Country' => $code,
                ]
            )->write();
            $output->write('.');
        }

        return Command::SUCCESS;
    }
}
