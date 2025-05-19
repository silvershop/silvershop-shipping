<?php

namespace SilverShop\Shipping\Forms;

use SilverShop\Shipping\Model\Zone;
use SilverStripe\Forms\DropdownField;

class ZoneSelectField extends DropdownField
{
    public function getSource(): array
    {
        $zones = Zone::get();

        if ($zones && $zones->exists()) {
            return $zones->map('ID', 'Name')
                ->unshift('', $this->emptyString)
                ->toArray();
        }

        return [];
    }
}
