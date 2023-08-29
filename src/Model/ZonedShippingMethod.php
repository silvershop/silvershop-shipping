<?php

namespace SilverShop\Shipping\Model;

use SilverShop\Shipping\ShippingPackage;
use SilverShop\Model\Address;
use SilverShop\Shipping\Model\Zone;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldDataColumns;

/**
 * Zoned shipping is a variant of TableShipping that regionalizes using zones,
 * which are collections of regions, rather than regionalising using specific
 * locations / wildcards.
 */
class ZonedShippingMethod extends ShippingMethod
{
    private static $defaults = [
        'Name' => 'Zoned Shipping',
        'Description' => 'Works out shipping from a pre-defined zone rates'
    ];

    private static $has_many = [
        "Rates" => ZonedShippingRate::class
    ];

    private static $table_name = 'SilverShop_ZonedShippingMethod';

    private static $singular_name = 'Zoned shipping method';

    private static $plural_name = 'Zoned shipping methods';

    public function calculateRate(ShippingPackage $package, Address $address)
    {
        $rate = null;
        $ids = Zone::get_zones_for_address($address);

        if (!$ids->exists()) {
            return $rate;
        }

        $ids = $ids->map('ID', 'ID')->toArray();
        $packageconstraints = [
            "Weight" => 'weight',
            "Volume" => 'volume',
            "Value" => 'value',
            "Quantity" => 'quantity'
        ];

        $constraintfilters = [];
        $emptyconstraint = [];

        foreach ($packageconstraints as $db => $pakval) {
            $mincol = "\"SilverShop_ZonedShippingRate\" . \"{$db}Min\"";
            $maxcol = "\"SilverShop_ZonedShippingRate\" . \"{$db}Max\"";
            $constraintfilters[] = "(" .
                "$mincol >= 0" .
                " AND $mincol <= " . $package->{$pakval}() .
                " AND $maxcol > 0" . //ignore constraints with maxvalue = 0
                " AND $maxcol >= " . $package->{$pakval}() .
                " AND $mincol < $maxcol" . //sanity check
            ")";
            //also include a special case where all constraints are empty
            $emptyconstraint[] = "($mincol = 0 AND $maxcol = 0)";
        }
        $constraintfilters[] = "(" . implode(" AND ", $emptyconstraint) . ")";

        $filter = "(" . implode(") AND (", [
            "\"ZonedShippingMethodID\" = " . $this->ID,
            "\"ZoneID\" IN(" . implode(",", $ids) . ")", //zone restriction
            implode(" OR ", $constraintfilters) //metrics restriction
        ]) . ")";

        if ($sr = ZonedShippingRate::get()->where($filter)->sort('Rate')->first()) {
            $rate = $sr->Rate;
        }

        $this->CalculatedRate = $rate;

        return $rate;
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $displayFieldsList = [
            "ZoneID" =>  "Zone",
            "WeightMin" => "WeightMin",
            "WeightMax" => "WeightMax",
            "VolumeMin" => "VolumeMin",
            "VolumeMax" => "VolumeMax",
            "ValueMin" => "ValueMin",
            "ValueMax" => "ValueMax",
            "QuantityMin" => "QuantityMin",
            "QuantityMax" => "QuantityMax",
            "Rate" => "Rate"
        ];

        $fields->fieldByName('Root')->removeByName("Rates");
        if ($this->isInDB()) {
            $config = new GridFieldConfig_RelationEditor();
            $gridField = new GridField(
                "Rates",
                "ZonedShippingRate",
                $this->Rates(),
                $config
            );

            $config->getComponentByType(GridFieldDataColumns::class)
                ->setDisplayFields($displayFieldsList);

            $fields->addFieldToTab("Root.Main", $gridField);
        }

        return $fields;
    }

    /**
     * @return bool
     */
    public function requiresAddress()
    {
        return true;
    }
}
