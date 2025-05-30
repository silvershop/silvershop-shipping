<?php

namespace SilverShop\Shipping\Tasks;

use SilverShop\Extension\ShopConfigExtension;
use SilverShop\Shipping\Model\Zone;
use SilverShop\Shipping\Model\ZoneRegion;
use SilverStripe\Dev\BuildTask;

class CreateInternationalZoneTask extends BuildTask
{
    protected $title = 'Create International Zone';

    protected $description = 'Quickly creates an international zone, based on all available countries.';

    public function run($request): void
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
            echo '.';
        }
    }
}
