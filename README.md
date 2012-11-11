## Shipping Framework

This isn't actually a sub-module, but really a bunch of potential changes to the shop system.

## Requirements

 * Shop module v0.9

## Installation

 * Put the shop_shippingframework folder into your SilverStripe root directory
 * Add the ShippingFrameWorkModifier to your modifiers config, eg:


    Order::set_modifiers(array(
        'ShippingFrameworkModifier',
        'FlatTaxModifier'
    ));


 * You need to use the new SteppedCheckout system to allow customers to set their address
 before they choose their shipping method. To set up steps, including the shippingmethod step,
 add the following to your mysite/_config.php file:


    SteppedCheckout::setupSteps(array(
        'contactdetails' => 'CheckoutStep_ContactDetails',
        'shippingaddress' => 'CheckoutStep_Address',
        'billingaddress' => 'CheckoutStep_Address',
        'shippingmethod' => 'CheckoutStep_ShippingMethod',
        'paymentmethod' => 'CheckoutStep_PaymentMethod',
        'summary' => 'CheckoutStep_Summary'
    ));


 * To add the shipping estimation form to your CartPage template, add the following
 somewhere on your CartPage.ss template:


    <% include ShippingEstimator %>


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