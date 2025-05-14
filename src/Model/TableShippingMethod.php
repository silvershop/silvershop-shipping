<?php

namespace SilverShop\Shipping\Model;

use SilverShop\Model\Address;
use SilverShop\Shipping\Model\RegionRestriction;
use SilverShop\Shipping\ShippingPackage;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\ORM\HasManyList;

/**
 * Work out shipping rate from a pre-defined table of regions - to - weights
 * and dimensions.
 *
 * @method HasManyList<TableShippingRate> Rates()
 */
class TableShippingMethod extends ShippingMethod
{
    private static array $defaults = [
        'Name'        => 'Table Shipping',
        'Description' => 'Works out shipping from a pre-defined table'
    ];

    private static array $has_many = [
        "Rates" => TableShippingRate::class
    ];

    private static string $table_name = 'SilverShop_TableShippingMethod';

    private static string $singular_name = 'Table shipping method';

    private static string $plural_name = 'Table shipping methods';

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();
        $fields->fieldByName('Root')->removeByName("Rates");
        if ($this->isInDB()) {
            $tablefield = GridField::create(
                "Rates",
                "TableShippingRate",
                $this->Rates(),
                GridFieldConfig_RecordEditor::create()
            );
            $fields->addFieldToTab("Root.Main", $tablefield);
        }
        return $fields;
    }

    /**
     * Find the appropriate shipping rate from stored table range metrics.
     */
    public function calculateRate(ShippingPackage $package, Address $address): float|int|null
    {
        $rate = null;
        $packageconstraints = [
            "Weight"   => 'weight',
            "Volume"   => 'volume',
            "Value"    => 'value',
            "Quantity" => 'quantity'
        ];

        $constraintfilters = [];
        $emptyconstraint = [];

        foreach ($packageconstraints as $db => $pakval) {
            $mincol = "\"SilverShop_TableShippingRate\".\"{$db}Min\"";
            $maxcol = "\"SilverShop_TableShippingRate\".\"{$db}Max\"";
            //constrain to rates with valid constraints
            $constraintfilters[] =
                "(" .
                "$mincol >= 0" .
                " AND $mincol <= " . $package->{$pakval}() .
                " AND $maxcol > 0" . //ignore constraints with maxvalue = 0
                " AND $maxcol >= " . $package->{$pakval}() .
                " AND $mincol < $maxcol" . //sanity check
                ")";

            // also include a special case where all constraints are empty
            $emptyconstraint[] = "($mincol = 0 AND $maxcol = 0)";
        }

        $constraintfilters[] = "(" . implode(" AND ", $emptyconstraint) . ")";

        $filter = sprintf(
            "(%s)",
            implode(
                ") AND (",
                [
                "\"ShippingMethodID\" = " . $this->ID,
                implode(" OR ", $constraintfilters)
                ]
            )
        );

        $tr = TableShippingRate::get()
            ->where($filter);

        if (($addressFilters = RegionRestriction::getAddressFilters($address)) !== []) {
            $tr = $tr->filter($addressFilters);
        }

        $tr = $tr->sort(
            "LENGTH(\"SilverShop_RegionRestriction\".\"PostalCode\") DESC, \"SilverShop_TableShippingRate\".\"Rate\" ASC"
        )->first();

        if ($tr) {
            $rate = $tr->Rate;
        }

        return $this->CalculatedRate = $rate;
    }

    /**
     * If this shipping method has any @TableShippingRate with any @RegionRestriction
     * where either Country, State, City or PostalCode are submitted, this method returns true
     * Else it returns false (@ShippingMethod::requiresAddress());
     */
    public function requiresAddress(): bool
    {
        if ($this->Rates()->exists()) {
            $defaults = RegionRestriction::config()->get('defaults');
            $filter = [];
            foreach ($defaults as $field => $val) {
                $filter[$field . ':not'] = $val;
            }
            $rates = $this->Rates()->filterAny($filter);
            if ($rates->exists()) {
                return true;
            }
        }

        return parent::requiresAddress();
    }
}
