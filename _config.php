<?php

PopulateShopTask::add_extension("PopulateShopTableShippingTask");
Order::add_extension("OrderShippingDecorator");
CartPage_Controller::add_extension("CartPageDecorator");