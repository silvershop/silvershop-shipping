<?php

namespace SilverShop\Shipping;

use SilverShop\Model\Modifiers\OrderModifier;

class ShippingFrameworkModifier extends OrderModifier
{
    private static string $singular_name = 'Shipping';

    private static string $table_name = 'SilverShop_ShippingFrameworkModifier';

    public function value($incoming): int|float
    {
        $order = $this->Order();
        if ($order && $order->exists() && ($shipping = $order->ShippingMethod()) && $shipping->exists()) {
            $value = $shipping->getCalculator($order)->calculate(null, $incoming);
            $order->ShippingTotal = $value;
            $order->write();
            return $value;
        }
        return 0;
    }

    public function TableTitle(): string
    {
        $title = $this->i18n_singular_name();

        if ($this->Order() && $this->Order()->ShippingMethod()->exists()) {
            $title .= " (" . $this->Order()->ShippingMethod()->Name . ")";
        }

        $this->extend('updateTableTitle', $title);

        return $title;
    }
}
