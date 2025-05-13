<?php

namespace SilverShop\Shipping\Tests;

use SilverStripe\Dev\SapphireTest;
use SilverShop\Shipping\ShippingPackage;

class ShippingPackageTest extends SapphireTest
{

    public function testPackages(): void
    {

        $p = ShippingPackage::create(25, ['depth' => 4, 'height' => 23,'width' => 12]);
        $this->assertEquals(23, $p->height());
        $this->assertEquals(12, $p->width());
        $this->assertEquals(4, $p->depth());
        $this->assertEquals(25, $p->weight());

        $p = ShippingPackage::create(25.3, ['h' => 23.7, 'd' => 4, 'w' => 12.344,]);
        $this->assertEqualsWithDelta(23.7, $p->height(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(12.344, $p->width(), PHP_FLOAT_EPSILON);
        $this->assertEquals(4, $p->depth());
        $this->assertEqualsWithDelta(25.3, $p->weight(), PHP_FLOAT_EPSILON);

        $p = ShippingPackage::create(1, [3,4,5]);
        $this->assertEquals(3, $p->height());
        $this->assertEquals(4, $p->width());
        $this->assertEquals(5, $p->depth());
        $this->assertEquals(60, $p->volume());

        $p = ShippingPackage::create(13, [1,1,2.5], ['shape' => 'cylinder']);
        $this->assertEquals(1, $p->height());
        $this->assertEqualsWithDelta(2.5, $p->depth(), PHP_FLOAT_EPSILON);
        $this->assertEquals(13, $p->weight());
        $this->assertEqualsWithDelta(2.5, $p->volume(), PHP_FLOAT_EPSILON);
    }
}
