<h2>Inventory Transactions</h2>
<p>
	Below is a combined list of transactions seen for all
	<a href="<?= SITE_URL ?>/tokenly/address-manager">verified addresses</a> in your <a href="<?= SITE_URL ?>/dashboard/tokenly/inventory">inventory</a>.
</p>
<p>
	Note: only recent transactions are recorded and are not recorded in real time. <br>
	For full and complete transaction histories,
	use a block explorer such as <a href="https://chain.so" target="_blank">Chain.so</a>
	or <a href="https://blockscan.com" target="_blank">Blockscan</a>.
</p>
<?php
if($last_address_update){
	echo '<p><strong>Last transaction check:</strong> '.formatDate($last_address_update).'</p>';
}
$asset_descs = array();
if(!$transactions OR count($transactions) == 0){
	echo '<p><strong>No inventory transactions found. Have you <a href="'.SITE_URL.'/tokenly/address-manager">registered and verified</a> any bitcoin addresses yet?</strong></p>';
}
else{
	echo '<div class="inventory-tx-list">';
	echo '<table class="admin-table data-table mobile-table">';
	?>
	<thead>
		<tr>
			<th>Date</th>
			<th class="no-sort">Sender</th>
			<th class="no-sort">Asset</th>
			<th class="no-sort">Receiver</th>
			<th class="no-sort"></th>
		</tr>
	</thead>
	<tbody>
	<?php
	foreach($transactions as $tx){
		$tx_symbol = 'fa-plus';
		$tx_class = 'text-success';
		if(isset($tx['txInfo']['to'])){
			if($tx['txInfo']['to'] != $tx['address']){
				$tx_symbol = 'fa-minus';
				$tx_class = 'text-error';
			}
		}
		$time_diff = $time = $tx['time'];
		if($time_diff < 86400){
			$show_time = date('h:i A', $tx['time']);
		}
		else{
			$show_time = date('Y/m/d', $tx['time']);
		}
		if(!isset($tx['txInfo']['from']) OR !isset($tx['txInfo']['to'])){
			continue;
		}
		$from_display = '<span title="'.$tx['txInfo']['from'].'"><a href="https://chain.so/address/BTC/'.$tx['txInfo']['from'].'" target="_blank">'.shortenMsg($tx['txInfo']['from'],12).'</a></span>';
		if(is_array($tx['txInfo']['from_user'])){
			$from_display = '<span title="'.$tx['txInfo']['from_user']['username'].'"><a href="'.SITE_URL.'/profile/user/'.$tx['txInfo']['from_user']['slug'].'" target="_blank">'.$tx['txInfo']['from_user']['username'].'</a></span><br><small>('.$from_display.')</small>';
		}
		$to_display = '<a href="https://chain.so/address/BTC/'.$tx['txInfo']['to'].'" target="_blank">'.shortenMsg($tx['txInfo']['to'],12).'</a>';
		if(is_array($tx['txInfo']['to_user'])){
			$to_display = '<span title="'.$tx['txInfo']['to_user']['username'].'"> <a href="'.SITE_URL.'/profile/user/'.$tx['txInfo']['to_user']['slug'].'" target="_blank">'.$tx['txInfo']['to_user']['username'].'</a></span><br><small>('.$to_display.')</small>';
		}					
		$to_display = '<span title="'.$tx['txInfo']['to'].'"> <i class="fa fa-arrow-right"></i> '.$to_display.'</span>';
		if(!in_array($tx['asset'], $asset_descs)){
			$asset_descs[] = $tx['asset'];
		}		
		?>
		<tr>
			<td><i class="fa fa-clock-o"></i> <?= $show_time ?></td>
			<td class="inv-tx-addr"><?= $from_display ?></td>
			<td>
				<span class="inv-tx-asset <?= $tx_class ?>">
					<span class="inv-tx-symbol"><i class="fa <?= $tx_symbol ?>"></i></span>
					<span class="inv-tx-amount"><?= convertFloat(abs($tx['amount'])) ?></span>							
					<span class="inv-tx-asset-link"><a href="#asset-desc-<?= $tx['asset'] ?>" class="fancy"><?= $tx['asset'] ?></a></span>
				</span>			
			</td>
			<td class="inv-tx-addr"><?= $to_display ?></td>
			<td>
			<?php
			if($tx['type'] == 'xcp'){
				echo '<a href="https://blockscan.com/tx?txhash='.$tx['txId'].'" class="btn btn-blue btn-small" target="_blank">View transaction</a>';
			}
			else{
				echo '<a href="https://chain.so/tx/BTC/'.$tx['txId'].'"  class="btn btn-blue btn-small" target="_blank">View transaction</a>';
			}
			?>			
			</td>
		</tr>
		<?php
	}
	echo '</tbody></table>';
	echo '</div>';
}//endif

?>
<?php
$inventory = new \App\Tokenly\Inventory_Model;
foreach($asset_descs as $asset){
	$asset = strtoupper($asset);
	if($asset == 'BTC'){
		echo '<div style="display: none;" id="asset-desc-BTC">';
		echo '<p><strong>Bitcoin!</strong></p>';
		echo '</div>';
	}
	else{
		$getAsset = $inventory->getAssetData($asset);
		if($getAsset){
			echo '<div style="display: none; width: 400px;" id="asset-desc-'.$asset.'">';
			if(trim($getAsset['description']) != ''){
				echo '<p><strong>Description:</strong><br>'.markdown($getAsset['description']).'</p>';
			}
			else{
				echo '<p><strong>No token description available.</strong></p>';
			}
			if(trim($getAsset['link']) != ''){
				echo '<p><strong>Project Information:</strong> <a href="'.$getAsset['link'].'" target="_blank">'.$getAsset['link'].'</a></p>';
			}
			echo '<p><strong>View token data on <a href="http://blockscan.com/assetInfo/'.$getAsset['asset'].'" target="_blank">Blockscan</a></strong> </p>';
			echo '</div>';
		}
	}
}
?>
<link href="//cdn.datatables.net/1.10.3/css/jquery.dataTables.css" rel="stylesheet" />
<script type="text/javascript" src="//cdn.datatables.net/1.10.3/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
	
		var tables = $('.data-table').DataTable({
			searching: true,
			lengthChange: true,
			paging: true,
			iDisplayLength: 10,
			"order": [[ 3, "desc" ]]
		});		
		

		
	});
</script>
