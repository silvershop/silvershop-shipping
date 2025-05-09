<?php

namespace SilverShop\Shipping\Model;

use SilverShop\Model\Address;
use SilverShop\Shipping\Model\Zone;
use SilverShop\Shipping\ShippingPackage;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\ORM\HasManyList;

/**
 * Zoned shipping is a variant of TableShipping that regionalizes using zones,
 * which are collections of regions, rather than regionalising using specific
 * locations / wildcards.
 *
 * @method HasManyList<ZonedShippingRate> Rates()
 */
class ZonedShippingMethod extends ShippingMethod
{
    private static array $defaults = [
        'Name' => 'Zoned Shipping',
        'Description' => 'Works out shipping from a pre-defined zone rates'
    ];

    private static array $has_many = [
        "Rates" => ZonedShippingRate::class
    ];

    private static string $table_name = 'SilverShop_ZonedShippingMethod';

    private static string $singular_name = 'Zoned shipping method';

    private static string $plural_name = 'Zoned shipping methods';

    public function calculateRate(ShippingPackage $package, Address $address): null
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

        $filter = "(" . implode(
            ") AND (",
            [
                "\"ZonedShippingMethodID\" = " . $this->ID,
                "\"ZoneID\" IN(" . implode(",", $ids) . ")", //zone restriction
                implode(" OR ", $constraintfilters) //metrics restriction
            ]
        ) . ")";

        if ($sr = ZonedShippingRate::get()->where($filter)->sort('Rate')->first()) {
            $rate = $sr->Rate;
        }

        $this->CalculatedRate = $rate;

        return $rate;
    }

    public function getCMSFields(): FieldList
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
            $config = GridFieldConfig_RelationEditor::create();
            $gridField = GridField::create("Rates", "ZonedShippingRate", $this->Rates(), $config);

            $config->getComponentByType(GridFieldDataColumns::class)
                ->setDisplayFields($displayFieldsList);

            $fields->addFieldToTab("Root.Main", $gridField);
        }

        return $fields;
    }

    public function requiresAddress(): bool
    {
        return true;
    }
}
