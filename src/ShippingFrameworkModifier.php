<?php

namespace SilverShop\Shipping;

use SilverShop\Model\Modifiers\OrderModifier;

class ShippingFrameworkModifier extends OrderModifier
{
    public function value($incoming)
    {
        $order = $this->Order();
        if ($order && $order->exists() && $shipping = $order->ShippingMethod()) {
            return $shipping->getCalculator($order)->calculate();
        }
        return 0;
    }

    public function TableTitle()
    {
        $title = $this->i18n_singular_name();

        if ($this->Order() && $this->Order()->ShippingMethod()->exists()) {
            $title .= " (".$this->Order()->ShippingMethod()->Name.")";
        }

        $this->extend('updateTableTitle', $title);

        return $title;
    }
}
