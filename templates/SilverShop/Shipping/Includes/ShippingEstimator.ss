<div class="silvershop-shipping-estimator">
    <% require themedCSS(shippingestimates,silvershop-shipping) %>
    <h3 class="silvershop-shipping-estimator__heading">Get shipping estimate:</h3>
    <div class="silvershop-shipping-estimator__form">
        $ShippingEstimateForm
    </div>
    <div class="silvershop-shipping-estimator__results">
        <% include SilverShop\Shipping\Includes\ShippingEstimates %>
    </div>
</div>
