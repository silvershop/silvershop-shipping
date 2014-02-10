## SilverStripe Shop Shipping Framework

[![Latest Stable Version](https://poser.pugx.org/burnbright/silverstripe-shop-shipping/v/stable.png)](https://packagist.org/packages/burnbright/silverstripe-shop-shipping)
[![Latest Unstable Version](https://poser.pugx.org/burnbright/silverstripe-shop-shipping/v/unstable.png)](https://packagist.org/packages/burnbright/silverstripe-shop-shipping)
[![Build Status](https://secure.travis-ci.org/burnbright/silverstripe-shop-shipping.png)](http://travis-ci.org/burnbright/silverstripe-shop)
[![Code Coverage](https://scrutinizer-ci.com/g/burnbright/silverstripe-shop-shipping/badges/coverage.png?s=cae0140f6d9a99c35b20c23b8bbe88711d526246)](https://scrutinizer-ci.com/g/burnbright/silverstripe-shop-shipping/)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/burnbright/silverstripe-shop-shipping/badges/quality-score.png?s=802731e23565b5a7051b5622a56fccb7b764662a)](https://scrutinizer-ci.com/g/burnbright/silverstripe-shop-shipping/)
[![Total Downloads](https://poser.pugx.org/burnbright/silverstripe-shop-shipping/downloads.png)](https://packagist.org/packages/burnbright/silverstripe-shop-shipping)

Introduce shipping options to the SilverStripe shop.

## Requirements

 * [Shop module](https://github.com/burnbright/silverstripe-shop)

## Installation

```sh
composer reqire burnbright/silverstripe-shop-shipping
```

 * Put the shop_shippingframework folder into your SilverStripe root directory
 * Add the ShippingFrameWorkModifier to your modifiers config, eg:

```yaml
Order:
    modifiers
        - ShippingFrameworkModifier
        - FlatTaxModifier
```

 * You need to use the new SteppedCheckout system to allow customers to set their address
 before they choose their shipping method. To set up steps, including the shippingmethod step,
 add the following to your mysite/_config.php file:

```yaml
CheckoutPage:
    steps:
		'membership' : 'CheckoutStep_Membership'
        'contactdetails' : 'CheckoutStep_ContactDetails'
        'shippingaddress' : 'CheckoutStep_Address'
        'billingaddress' : 'CheckoutStep_Address'
        'shippingmethod' : 'CheckoutStep_ShippingMethod' #extra line for shipping method
        'paymentmethod' : 'CheckoutStep_PaymentMethod'
        'summary' : 'CheckoutStep_Summary'
```

 * To add the shipping estimation form to your CartPage template, add the following
 somewhere on your CartPage.ss template:

```
    <% include ShippingEstimator %>
```

If you need some example tableshipping data to populate your site for testing/development, 
you can run the task: `yoursite.tld/dev/tasks/PopulateTableShippingTask`

## Architecture

`ShippingPackage` is a class used to encapsulate shipping data including: weight, dimensions, value, quantity.

`ShippingMethod` is the base class for different types of shipping calculation.
These could either be flat rates, table based rates, or a

`TableShippingMethod` has many `TableShippingRate`, where `TableShppingRate extends RegionRestriction`.
Table shipping rates also have optional weight, volume, value, and quantity constraint fields.

### Region Restrictions

The `RegionRestriction` class serves as a base class for providing regionalised restrictions.
Restrictions are specified by Country, State, and PostalCode. A value/rate can be given to each
restriction. To work the appropriate rate, query for all the matchign restrictions, and sort
by cheapest.

The wildcard '*' means the restriction will match any region.