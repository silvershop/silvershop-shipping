<?php

namespace SilverShop\Shipping\Model;

use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use Symbiote\GridFieldExtensions\GridFieldAddNewInlineButton;
use SilverStripe\Forms\LiteralField;
use SilverShop\Shipping\ShippingPackage;
use SilverShop\Shipping\Model\Warehouse;
use SilverShop\Model\Address;
use SilverStripe\ORM\DataObject;
use SilverShop\Shipping\Model\DistanceShippingFare;

class DistanceShippingMethod extends ShippingMethod
{
    private static $defaults = [
        'Name' => 'Distance Shipping',
        'Description' => 'Per product shipping'
    ];

    private static $has_many = [
        "DistanceFares" => DistanceShippingFare::class
    ];

    private static $table_name = 'SilverShop_DistanceShippingMethod';

    private static $singular_name = 'Distance shipping method';

    private static $plural_name = 'Distance shipping methods';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->fieldByName('Root')->removeByName("DistanceFares");
        if ($this->isInDB()) {
            $fields->addFieldToTab("Root.Main", $gridfield = GridField::create(
                "DistanceFares",
                "Fares",
                $this->DistanceFares(),
                $config = new GridFieldConfig_RecordEditor()
            ));
            $config->removeComponentsByType(GridFieldDataColumns::class);
            $config->removeComponentsByType(GridFieldEditButton::class);
            $config->removeComponentsByType(GridFieldDeleteAction::class);
            $config->removeComponentsByType(GridFieldAddNewButton::class);
            $config->addComponent($cols = new GridFieldEditableColumns());
            $config->addComponent(new GridFieldDeleteAction());
            $config->addComponent($addnew = new GridFieldAddNewInlineButton());
            $addnew->setTitle($addnew->getTitle() . " Fare");
            if ($this->greatestCostDistance()) {
                $fields->insertAfter(
                    "DistanceFares",
                    LiteralField::create(
                        "costnote",
                        "<p class=\"message\">Distances beyond the greatest specified distance will be cost " .
                            $this->greatestCostDistance()->dbObject("Cost")->Nice() .
                        " (the most expensive fare)</p>"
                    )
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

    /**
     * @return bool
     */
    public function requiresAddress()
    {
        return true;
    }
}
