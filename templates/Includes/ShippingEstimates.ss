<% if ShippingEstimates %>
	<ul>
		<% control ShippingEstimates %>
			<li>$Rate.Nice - $Name</li>
		<% end_control %>
	</ul>
<% end_if %>