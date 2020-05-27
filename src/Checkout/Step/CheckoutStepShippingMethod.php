<?php

namespace SilverShop\Shipping\Checkout\Step;

use SilverShop\Checkout\Step\CheckoutStep;
use SilverShop\Cart\ShoppingCart;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\Form;
use SilverShop\Shipping\Model\ShippingMethod;

/**
 * Gives methods to ship by, based on previously given address and order items.
 */
class CheckoutStepShippingMethod extends CheckoutStep
{
    private static $allowed_actions = [
        'shippingmethod',
        'ShippingMethodForm'
    ];

    public function shippingmethod()
    {
        $form = $this->ShippingMethodForm();
        $cart = ShoppingCart::singleton()->current();

        if ($cart->ShippingMethodID) {
            $form->loadDataFrom($cart);
        }

        return [
            'OrderForm' => $form
        ];
    }

    /**
     * @return Form
     */
    public function ShippingMethodForm()
    {
        $order = $this->owner->Cart();

        if (!$order) {
            return null;
        }

        $estimates = $order->getShippingEstimates();
        $fields = new FieldList();

        if ($estimates->exists()) {
            // if there is only one option then automatically select the option
            if ($estimates->count() === 1) {
                $order->setShippingMethod($estimates->First());
            }

            $fields->push(
                OptionsetField::create(
                    "ShippingMethodID",
                    _t('CheckoutStep_ShippingMethod.ShippingOptions', 'Shipping Options'),
                    $estimates->map(),
                    $estimates->First()->ID
                )
            );
        } else {
            $fields->push(
                LiteralField::create(
                    "NoShippingMethods",
                    _t('CheckoutStep_ShippingMethod.NoShippingMethods',
                        '<p class=\"message warning\">There are no shipping methods available</p>'
                    )
                )
            );
        }

        $actions = new FieldList(
            new FormAction("setShippingMethod",  _t('SilverShop\Checkout\Step\CheckoutStep.Continue', 'Continue'))
        );

        $form = new Form($this->owner, "ShippingMethodForm", $fields, $actions);
        $this->owner->extend('updateShippingMethodForm', $form);

        return $form;
    }

    public function setShippingMethod($data, $form)
    {
        $order = $this->owner->Cart();
        $option = null;

        if (isset($data['ShippingMethodID'])) {
            $option = ShippingMethod::get()->byID($data['ShippingMethodID']);

            if ($option) {
                $order->setShippingMethod($option);
            }
        }

        // perform write to store changes
        $order->calculate();
        $order->write();

        return $this->owner->redirect($this->NextStepLink());
    }
}
