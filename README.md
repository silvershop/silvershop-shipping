## Shipping Framework

This isn't actually a sub-module, but really a bunch of potential changes to the shop system.

## Installation

 * Put the shop_shippingframework folder into your SilverStripe root directory
 * Add the ShippingFrameWorkModifier to your modifiers config, eg:
 
	Order::set_modifiers(array(
		'ShippingFrameworkModifier',
		'FlatTaxModifier'
	));

## Architecture

`ShippingPackage` is a class used to encapsulate shipping data such as weight.

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

## To Do:

 * Add Country and State models. Include pre-defined lists for popular countries.
 * Create API-connected shipping options.
 * Store estimate data in session to pre-populate the checkout form(s)
 * Possibly ditch the ShippingFrameworkModifier, and enforce always using the shippingframework