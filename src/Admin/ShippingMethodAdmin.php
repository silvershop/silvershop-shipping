<?php

namespace SilverShop\Shipping\Admin;

use SilverStripe\Admin\ModelAdmin;
use Symbiote\GridFieldExtensions\GridFieldAddNewMultiClass;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Core\Config\Config;
use SilverShop\Shipping\Model\Warehouse;
use SilverShop\Shipping\Model\ShippingMethod;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldPrintButton;
use SilverStripe\Forms\GridField\GridFieldExportButton;
use SilverStripe\ORM\ArrayLib;

class ShippingMethodAdmin extends ModelAdmin
{
    private static string $url_segment = "shipping";

    private static string $menu_title = "Shipping";

    private static int $menu_priority = 3;

    private static string $menu_icon = 'silvershop/shipping:images/shipping.png';

    private static array $managed_models = [
        ShippingMethod::class,
        Warehouse::class
    ];

    public function getEditForm($id = null, $fields = null): Form
    {
        $form = parent::getEditForm($id, $fields);

        if ($this->modelClass === ShippingMethod::class) {
            $gridfield = $form->Fields()->fieldByName($this->sanitiseClassName($this->modelClass));
            $config = $gridfield->getConfig();
            $config->removeComponentsByType(GridFieldAddNewButton::class);
            $config->removeComponentsByType(GridFieldPrintButton::class);
            $config->removeComponentsByType(GridFieldExportButton::class);
            $addNew = Injector::inst()->create(GridFieldAddNewMultiClass::class, 'toolbar-header-left');
            $classes = ClassInfo::subclassesFor($this->modelClass);
            $classes = ArrayLib::valuekey($classes);
            unset($classes[$this->modelClass]);

            foreach (Config::inst()->get(ShippingMethod::class, 'disable_methods') as $disable) {
                if (isset($classes[$disable])) {
                    unset($classes[$disable]);
                }
            }

            $config->addComponent($addNew->setClasses($classes));
        }

        return $form;
    }
}
