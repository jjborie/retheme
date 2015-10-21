<div class="layout__col layout__col--small layout__col--second">
	<div class="mortgage__calc mortgage__calc-main side__blocks">
		<h4 class="title--colored">Mortgage Calculator</h4>
		<div class="mortgage__calc-line clearfix">
			<div class="mortgage__calc-cell">
				<span>Mortgage Amount</span>
				<input type="number" placeholder="Mortgage Amount" value=" " name="mortage_sum" >
			</div>
			<div class="mortgage__calc-cell">
				<span>Interest Rate</span>
				<select name="rate">
					<option value="2.7900"> 2.79% in 1 years </option>
					<option value="2.3400"> 2.34% in 2 years </option>
					<option value="2.4000"> 2.40% in 3 years </option>
					<option value="2.1000" selected="selected"> 2.10% in 5 years </option>
				</select>
			</div>
			<div class="mortgage__calc-cell">
				<span>Amortization Period</span>
				<select name="amortization_period">
					<option value="1"> 1 year </option>
					<option value="2"> 2 years </option>
					<option value="3"> 3 years </option>
					<option value="4"> 4 years </option>
					<option value="5"> 5 years </option>
					<option value="6"> 6 years </option>
				</select>
			</div>
			<div class="mortgage__calc-cell">
				<span>Payment Frequency </span>
				<select name="payment_frequency">
					<option value="weekly" selected="selected">Weekly</option>
					<option value="rapid_weekly">Rapid Weekly</option>
					<option value="biweekly">Bi-Weekly</option>
					<option value="rapid_biweekly">Rapid Bi-Weekly</option>
					<option value="monthly">Monthly</option>
				</select>
			</div>	
			<h2 class="title--underlined">Your mortgage payment would be - <strong> 889.02$ / Weekly </strong></h2>
			<a class="blue_button universal-button" href="#" data-action="recalculate">Calculate</a>
		</div>

	</div>
</div>