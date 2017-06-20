<?php

/**
 * @package silvershop-shipping
 */
class DistanceShippingMethod extends ShippingMethod
{
    private static $defaults = array(
        'Name' => 'Distance Shipping',
        'Description' => 'Per product shipping'
    );

    private static $has_many = array(
        "DistanceFares" => "DistanceShippingFare"
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->fieldByName('Root')->removeByName("DistanceFares");
        if ($this->isInDB()) {
            $fields->addFieldToTab("Root.Main", $gridfield = GridField::create(
                "DistanceFares", "Fares",
                $this->DistanceFares(), $config = new GridFieldConfig_RecordEditor()
            ));
            $config->removeComponentsByType("GridFieldDataColumns");
            $config->removeComponentsByType("GridFieldEditButton");
            $config->removeComponentsByType("GridFieldDeleteAction");
            $config->removeComponentsByType("GridFieldAddNewButton");
            $config->addComponent($cols = new GridFieldEditableColumns());
            $config->addComponent(new GridFieldDeleteAction());
            $config->addComponent($addnew = new GridFieldAddNewInlineButton());
            $addnew->setTitle($addnew->getTitle()." Fare");
            if ($greatest = $this->greatestCostDistance()) {
                $fields->insertAfter(
                    LiteralField::create("costnote",
                        "<p class=\"message\">Distances beyond the greatest specified distance will be cost ".
                            $this->greatestCostDistance()->dbObject("Cost")->Nice().
                        " (the most expensive fare)</p>"
                    ), "DistanceFares"
                );
            }
        }

        return $fields;
    }

    public function calculateRate(ShippingPackage $package, Address $address)
    {
        $warehouse = Warehouse::closest_to($address);
        $distance = $warehouse->Address()->distanceTo($address);

        return $this->getDistanceFare($distance);
    }

    public function getDistanceFare($distance)
    {
        $cost = 0;
        $fare = $this->DistanceFares()
            ->filter("Distance:GreaterThan", 0)
            ->filter("Distance:GreaterThan", $distance)
            ->sort("Distance", "ASC")
            ->first();
        if (!$fare) {
            $fare = $this->greatestCostDistance();
        }
        if ($fare->exists()) {
            $cost = $fare->Cost;
        }

        return $cost;
    }

    public function greatestCostDistance()
    {
        return $this->DistanceFares()
                ->sort("Cost", "DESC")
                ->first();
    }
}

class DistanceShippingFare extends DataObject
{
    private static $db = array(
        'Distance' => 'Float',
        'Cost' => 'Currency'
    );

    private static $has_one = array(
        'ShippingMethod' => 'DistanceShippingMethod'
    );

    private static $summary_fields = array(
        'MinDistance',
        'Distance',
        'Cost'
    );

    private static $field_labels = array(
        'MinDistance' => 'Min Distance (km)',
        'Distance' => 'Max Distance (km)',
        'Cost' => 'Cost'
    );

    private static $singular_name = "Fare";

    private static $default_sort = "\"Distance\" ASC";

    public function getMinDistance()
    {
        $dist = 0;
        if (
            $dfare = self::get()
            ->filter("Distance:LessThan", $this->Distance)
            ->filter("ShippingMethodID", $this->ShippingMethodID)
            ->sort("Distance", "DESC")
            ->first()
        ) {
            $dist = $dfare->Distance;
        }

        return $dist;
    }
}
