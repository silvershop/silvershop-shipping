<?php

namespace SilverShop\Shipping\Model;

use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverShop\Shipping\ShippingPackage;
use SilverShop\Model\Address;
use SilverShop\Shipping\Model\RegionRestriction;
use SilverStripe\ORM\DataObject;


/**
 * Work out shipping rate from a pre-defined table of regions - to - weights
 * and dimensions.
 */
class TableShippingMethod extends ShippingMethod
{
    private static $defaults = [
        'Name'        => 'Table Shipping',
        'Description' => 'Works out shipping from a pre-defined table'
    ];

    private static $has_many = [
        "Rates" => TableShippingRate::class
    ];

    private static $table_name = 'SilverShop_TableShippingMethod';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fieldList = [
            "Country"     => "Country",
            "State"       => "State",
            "City"        => "City",
            "PostalCode"  => "PostCode",
            "WeightMin"   => "WeightMin",
            "WeightMax"   => "WeightMax",
            "VolumeMin"   => "VolumeMin",
            "VolumeMax"   => "VolumeMax",
            "ValueMin"    => "ValueMin",
            "ValueMax"    => "ValueMax",
            "QuantityMin" => "QuantityMin",
            "QuantityMax" => "QuantityMax",
            "Rate"        => "Rate"
        ];

        $fields->fieldByName('Root')->removeByName("Rates");
        if ($this->isInDB()) {
            $tablefield = new GridField(
                "Rates",
                "TableShippingRate",
                $this->Rates(),
                new GridFieldConfig_RecordEditor()
            );

            $fields->addFieldToTab("Root.Main", $tablefield);
        }

        return $fields;
    }

    /**
     * Find the appropriate shipping rate from stored table range metrics
     */
    public function calculateRate(ShippingPackage $package, Address $address)
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
            //also include a special case where all constraints are empty
            $emptyconstraint[] = "($mincol = 0 AND $maxcol = 0)";
        }
        $constraintfilters[] = "(" . implode(" AND ", $emptyconstraint) . ")";

        $filter = "(" . implode(") AND (", [
                "\"ShippingMethodID\" = " . $this->ID,
                RegionRestriction::filteredByAddress($address), //address restriction
                implode(" OR ", $constraintfilters) //metrics restriction
            ]) . ")";

        if ($tr = TableShippingRate::get()->where($filter)->sort("LENGTH(\"RegionRestriction\".\"PostalCode\") DESC, Rate ASC")) {
            $rate = $tr->Rate;
        }
        $this->CalculatedRate = $rate;

        return $rate;
    }

    /**
     * If this shipping method has any @TableShippingRate with any @RegionRestriction where either Country, State, City or PostalCode are submitted, this method returns true
     * Else it returns false (@ShippingMethod::requiresAddress());
     *
     * @return bool
     */
    public function requiresAddress()
    {
        if ($this->Rates()->exists()) {
            $defaults = RegionRestriction::config()->get('defaults');
            $filter = [];
            foreach($defaults as $field => $val){
                $filter[$field . ':not'] = $val;
            }
            $rates = $this->Rates()->filterAny($filter);
            if($rates->exists()){
                return true;
            }
        }

        return parent::requiresAddress();
    }
}
