<?php

namespace SilverShop\Shipping\Tests;

use SilverStripe\Dev\SapphireTest;
use SilverShop\Model\Order;
use SilverShop\Model\Address;
use SilverShop\Shipping\ShippingPackage;
use SilverShop\Shipping\ShippingEstimator;
use SilverShop\ORM\FieldType\ShopCurrency;

class ShippingEstimatorTest extends SapphireTest
{
    protected static $fixture_file = 'TableShippingMethod.yml';

    public function testGetEstimates(): void
    {
        $order = Order::create();
        $address = Address::create();
        ShippingPackage::create(2);
        $estimator = ShippingEstimator::create($order, $address);

        $options = $estimator->getShippingMethods();
        $this->assertNotNull($options, "options found");

        $estimates = $estimator->getEstimates();
        $this->assertTrue($estimates->exists(), "estimates found");
    }

    public function testEstimatesExposeCalculatedRate(): void
    {
        $order = Order::create();
        $estimator = ShippingEstimator::create($order, Address::create());

        $estimates = $estimator->getEstimates();
        $this->assertTrue($estimates->exists(), "estimates found");

        $previous = null;
        foreach ($estimates as $estimate) {
            $rate = $estimate->getRate();

            // Core regression: the estimator assigns the rate via a protected property; before the fix
            // that write landed on a dynamic field, so getRate() stayed null and the checkout rendered
            // every option as 0.00.
            $this->assertNotNull($rate, "getRate() should expose the calculated rate");
            $this->assertGreaterThan(0, $rate, "the calculated rate should be the real, non-zero rate");

            $expected = number_format(
                (float)$rate,
                2,
                ShopCurrency::config()->decimal_delimiter,
                ShopCurrency::config()->thousand_delimiter
            );
            $this->assertStringContainsString($expected, $estimate->getTitle(), "the title should show the rate");

            if ($previous !== null) {
                $this->assertGreaterThanOrEqual($previous, $rate, "estimates should be sorted cheapest-first");
            }
            $previous = $rate;
        }
    }
}
