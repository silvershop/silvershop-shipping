<table class="silvershop-shipping-estimates">
	<thead class="silvershop-shipping-estimates__head">
		<tr class="silvershop-shipping-estimates__row silvershop-shipping-estimates__row--head">
			<th class="silvershop-shipping-estimates__cell silvershop-shipping-estimates__cell--rate">Rate</th>
			<th class="silvershop-shipping-estimates__cell silvershop-shipping-estimates__cell--name">Name</th>
			<th class="silvershop-shipping-estimates__cell silvershop-shipping-estimates__cell--description">Description</th>
		</tr>
	</thead>
	<tbody class="silvershop-shipping-estimates__body">
		<% loop ShippingEstimates %>
			<tr class="silvershop-shipping-estimates__row">
				<td class="silvershop-shipping-estimates__cell silvershop-shipping-estimates__cell--rate">$Rate.Nice</td>
				<% if $Name == "ShippingEstimates" %>
					<td class="silvershop-shipping-estimates__cell silvershop-shipping-estimates__cell--name silvershop-shipping-estimates__cell--name-empty"></td>
				<% else %>
					<td class="silvershop-shipping-estimates__cell silvershop-shipping-estimates__cell--name">$Name</td>
				<% end_if %>
				<td class="silvershop-shipping-estimates__cell silvershop-shipping-estimates__cell--description">$Description</td>
			</tr>
		<% end_loop %>
	</tbody>
</table>
