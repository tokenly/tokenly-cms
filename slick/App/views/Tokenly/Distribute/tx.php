<?php
if($distribute['divisible'] == 1){
	$distribute['total'] = $distribute['total'] / SATOSHI_MOD;
}
?>
<div class="distribute-qr">
	<img src="<?= SITE_URL ?>/qr.php?q=<?= $distribute['address'] ?>" alt="" />
</div>
<?php
if(trim($distribute['name']) != ''){
	echo '<h2><em>'.$distribute['name'].'</em></h2>';
}
?>
<h2><?= $distribute['asset'] ?> Token Distribution (ID: <?= $distribute['distributeId'] ?>)</h2>
<h3 style="color: #000;"><a href="bitcoin:<?= $distribute['address'] ?>"><?= $distribute['address'] ?></a></h3>
<p>
	<a href="<?= $site['url'] ?>/<?= $app['url'] ?>/<?= $module['url'] ?>">Go Back</a>
</p>
<ul class="asset-info">
	<li><strong>Status</strong>: <?= $distribute['status'] ?></li>
	<li><strong>Asset Name</strong>: <?= $distribute['asset'] ?></li>
	<li><strong>Payment Address</strong>: <a href="https://blockchain.info/address/<?= $distribute['address'] ?>" target="_blank"><?= $distribute['address'] ?></a></li>
	<li><strong>Initilization Date:</strong> <?= formatDate($distribute['initDate']) ?></li>
	<?php
	if($distribute['complete'] != 0){
		echo '<li><strong>Completion Date:</strong> '.formatDate($distribute['completeDate']).'</li>';
	}
	?>
	<?php
	if($distribute['divisible'] != 0){
		?>
		<li><strong>Total Sending</strong>: <?= number_format($distribute['total'], 8) ?></li>
		<?php
	}
	else{
		?>
		<li><strong>Total Sending</strong>: <?= number_format($distribute['total']) ?></li>
		<?php
	}
	?>
	<li><strong># Addresses</strong>: <?= number_format(count($distribute['addressList'])) ?></li>
	<li><strong>Last Batch #:</strong> <?= $distribute['currentBatch'] ?></li>
	<li><strong>Completed?</strong>: <?= boolToText($distribute['complete']) ?></li>
	<?php
	if(trim($distribute['txInfo']) != ''){
		?>
		<li><a href="#sent-tx">View Transactions</a></li>
	<?php
	}
	if($perms['canChangeDistributeStatus'] || $perms['canChangeDistributeLabels']){
		echo '<li><strong><a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'.$distribute['address'].'">Edit Details</a></strong></li>';
	}
	?>
	
</ul>
<br>
<?php
	$monitorStatus = '';
	$checkMonitor = exec('pgrep xcp-distributor', $checkMonitor);
	
	if(!$checkMonitor OR trim($checkMonitor) == ''){
		$monitorStatus = 'OFFLINE';
	}
	else{
		$monitorStatus = 'ONLINE';
	}
?>
<p>
	<strong>Tokens Received:</strong> <?= convertFloat($distribute['tokenReceived']) ?> / <?= convertFloat($distribute['total']) ?> <?= $distribute['asset'] ?><br>
	<strong>Fee Received:</strong> <?= convertFloat($distribute['feeReceived']) ?> / <?= convertFloat($distribute['fee']) ?> BTC<br>
	<strong>Transaction Monitor:</strong> <?= $monitorStatus ?><br>
	<em style="font-size: 11px;">Note: BTC sent above the required fee will add to the overall transaction fee, which may result in faster confirmation.</em>
</p>
<?php
if($monitorStatus == 'OFFLINE'){
	echo '<p class="error">Please contact administration to restart the transaction monitor</p>';
}
?>
<h4>Distribution Addresses:</h4>
<?php
$tableData = array();
$xcpModel = new Slick_App_Tokenly_Distribute_Model;
foreach($distribute['addressList'] as $addr => $amount){
	if($distribute['divisible'] == 1){
		$amount = $amount / SATOSHI_MOD;
		$amount = number_format($amount, 8);
	}
	else{
		$amount = number_format($amount);
	}
	
	$andUserName = '';
	$lookup = $xcpModel->lookupAddress($addr);
	if($lookup){
		$andUserName = ' - ('.$lookup['names'].')';
	}
	
	
	$tableData[] = array('address' => '<a href="https://blockchain.info/address/'.$addr.'" target="_blank">'.$addr.'</a> '.$andUserName, 'amount' => $amount);
}

$table = new Slick_UI_Table;
$table->addClass('admin-table');
$table->setData($tableData);
$table->addColumn('address', 'Address');
$table->addColumn('amount', 'Amount');
echo $table->display();

if(trim($distribute['txInfo']) != ''){
	
	$txInfo = json_decode($distribute['txInfo'], true);
	$infoData = array();
	foreach($txInfo as $tx){
		if($tx['result']['code'] == 200){
			if($distribute['divisible'] == 1){
				$tx['details'][3] = $tx['details'][3] / SATOSHI_MOD;
				$tx['details'][3] = number_format($tx['details'][3], 8);
			}
			else{
				$tx['details'][3] = number_format($tx['details'][3]);
			}
			
			$andUserName = '';
			$lookup = $xcpModel->lookupAddress($tx['details'][1]);
			if($lookup){
				$andUserName = ' - ('.$lookup['names'].')';
			}
			
			
			
			
			$infoData[] = array('address' => $tx['details'][1].$andUserName, 'txId' => '<a href="http://blockchain.info/tx/'.$tx['result']['txId'].'" target="_blank">'.$tx['result']['txId'].'</a>',
								'amount' => $tx['details'][3]);
		}
	}
	
	if(count($infoData) > 0){
		echo '<a name="sent-tx"></a><br>';
		echo '<h4>Sent Transactions: ('.count($infoData).' / '.count($distribute['addressList']).')</h4>';
		echo '<p style="font-size: 11px;"><em>If transactions seem to be stuck, try sending more BTC to cover network transaction fees</em></p>';
		if($distribute['complete'] == 1){
			echo '<p><a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/download/'.$distribute['address'].'" target="_blank">Download .CSV Transaction Report</a></p>';
		}
		$xTable = new Slick_UI_Table;
		$xTable->setData($infoData);
		$xTable->addColumn('address', 'Address');
		$xTable->addColumn('amount', 'Quantity');
		$xTable->addColumn('txId', 'TX ID');
		echo $xTable->display();
	}
	
	
}


?>
