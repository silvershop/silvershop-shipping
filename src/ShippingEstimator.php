<?php

namespace SilverShop\Shipping;

use SilverStripe\Core\Injector\Injectable;
use SilverShop\Shipping\Model\ShippingMethod;
use SilverShop\Model\Order;
use SilverShop\Model\Address;
use SilverStripe\ORM\ArrayList;

/**
 * Helper class for calculating rates for available shipping options.
 * Provides a little caching, so estimates aren't calculated more than once.
 */
class ShippingEstimator
{
    use Injectable;

    protected $order;

    protected $address;

    protected $estimates = null;

    protected $calculated = false;

    public function __construct(Order $order, Address $address = null)
    {
        $this->order = $order;
        $this->address = $address ? $address : $order->getShippingAddress();
    }

    public function getEstimates()
    {
        if ($this->calculated) {
            return $this->estimates;
        }

        $total = $this->order->TotalWithoutShipping();

        $output = new ArrayList();
        if ($options = $this->getShippingMethods()) {
            foreach ($options as $option) {
                $rate = $option->getCalculator($this->order)->calculate($this->address, $total);
                if ($rate !== null) {
                    $option->CalculatedRate = $rate;
                    $output->push($option);
                }
            }
        }

        $output->sort("CalculatedRate", "ASC"); //sort by rate, lowest to highest
        // cache estimates
        $this->estimates = $output;
        $this->calculated = true;

        return $output;
    }

    /**
     * Get options that apply to package and location,
     */
    public function getShippingMethods()
    {
        return ShippingMethod::get()->filter("Enabled", 1);
    }
}
