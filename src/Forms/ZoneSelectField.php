<?php

namespace SilverShop\Shipping\Forms;

use SilverShop\Shipping\Zone;
use SilverStripe\Forms\DropdownField;

class ZoneSelectField extends DropdownField
{
    public function getSource(): array
    {
        $zones = Zone::get();

        if ($zones && $zones->exists()) {
            return ['' => $this->emptyString] + $zones->map('ID', 'Name');
        }

        return [];
    }
}
