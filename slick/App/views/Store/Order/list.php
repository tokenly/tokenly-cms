<h2>Orders & Payments</h2>
<?php

$numComplete = 0;
$numIncomplete = 0;
$receivedAssets = array();
foreach($orders as $order){
	if($order['complete'] == 1){
		$numComplete++;
	}
	else{
		$numIncomplete++;
	}
	if(!isset($receivedAssets[$order['asset']])){
		$receivedAssets[$order['asset']] = $order['received'];
	}
	else{
		$receivedAssets[$order['asset']] += $order['received'];
	}
}
?>

<ul>
	<li><strong>Complete Orders:</strong> <?= $numComplete ?></li>
	<li><strong>Incomplete Orders:</strong> <?= $numIncomplete ?></li>
	<li><strong>Payments Received:</strong><br>
		<ul>
			<?php
			foreach($receivedAssets as $asset => $amount){
				$showAmount = number_format($amount);
				switch($asset){
					case 'BTC':
						$showAmount = convertFloat($amount);
						break;
				}
				echo '<li><strong>'.$showAmount.' '.$asset.'</strong></li>';
			}
			?>
		</ul>
	</li>
</ul>


<?php
if(count($orders) == 0){
	echo '<p>No orders found.</p>';
}
else{
	echo '<table class="admin-table mobile-table">
	<thead>
		<tr>
			<th>Order ID</th>
			<th>Type</th>
			<th>Amount</th>
			<th>Paid/Complete?</th>
			<th>Order Date</th>
			<th></th>
		</tr>
	</thead>
	<tbody>';
	
	$model = new \Core\Model;
	foreach($orders as $order){
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
		echo '<tr>';
		echo '<td>'.$order['orderId'].'</td>';
		echo '<td>'.$type.'</td>';
		echo '<td>'.$amount.' '.$order['asset'].'</td>';
		echo '<td>'.boolToText($order['complete']).'</td>';
		echo '<td>'.formatDate($order['orderTime']).'</td>';
		echo '<td><a href="#details-'.$order['orderId'].'" class="fancy">View Details</a><br>
				  <a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/'.$order['orderId'].'" class="delete">Delete</a>
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
			</td>';
		echo '</tr>';
	}

	echo '</tbody></table>';
}
?>
