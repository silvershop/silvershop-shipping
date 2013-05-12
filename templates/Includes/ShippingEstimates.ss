<% if ShippingEstimates %>
	<ul class="estimatelist">
		<% loop ShippingEstimates %>
			<li>$Rate.Nice - $Name: $Description</li>
		<% end_loop %>
	</ul>
<% end_if %>
