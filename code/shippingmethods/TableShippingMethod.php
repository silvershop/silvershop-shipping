<?php

/**
 * Work out shipping rate from a pre-defined table of regions - to - weights and dimensions.
 *
 * @package silvershop-shipping
 */
class TableShippingMethod extends ShippingMethod
{
    private static $defaults = array(
        'Name'        => 'Table Shipping',
        'Description' => 'Works out shipping from a pre-defined table'
    );

    private static $has_many = array(
        "Rates" => "TableShippingRate"
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fieldList = array(
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
        );

        $fields->fieldByName('Root')->removeByName("Rates");
        if ($this->isInDB()) {
            $tablefield = new GridField("Rates", "TableShippingRate", $this->Rates(), new GridFieldConfig_RecordEditor());
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
        $packageconstraints = array(
            "Weight"   => 'weight',
            "Volume"   => 'volume',
            "Value"    => 'value',
            "Quantity" => 'quantity'
        );
        $constraintfilters = array();
        $emptyconstraint = array();
        foreach ($packageconstraints as $db => $pakval) {
            $mincol = "\"TableShippingRate\".\"{$db}Min\"";
            $maxcol = "\"TableShippingRate\".\"{$db}Max\"";
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

        $filter = "(" . implode(") AND (", array(
                "\"ShippingMethodID\" = " . $this->ID,
                RegionRestriction::address_filter($address), //address restriction
                implode(" OR ", $constraintfilters) //metrics restriction
            )) . ")";
        if ($tr = DataObject::get_one("TableShippingRate", $filter, true, "LENGTH(\"RegionRestriction\".\"PostalCode\") DESC, Rate ASC")) {
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

/**
 * Adds extra metric ranges to restrict with, rather than just region.
 */
class TableShippingRate extends RegionRestriction
{
    private static $db = array(
        //constraint values
        "WeightMin"   => "Decimal",
        "WeightMax"   => "Decimal",
        "VolumeMin"   => "Decimal",
        "VolumeMax"   => "Decimal",
        "ValueMin"    => "Currency",
        "ValueMax"    => "Currency",
        "QuantityMin" => "Int",
        "QuantityMax" => "Int",

        "Rate" => "Currency"
    );

    private static $has_one = array(
        "ShippingMethod" => "TableShippingMethod"
    );

    private static $summary_fields = array(
        'Country',
        'State',
        'City',
        'PostalCode',
        'WeightMin',
        'WeightMax',
        'VolumeMin',
        'VolumeMax',
        'ValueMin',
        'ValueMax',
        'QuantityMin',
        'QuantityMax',
        'Rate'
    );

    private static $default_sort = "\"Country\" ASC, \"State\" ASC, \"City\" ASC, \"PostalCode\" ASC, \"Rate\" ASC";

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('ShippingMethodID');

        return $fields;
    }
}
