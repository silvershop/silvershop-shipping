<% if ShippingEstimates %>
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
					<td>$Name</td>
					<td>$Description</td>
				</tr>
			<% end_loop %>
		</tbody>
	</table>
<% end_if %>