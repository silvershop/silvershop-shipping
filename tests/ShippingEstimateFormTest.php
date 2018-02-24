<?php

namespace SilverShop\Shipping\Tests;

use SilverStripe\Dev\FunctionalTest;
use SilverShop\Tests\ShopTest;
use SilverShop\Cart\ShoppingCart;
use SilverStripe\ORM\DataObject;
use SilverStripe\Core\Config\Config;
use SilverShop\Model\Order;
use SilverShop\Page\Product;
use SilverShop\Page\CartPage;
use SilverStripe\SiteConfig\SiteConfig;

class ShippingEstimateFormTest extends FunctionalTest
{
    protected static $fixture_file = [
        "TableShippingMethod.yml",
        "Shop.yml"
    ];

    protected static $use_draft_site = true;

    protected function setUp() {
        $this->useTheme('testtheme');

        parent::setUp();

        ShopTest::setConfiguration();

        // add product to the cart
        $this->socks = $this->objFromFixture(Product::class, 'socks');
        $this->socks->publish('Stage','Live');

        $this->cartpage = $this->objFromFixture(CartPage::class, "cart");
        $this->cartpage->publish('Stage','Live');
        ShoppingCart::singleton()->setCurrent($this->objFromFixture(Order::class, "cart")); //set the current cart

        // Open cart page
        $page = $this->get('/cart');
    }

    function testGetEstimates() {

        //good data for Shipping Estimate Form
        $data = [
            'Country' => 'NZ',
            'State' => 'Auckland',
            'City' => 'Auckland',
            'PostalCode' => 1010
        ];
        $page1 = $this->post('/cart/ShippingEstimateForm', $data);
        $this->assertEquals(200, $page1->getStatusCode(), "a page should load");
        $this->assertContains("Quantity-based shipping", $page1->getBody(), "ShippingEstimates presented in a table");


        //un-escaped data for Shipping Estimate Form
        $data = [
            'Country' => 'NZ',
            'State' => 'Hawke\'s Bay',
            'City' => 'SELECT * FROM \" \' WHERE AND EVIL',
            'PostalCode' => 1234
        ];
        $page2 = $this->post('/cart/ShippingEstimateForm', $data);
        $this->assertEquals(200, $page2->getStatusCode(), "a page should load");
        $this->assertContains("Quantity-based shipping", $page2->getBody(), "ShippingEstimates can be successfully presented with un-escaped data in the form");

    }

    function testShippingEstimateWithReadonlyFieldForCountry() {
        // setup a single-country site
        $siteconfig = SiteConfig::get()->first();
        $siteconfig->AllowedCountries = "NZ";
        $siteconfig->write();

        // Open cart page where Country field is readonly
        $page = $this->get('/cart');
        $this->assertContains("Country_readonly", $page->getBody(), "The Country field is readonly");
        $this->assertNotContains("<option value=\"NZ\">New Zealand</option>", $page->getBody(), "Dropdown field is not shown");

        // The Shipping Estimate Form can post with a Country readonly field
        $data = [
            'State' => 'Waikato',
            'City' => 'Hamilton',
            'PostalCode' => 3210
        ];
        $page3 = $this->post('/cart/ShippingEstimateForm', $data);
        $this->assertEquals(200, $page3->getStatusCode(), "a page should load");
        $this->assertContains("Quantity-based shipping", $page3->getBody(), "ShippingEstimates can be successfully presented with a Country readonly field");


    }


    /**
    * Test against a SS Shop Shipping theme
    *
    * Template CartPage.ss contains <% include ShippingEstimator %>
    * Function adapted from SSViewerTest.php in SSv3.1
    * @param $theme string - theme name
    */
    protected function useTheme($theme) {
        global $project;

        // @todo v4
    }

}
