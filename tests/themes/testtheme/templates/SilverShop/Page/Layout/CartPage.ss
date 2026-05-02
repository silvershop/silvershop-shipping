<h1 class="silvershop-pagetitle">$Title</h1>
<div class="silvershop-typography">
	<% if Content %>
		$Content
	<% end_if %>
</div>
<% if Cart %>

	<% if CartForm %>
		$CartForm

		<!-- start Shipping Estimator -->
		<% include SilverShop\Shipping\Includes\ShippingEstimator %>
		<!-- finish Shipping Estimator -->

	<% else %>
		<% with Cart %><% include SilverShop\Cart\Cart Editable=true %><% end_with %>
	<% end_if %>

<% else %>
	<p class="silvershop-message silvershop-warning"><%t CartPage.ss.CARTEMPTY 'Your cart is empty.' %></p>
<% end_if %>
<div class="silvershop-cartfooter">
	<% if ContinueLink %>
		<a class="silvershop-continuelink silvershop-button" href="$ContinueLink">
			<%t CartPage.ss.CONTINUE 'Continue Shopping' %>
		</a>
	<% end_if %>
	<% if Cart %>
		<% if CheckoutLink %>
			<a class="silvershop-checkoutlink silvershop-button" href="$CheckoutLink">
				<%t CartPage.ss.PROCEEDTOCHECKOUT 'Proceed to Checkout' %>
			</a>
		<% end_if %>
	<% end_if %>
</div>
