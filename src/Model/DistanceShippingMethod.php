<?php

namespace SilverShop\Shipping\Model;

use SilverShop\Model\Address;
use SilverShop\Shipping\Model\DistanceShippingFare;
use SilverShop\Shipping\Model\Warehouse;
use SilverShop\Shipping\ShippingPackage;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\HasManyList;
use Symbiote\GridFieldExtensions\GridFieldAddNewInlineButton;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;

/**
 * @method HasManyList<DistanceShippingFare> DistanceFares()
 */
class DistanceShippingMethod extends ShippingMethod
{
    private static array $defaults = [
        'Name' => 'Distance Shipping',
        'Description' => 'Per product shipping'
    ];

    private static array $has_many = [
        "DistanceFares" => DistanceShippingFare::class
    ];

    private static string $table_name = 'SilverShop_DistanceShippingMethod';

    private static string $singular_name = 'Distance shipping method';

    private static string $plural_name = 'Distance shipping methods';

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();
        $fields->fieldByName('Root')->removeByName("DistanceFares");
        if ($this->isInDB()) {
            $fields->addFieldToTab(
                "Root.Main",
                $gridfield = GridField::create(
                    "DistanceFares",
                    "Fares",
                    $this->DistanceFares(),
                    $config = GridFieldConfig_RecordEditor::create()
                )
            );
            $config->removeComponentsByType(GridFieldDataColumns::class);
            $config->removeComponentsByType(GridFieldEditButton::class);
            $config->removeComponentsByType(GridFieldDeleteAction::class);
            $config->removeComponentsByType(GridFieldAddNewButton::class);
            $config->addComponent($cols = GridFieldEditableColumns::create());
            $config->addComponent(GridFieldDeleteAction::create());
            $config->addComponent($addnew = GridFieldAddNewInlineButton::create());
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

    public function calculateRate(ShippingPackage $package, Address $address): null
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

    public function requiresAddress(): bool
    {
        return true;
    }
}
