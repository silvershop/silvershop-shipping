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

    public function calculateRate(ShippingPackage $package, Address $address): float|int|null
    {
        $rate = null;
        $ids = Zone::get_zones_for_address($address);

        if (!$ids->exists()) {
            return $this->CalculatedRate = $rate;
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
            $mincol = sprintf('"SilverShop_ZonedShippingRate" . "%sMin"', $db);
            $maxcol = sprintf('"SilverShop_ZonedShippingRate" . "%sMax"', $db);
            $constraintfilters[] = "(" .
                ($mincol . ' >= 0') .
                sprintf(' AND %s <= ', $mincol) . $package->{$pakval}() .
                sprintf(' AND %s > 0', $maxcol) . //ignore constraints with maxvalue = 0
                sprintf(' AND %s >= ', $maxcol) . $package->{$pakval}() .
                sprintf(' AND %s < %s', $mincol, $maxcol) . //sanity check
            ")";
            //also include a special case where all constraints are empty
            $emptyconstraint[] = sprintf('(%s = 0 AND %s = 0)', $mincol, $maxcol);
        }
        $constraintfilters[] = "(" . implode(" AND ", $emptyconstraint) . ")";

        $filter = "(" . implode(
            ") AND (",
            [
                '"ZonedShippingMethodID" = ' . $this->ID,
                '"ZoneID" IN(' . implode(",", $ids) . ")", //zone restriction
                implode(" OR ", $constraintfilters) //metrics restriction
            ]
        ) . ")";

        if ($sr = ZonedShippingRate::get()->where($filter)->sort('Rate')->first()) {
            $rate = $sr->Rate;
        }

        return $this->CalculatedRate = $rate;
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

        $fields->removeByName("Rates");
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
