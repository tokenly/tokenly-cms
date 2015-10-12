<?php
function displayPaymentOrderDetails($order, $app, $module)
{
	if(is_array($order) AND isset($order[0]['orderId'])){
		foreach($order as $item){
			echo displayPaymentOrderDetails($item, $app, $module);
		}
	}
	else{
		$model = new \Core\Model;
		$type = $order['orderType'];
		switch($type){
			case 'tca-forum':
				$type = 'TCA Forum';
				break;
		}
		$amount = number_format($order['amount']);
		$received = number_format($order['received']);
		switch($order['asset']){
			case 'BTC':
				$amount = convertFloat($order['amount']);
				$received = convertFloat($order['received']);
				break;
		}	
		echo '<a href="#details-'.$order['orderId'].'" class="fancy">View Details</a><br>
				  <div id="details-'.$order['orderId'].'" style="display: none;">
						<h3>Order #'.$order['orderId'].' - '.$type.'</h3>
						<p><strong>Payment Address:</strong> <a href="https://blockchain.info/address/'.$order['address'].'" target="_blank">'.$order['address'].'</a><br>
						<strong>Payment Account:</strong> '.$order['account'].'</p>
						<p>
							<strong>Price:</strong> '.$amount.' '.$order['asset'].'<br>
							<strong>Received:</strong> '.$received.' '.$order['asset'].'
						</p>
						<h4>Order Info</h4>
						<ul>';
		
		foreach($order['orderData'] as $key => $val){
			if($key == 'userId'){
				$getUser = $model->get('users', $val);
				if($getUser){
					$val .= ' (<a href="'.SITE_URL.'/profile/user/'.$getUser['slug'].'" target="_blank">'.$getUser['username'].'</a>)';
				}
			}
			echo '<li><strong>'.$key.':</strong> '.$val.'</li>';
		}
		if($order['complete'] == 1){
			echo '<li><strong>Completion Time:</strong> '.formatDate($order['completeTime']).'</li>';
		}
		echo '	  		</ul>
				  </div>
			';		
	}
}


