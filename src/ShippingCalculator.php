<?php

namespace SilverShop\Shipping;

use SilverShop\Shipping\Model\ShippingMethod;
use SilverShop\Model\Order;

/**
 * Helper class for encapsulating shipping calculation logic.
 */
class ShippingCalculator
{
    protected ShippingMethod $method;
    protected Order $order;

    public function __construct(ShippingMethod $method, Order $order)
    {
        $this->method = $method;
        $this->order = $order;
    }

    public function calculate($address = null, $value = null): null
    {
        return $this->method->calculateRate(
            $this->order->createShippingPackage($value),
            $address ? $address : $this->order->getShippingAddress()
        );
    }
}
