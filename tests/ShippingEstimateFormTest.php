<?php

namespace SilverShop\Shipping\Tests;

use SilverShop\Cart\ShoppingCart;
use SilverShop\Model\Order;
use SilverShop\Page\Product;
use SilverShop\Page\CartPage;
use SilverShop\Tests\ShopTest;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\SiteConfig\SiteConfig;

class ShippingEstimateFormTest extends FunctionalTest
{
    protected static $fixture_file = [
        "TableShippingMethod.yml",
        "Shop.yml"
    ];

    protected static $disable_theme = true;
    protected static $use_draft_site = true;
    protected $socks;
    protected $cartpage;

    protected function setup(): void
    {
        parent::setUp();
        ShopTest::setConfiguration();
        $this->logInWithPermission('ADMIN');

        // add product to the cart
        $this->socks = $this->objFromFixture(Product::class, 'socks');
        $this->socks->publishRecursive();

        $this->cartpage = $this->objFromFixture(CartPage::class, "cart");
        $this->cartpage->publishRecursive();

        ShoppingCart::singleton()->setCurrent($this->objFromFixture(Order::class, "cart")); //set the current cart
    }

    public function testGetEstimates(): void
    {
        $this->useTestTheme(
            dirname(__FILE__),
            'testtheme',
            function (): void {
                $page = $this->get('/cart');

                //good data for Shipping Estimate Form
                $data = [
                    'Country' => 'NZ',
                    'State' => 'Auckland',
                    'City' => 'Auckland',
                    'PostalCode' => 1010
                ];
                $page1 = $this->post('/cart/ShippingEstimateForm', $data);
                $this->assertEquals(200, $page1->getStatusCode(), "a page should load");
                $this->assertStringContainsString(
                    "Quantity-based shipping",
                    $page1->getBody(),
                    "ShippingEstimates presented in a table"
                );

                //un-escaped data for Shipping Estimate Form
                $data = [
                    'Country' => 'NZ',
                    'State' => "Hawke's Bay",
                    'City' => 'SELECT * FROM \" \' WHERE AND EVIL',
                    'PostalCode' => 1234
                ];
                $page2 = $this->post('/cart/ShippingEstimateForm', $data);
                $this->assertEquals(200, $page2->getStatusCode(), "a page should load");
                $this->assertStringContainsString(
                    "Quantity-based shipping",
                    $page2->getBody(),
                    "ShippingEstimates can be successfully presented with un-escaped data in the form"
                );
            }
        );
    }

    public function testShippingEstimateWithReadonlyFieldForCountry(): void
    {
        $siteconfig = SiteConfig::get()->first();
        $this->assertInstanceOf(SiteConfig::class, $siteconfig);
        $siteconfig->setField('AllowedCountries', '["NZ"]'); // setup a single-country site
        $siteconfig->write();

        $this->useTestTheme(
            dirname(__FILE__),
            'testtheme',
            function (): void {
                // Open cart page where Country field is readonly
                $page = $this->get('/cart');

                $this->assertStringContainsString(
                    "Country_readonly",
                    $page->getBody(),
                    "The Country field is readonly"
                );
                $this->assertStringNotContainsString(
                    '<option value="NZ">New Zealand</option>',
                    $page->getBody(),
                    "Dropdown field is not shown"
                );

                // The Shipping Estimate Form can post with a Country readonly field
                $data = [
                    'State' => 'Waikato',
                    'City' => 'Hamilton',
                    'PostalCode' => 3210
                ];
                $page3 = $this->post('/cart/ShippingEstimateForm', $data);
                $this->assertEquals(200, $page3->getStatusCode(), "a page should load");
                $this->assertStringContainsString(
                    "Quantity-based shipping",
                    $page3->getBody(),
                    "ShippingEstimates can be successfully presented with a Country readonly field"
                );
            }
        );
    }
}