?>
<h2>Payments Collector</h2>
<p>
	Use this tool to gather up any payments received for products/services which are sitting on the server.<br>
	For counterparty transactions, "dust fuel" may be required if each address does not have enough BTC to pay for miner fees+dust outputs.
	Send any amount to the fuel address to keep things well lubricated! (fuel address may change frequently, don't worry)
</p>
<ul>
	<li><strong>Total Server Balance:</strong> 
		<?php
		if(!$server_balance){
			echo 'N/A';
		}
		else{
			echo convertFloat($server_balance).' BTC';
		}
		?>
	</li>
	<li><strong>XCP Dust Fuel:</strong> 
		<?php
		if(!$fuel_info['balance']){
			echo 'N/A';
		}
		else{
			echo convertFloat($fuel_info['balance']).' BTC';
		}
		?>
	</li>
	<li><strong>Dust Fuel Address:</strong>
		<?php
		if(!$fuel_info['address']){
			echo 'N/A';
		}
		else{
			echo '<a href="https://blockchain.info/address/'.$fuel_info['address'].'" target="_blank">'.$fuel_info['address'].'</a>';
		}
		?>	
	</li>
</ul>
<hr>
<?php

echo $this->displayFlash('collector-message');
if($option_selected){
	$opt_title = $option_selected;
	switch($option_selected){
		case 'sponsor-form':
			$opt_title = 'Sponsorship Form Payments';
			break;
		case 'submission-credits';
			$opt_title = 'Blog Submission Credit Purchases';
			break;
		case 'tca-forums':
			$opt_title = 'Token Society Payments';
			break;
		case 'distributor-change';
			$opt_title = 'Leftover change/multisig outputs from distributions';
			break;
		case 'donate-verify':
			$opt_title = '"Verification-by-donation" funds';
			break;
		
	}
	echo '<h3>'.$opt_title.'</h3>';
	echo '<p><a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'">Cancel</a></p>';
	echo $collect_form->open();
	echo $collect_form->displayFields();
	
	$totals = array();
	$fuel_needed = 0;
	
	foreach($payments as $payment){
		if(!isset($totals[$payment['asset']])){
			$totals[$payment['asset']] = 0;
		}
		$totals[$payment['asset']] += $payment['amount'];
		if($payment['asset'] != 'BTC'){
			$fuel_needed += XCP_DEFAULT_FUEL;
		}
	}
	
	$real_fuel = $fuel_needed;
	if(isset($totals['BTC'])){
		$real_fuel -= $totals['BTC'];
		if($real_fuel < 0){
			$real_fuel = 0;
		}
	}
	
	echo '<p><strong>Total to Collect:</strong></p><ul class="asset-totals">';
	foreach($totals as $asset => $amount){
		echo '<li id="'.$asset.'-total"><strong>'.$asset.':</strong> <span class="amount">'.number_format($amount, 8).'</span></li>';
	}
	echo '</ul>';
	
	echo '<p><strong>Fuel Needed:</strong> <span class="real-fuel">'.number_format($real_fuel, 8).'</span> <small>(<span class="fuel-needed">'.number_format($fuel_needed, 8).'</span>)</small><br>
			 <strong>Payments Selected:</strong> <span class="num-selected">'.count($payments).'</span></p>';
	
	echo $collect_form->displaySubmit();

	echo '<p><em>Any BTC amounts are used towards collection fuel first.</em></p>';
	echo '<br>';
	if(count($payments) == 0){
		echo '<p>No available payments to collect.</p>';
	}
	else{
		echo '<table class="admin-table payment-table mobile-table">
				<thead>
					<tr>
						<th style="width:30px;"><input type="checkbox" id="check-all"></th>					
						<th>Item</th>
						<th>Amount</th>
						<th>Address</th>
						<th>Date</th>
						<th></th>
					</tr>
				</thead>
				<tbody>';
		
		foreach($payments as $k => $payment){
			?>
			<tr>
				<td><input type="checkbox" checked="checked" class="payment-values" id="payment-<?= $k ?>" name="payments[]" value="<?= $k ?>" data-amount="<?= convertFloat($payment['amount']) ?>" data-asset="<?= $payment['asset'] ?>" /></td>
				<td><label for="payment-<?= $k ?>"><?= $payment['title'] ?></label></td>
				<td><?= number_format($payment['amount'], 8) ?> <?= $payment['asset'] ?></td>
				<td><a href="https://blockchain.info/address/<?= $payment['address'] ?>" target="_blank"><?= $payment['address'] ?></a></td>
				<td><?= $payment['date'] ?></td>
				<td>
				<?php
				switch($option_selected){
					case 'sponsor-form':
					case 'tca-forums':
					case 'submission-credits':
						displayPaymentOrderDetails($payment['info'], $app, $module);
						break;
				}
				?>
				</td>
			</tr>
			<?php
		}
		
		echo '</tbody></table>';
	}
	echo $collect_form->close();
	?>
	<script type="text/javascript">
		$(document).ready(function(){
			
			function refreshPriceTotals()
			{
				var fuel_cost = <?= XCP_DEFAULT_FUEL ?>;
				var totalList = {};
				var numSelected = 0;
				var fuel_needed = 0;
				$('input[name="payments[]"]').each(function(){
					if($(this).is(':checked')){
						numSelected++;
						var asset = $(this).data('asset');
						var amount = parseFloat($(this).data('amount'));
						if(asset != 'BTC'){
							fuel_needed += fuel_cost;
						}
						if(amount > 0){
							if(typeof totalList[asset] == 'undefined'){
								totalList[asset] = 0;
							}
							totalList[asset] += amount;
						}
					}
					
				});
				
				var collectList = '';
				$.each(totalList, function(idx, val){
					collectList = collectList + '<li id="' + idx + '-total"><strong>' + idx + ':</strong> <span class="amount">' + val.toFixed(8) + '</span></li>';
				});
				$('.asset-totals').html(collectList);
				var real_fuel = fuel_needed;
				if(typeof totalList.BTC != 'undefined'){
					real_fuel = real_fuel - totalList.BTC;
					if(real_fuel < 0){
						real_fuel = 0;
					}
				}
				$('.num-selected').html(numSelected);
				$('.fuel-needed').html(fuel_needed.toFixed(8));
				$('.real-fuel').html(real_fuel.toFixed(8));
			
			}
			
			
			$('#check-all').click(function(e){
				if($(this).is(':checked')){
					$('.payment-table tbody').find('tr').each(function(){
						$(this).find('input[type="checkbox"]').attr('checked', 'checked');
					});
				}
				else{
					$('.payment-table tbody').find('tr').each(function(){
						$(this).find('input[type="checkbox"]').removeAttr('checked');
					});
				}
				refreshPriceTotals();
				
			});
			
			$('input[name="payments[]"]').click(function(){
				refreshPriceTotals();
			});
			
		});
	
	</script>
	<?php
}
else{
	echo $select_form->display();
	echo '<h3>Past Payment Collections</h3>';
	if(count($collections) == 0){
		echo '<p>No collections made yet.</p>';
	}
	else{
		echo '<table class="admin-table data-table mobile-table">
			<thead>
				<tr>
					<th style="width: 25px;">ID</th>
					<th>User</th>
					<th>Source</th>
					<th>Destination</th>
					<th>Amount</th>
					<th>TX/Type</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>';
		foreach($collections as $collect){
			$collectUser = 'N/A';
			if($collect['user']){
				$collectUser = linkify_username($collect['user']['username']);
			}
			echo '<tr>
					<td>'.$collect['collectionId'].'</td>
					<td>'.$collectUser.'</td>
					<td><a href="https://blockchain.info/address/'.$collect['source'].'" target="_blank">'.$collect['source'].'</a></td>
					<td><a href="https://blockchain.info/address/'.$collect['destination'].'" target="_blank">'.$collect['destination'].'</a></td>
					<td>'.number_format($collect['amount'], 8).' '.$collect['asset'].'</td>
					<td><a href="https://blockchain.info/tx/'.$collect['txId'].'" target="_blank">'.substr($collect['txId'], 0, 8).'</a><br>
						'.$collect['type'].'</td>
					<td>'.formatDate($collect['collectionDate']).'</td>
				  </tr>';
		}
		echo '</tbody></table>';
	}
}
?>
