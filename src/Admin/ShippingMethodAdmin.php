<?php

namespace SilverShop\Shipping\Admin;

use SilverStripe\Admin\ModelAdmin;
use Symbiote\GridFieldExtensions\GridFieldAddNewMultiClass;
use SilverStripe\Core\ClassInfo;
use SilverShop\Shipping\Model\Warehouse;
use SilverShop\Shipping\Model\ShippingMethod;

class ShippingMethodAdmin extends ModelAdmin
{
    private static $url_segment = "shipping";

    private static $menu_title = "Shipping";

    private static $menu_priority = 3;

    private static $menu_icon = 'silvershop/shipping:images/shipping.png';

    private static $managed_models = [
        ShippingMethod::class,
        Warehouse::class
    ];

    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm($id, $fields);
        if ($this->modelClass === "ShippingMethod") {
            $gridfield = $form->Fields()->fieldByName($this->sanitiseClassName($this->modelClass));
            $config = $gridfield->getConfig();
            $config->removeComponentsByType("GridFieldAddNewButton");
            $config->removeComponentsByType("GridFieldPrintButton");
            $config->removeComponentsByType("GridFieldExportButton");
            $config->addComponent($multiclass = new GridFieldAddNewMultiClass());
            $classes = ClassInfo::subclassesFor($this->modelClass);
            unset($classes[$this->modelClass]);
            $multiclass->setClasses($classes);
        }
        return $form;
    }
}
