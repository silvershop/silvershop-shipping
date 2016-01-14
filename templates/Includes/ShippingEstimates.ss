<table class="table estimatelist">
	<thead>
		<tr>
			<th>Rate</th>
			<th>Name</th>
			<th>Description</th>
		</tr>
	</thead>
	<tbody>
		<% loop ShippingEstimates %>
			<tr>
				<td>$Rate.Nice</td>
				<% if $Name == "ShippingEstimates" %>
					<td></td>
				<% else %>
					<td>$Name</td>
				<% end_if %>
				<td>$Description</td>
			</tr>
		<% end_loop %>
	</tbody>
</table>
