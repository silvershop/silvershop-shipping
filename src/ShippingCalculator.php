<?php

namespace SilverShop\Shipping;

use SilverShop\Shipping\Model\ShippingMethod;
use SilverShop\Model\Order;

/**
 * Helper class for encapsulating shipping calculation logic.
 */
class ShippingCalculator
{
    protected $method;
    protected $order;

    public function __construct(ShippingMethod $method, Order $order)
    {
        $this->method = $method;
        $this->order = $order;
    }

    public function calculate($address = null)
    {
        return $this->method->calculateRate(
            $this->order->createShippingPackage(),
            $address ? $address : $this->order->getShippingAddress()
        );
    }
}
