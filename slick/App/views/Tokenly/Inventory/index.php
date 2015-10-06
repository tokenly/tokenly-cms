<h2>Token Inventory</h2>
<p>
	This is your Token Inventory, a list of balances for all CounterParty (XCP) tokens
	that live on each of your <strong>verified, XCP compatible</strong> bitcoin addresses.
	Token balances may effect or grant additional access to features of the website via the "Token Controlled Access" (<strong>TCA</strong>) system - coming soon!
	Some token names may be clicked on to view additional info.
</p>
<p>
	Go to your <a href="<?= SITE_URL ?>/<?= $app['url'] ?>/address-manager">Address Manager</a> to add and verify new bitcoin addresses.
</p>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/inventory/transactions">Click here</a> to view your recent inventory transactions.
</p>
<form action="" method="post">
<input type="submit" name="forceRefresh" style="font-size: 14px;" id="forceRefresh" value="Force Balance Refresh" />
</form>
<h4>My Tokens</h4>
<p>
	<?php
	if($grouped){
		echo '<em><a href="?grouped=0">Click to display grouped by address</a></em>';
	}
	else{
		echo '<em><a href="?grouped=1">Click to display as grouped totals</a></em>';
	}
	?>
</p>
<?php
if(count($addressBalances) == 0){
	echo '<p>Looks like you don\'t have any tokens yet. Have you <a href="'.SITE_URL.'/'.$app['url'].'/address-manager">added and verified</a> a bitcoin address yet?</p>';
}
else{
	$model = new \Core\Model;
	$usedAssets = array();
	echo '<ul class="token-list">';
	if($grouped){
		foreach($addressBalances as $asset => $amnt){
			$getAsset = $model->get('xcp_assetCache', $asset, array(), 'asset');
			$divisible = true;
			if($getAsset AND $getAsset['divisible'] == 0){
				$divisible = false;
			}
			if($divisible){
				$amnt = number_format($amnt, 8);
			}
			else{
				$amnt = number_format($amnt, 0);
			}
			if(trim($getAsset['description']) != '' OR trim($getAsset['link']) != ''){
				$asset = '<a href="#asset-'.$asset.'" class="fancy">'.$asset.'</a>';
			}			
			echo '<li><strong>'.$asset.':</strong> '.$amnt.'</li>';
			if(!isset($usedAssets[$asset])){
				$usedAssets[$asset] = $getAsset;
			}
		}
	}
	else{
		foreach($addressBalances as $address => $balances){
			echo '<li><strong><a href="http://blockscan.com/address.aspx?q='.$address.'" target="_blank">'.$address.'</a></strong>
					<ul>';
			foreach($balances as $asset => $amnt){
				$getAsset = $model->get('xcp_assetCache', $asset, array(), 'asset');
				$divisible = true;
				if($getAsset AND $getAsset['divisible'] == 0){
					$divisible = false;
				}
				if($divisible){
					$amnt = number_format($amnt, 8);
				}
				else{
					$amnt = number_format($amnt, 0);
				}
				if(!isset($usedAssets[$asset])){
					$usedAssets[$asset] = $getAsset;
				}
				if(trim($getAsset['description']) != '' OR trim($getAsset['link']) != ''){
					$asset = '<a href="#asset-'.$asset.'" class="fancy">'.$asset.'</a>';
				}
				echo '<li><strong>'.$asset.':</strong> '.$amnt.'</li>';
			}
			echo '</ul></li>';
		}
	}
	
	echo '</ul>';
	
	foreach($usedAssets as $usedAsset){
		if(trim($usedAsset['description']) == '' AND trim($usedAsset['link']) == ''){
			continue;
		}
		echo '<div id="asset-'.$usedAsset['asset'].'" style="display: none;">';
		if(trim($usedAsset['description']) != ''){
			echo '<p><strong>Description:</strong><br>'.markdown($usedAsset['description']).'</p>';
		}
		if(trim($usedAsset['link']) != ''){
			echo '<p><strong>Project Information:</strong> <a href="'.$usedAsset['link'].'" target="_blank">'.$usedAsset['link'].'</a></p>';
		}
		echo '<p><strong>View token data on <a href="http://blockscan.com/assetInfo/'.$usedAsset['asset'].'" target="_blank">Blockscan</a></strong> </p>';
		echo '</div>';
	}
}
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('.fancy').fancybox();
	});
</script>
