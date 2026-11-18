<?php

namespace SilverShop\Shipping\Checkout\Component;

use SilverShop\Checkout\Component\CheckoutComponent;
use SilverShop\Model\Order;
use SilverShop\Shipping\Model\ShippingMethod;
use SilverShop\ShopTools;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Core\Validation\ValidationResult;
use SilverStripe\Core\Validation\ValidationException;

class ShippingCheckoutComponent extends CheckoutComponent
{
    public function getFormFields(Order $order): FieldList
    {
        $fields = FieldList::create();
        $estimates = $order->getShippingEstimates();
        if ($estimates->exists()) {
            $fields->push(
                OptionsetField::create(
                    "ShippingMethodID",
                    _t('ShippingCheckoutComponent.ShippingOptions', 'Shipping Options'),
                    $estimates->map(),
                    $estimates->First()->ID
                )
            );
        }
        return $fields;
    }

    public function getRequiredFields(Order $order): array
    {
        return [];
    }

    public function validateData(Order $order, array $data): bool
    {
        // We fixed the wrong call of ValdiationResult::error() which doesn't exist by using addError()
        //in $result->addError()
        $result = ValidationResult::create();
        if (!isset($data['ShippingMethodID'])) {
            $result->addError(
                _t('ShippingCheckoutComponent.ShippingMethodNotProvidedMessage', "Shipping method not provided"),
                _t('ShippingCheckoutComponent.ShippingMethodErrorCode', "ShippingMethod")
            );
            throw ValidationException::create($result);
        }

        if (!$this->getAvailableShippingMethod($order, $data['ShippingMethodID'])) {
            $result->addError(
                _t(
                    'ShippingCheckoutComponent.ShippingMethodDoesNotExistMessage',
                    "Shipping Method does not exist"
                ),
                _t(
                    'ShippingCheckoutComponent.ShippingMethodErrorCode',
                    "ShippingMethod"
                )
            );
            throw ValidationException::create($result);
        }
        return true;
    }

    public function getData(Order $order): array
    {
        $estimates = $order->getShippingEstimates();
        $method = count($estimates) === 1 ? $estimates->First() : ShopTools::getSession()->get("Checkout.ShippingMethod");

        return [
            'ShippingMethod' => $method
        ];
    }

    public function setData(Order $order, array $data): Order
    {
        $option = null;
        if (isset($data['ShippingMethodID'])) {
            $option = $this->getAvailableShippingMethod($order, $data['ShippingMethodID']);
        }
        //assign option to order / modifier
        if ($option && $order->setShippingMethod($option)) {
            ShopTools::getSession()->set("Checkout.ShippingMethod", $option);
        }
        return $order;
    }

    /**
     * Only shipping methods that are currently offered for the order (enabled, and able to
     * calculate a rate for its address and contents) may be chosen. Looking the method up by ID
     * alone would let a customer submit a disabled method, or one that doesn't serve their
     * address, and check out with no shipping charge.
     */
    protected function getAvailableShippingMethod(Order $order, mixed $id): ?ShippingMethod
    {
        if (!is_numeric($id) || (int) $id <= 0) {
            return null;
        }

        $method = $order->getShippingEstimates()->find('ID', (int) $id);

        return $method instanceof ShippingMethod ? $method : null;
    }
}
