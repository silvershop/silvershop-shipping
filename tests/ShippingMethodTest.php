<?php

namespace SilverShop\Shipping\Tests;

use SilverStripe\Dev\SapphireTest;
use SilverShop\Shipping\Model\ShippingMethod;

class ShippingMethodTest extends SapphireTest
{
    /**
     * getTitle() is rendered as HTML in the checkout (CheckoutStepShippingMethod passes it to an
     * OptionsetField as DBHTMLText), so the free-text Name/Description fields must be HTML-escaped
     * to avoid an injection surface.
     */
    public function testGetTitleEscapesFreeTextFields(): void
    {
        $method = ShippingMethod::create();
        $method->Name = 'Fast & <b>Cheap</b>';
        $method->Description = '<script>alert(1)</script>';

        $title = $method->getTitle();

        $this->assertStringContainsString('Fast &amp; &lt;b&gt;Cheap&lt;/b&gt;', $title);
        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $title);
        $this->assertStringNotContainsString('<script>', $title);
        $this->assertStringNotContainsString('<b>Cheap</b>', $title);
    }

    /**
     * Plain-text titles are unchanged (aside from being combined with the rate).
     */
    public function testGetTitleLeavesPlainTextIntact(): void
    {
        $method = ShippingMethod::create();
        $method->Name = 'Standard';
        $method->Description = 'Delivered in 3 days';

        $title = $method->getTitle();

        $this->assertStringContainsString('Standard', $title);
        $this->assertStringContainsString('Delivered in 3 days', $title);
    }
}
