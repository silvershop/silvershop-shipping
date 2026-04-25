<?php

namespace SilverShop\Shipping\Forms;

use SilverStripe\Control\RequestHandler;
use SilverStripe\Forms\Form;
use SilverShop\Model\Address;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\Validation\RequiredFieldsValidator;
use SilverStripe\SiteConfig\SiteConfig;
use SilverShop\Cart\ShoppingCart;
use SilverShop\Shipping\ShippingEstimator;
use SilverStripe\Core\Convert;

class ShippingEstimateForm extends Form
{
    public function __construct(RequestHandler $controller, $name = "ShippingEstimateForm")
    {
        $address = Address::create();  // get address to access it's getCountryField method
        $fields = FieldList::create(
            $address->getCountryField(),
            TextField::create('State', _t('Address.db_State', 'State')),
            TextField::create('City', _t('Address.db_City', 'City')),
            TextField::create('PostalCode', _t('Address.db_PostalCode', 'Postal Code'))
        );
        $actions =  FieldList::create(
            FormAction::create(
                "submit",
                _t('ShippingEstimateForm.FormActionTitle', 'Estimate')
            )
        );
        $validator = RequiredFieldsValidator::create(['Country']);
        parent::__construct($controller, $name, $fields, $actions, $validator);
        $this->extend('updateForm');
    }

    public function submit(array $data, $form)
    {
        if ($country = SiteConfig::current_site_config()->getSingleCountry()) {
            // Add Country if missing due to ReadonlyField in form
            $data['Country'] = $country;
        }

        if ($order = ShoppingCart::singleton()->current()) {
            $addressData = [
                'Country' => Convert::raw2sql((string) ($data['Country'] ?? '')),
                'State' => Convert::raw2sql((string) ($data['State'] ?? '')),
                'City' => Convert::raw2sql((string) ($data['City'] ?? '')),
                'PostalCode' => Convert::raw2sql((string) ($data['PostalCode'] ?? '')),
            ];
            $estimator = ShippingEstimator::create(
                $order,
                Address::create()->update($addressData)
            );

            $estimates = $estimator->getEstimates();

            if (!$estimates->exists()) {
                $form->sessionMessage(
                    _t(
                        'ShippingEstimateForm.FormActionWarningMessage',
                        'No estimates could be found for that location.'
                    ),
                    _t(
                        'ShippingEstimateForm.FormActionWarningCode',
                        "warning"
                    )
                );
            }

            $this->controller->getRequest()->getSession()->set(
                "ShippingEstimates",
                $estimates
            );

            if ($this->getRequest()->isAjax()) {
                //TODO: replace with an AJAXResponse class that can output to different formats
                return json_encode($estimates->toNestedArray());
            }
        }
        $this->controller->redirectBack();
        return null;
    }
}
