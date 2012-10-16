<% if ShippingEstimates %>
	<ul class="estimatelist">
		<% control ShippingEstimates %>
			<li>$Rate.Nice - $Name: $Description</li>
		<% end_control %>
	</ul>
<% end_if %>