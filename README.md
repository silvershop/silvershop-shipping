## Shipping Framework

This isn't actually a sub-module, but really a bunch of potential changes to the shop system.

## Installation

 * Put the shop_shippingframework folder into your SilverStripe root directory
 * Add the ShippingFrameWorkModifier to your modifiers config, eg:
 
	Order::set_modifiers(array(
		'ShippingFrameworkModifier',
		'FlatTaxModifier'
	));
	
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