<?php

Object::add_extension("PopulateShopTask", "PopulateShopTableShippingTask");
Object::add_extension("Order", "OrderShippingDecorator");
Object::add_extension("CartPage_Controller", "CartPageDecorator");