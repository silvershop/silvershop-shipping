<?php

Object::add_extension("PopulateShopTask", "PopulateTableShippingTask");
Object::add_extension("Order", "OrderShippingDecorator");
Object::add_extension("CartPage_Controller", "CartPageDecorator");