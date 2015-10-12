<h2>Asset Scouter</h2>
<?= $this->displayFlash('message') ?>
<p>
	Use this tool to discover how many users hold a specific token and who they are.
</p>
<p>
	<em>Note: data is based on last cached token balances for each user and may not be 100% up to date.</em>
</p>
<?= $form->display() ?>
<?php
if($scout){
	
	if($scout['isUser']){
		echo '<h3>'.$scout['user']['username'].'\'s Inventory</h3>';
		echo '<p>
				<a href="'.SITE_URL.'/profile/user/'.$scout['user']['slug'].'" target="_blank">Go to user profile</a>
			  </p>';
			  
		echo '<ul class="token-list">';
		$model = new \Core\Model;
		$usedAssets = array();
		foreach($scout['balances'] as $address => $balances){
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
				echo '<p><strong>More Information:</strong> <a href="'.$usedAsset['link'].'" target="_blank">'.$usedAsset['link'].'</a></p>';
			}
			echo '</div>';
		}		
		?>
	<script type="text/javascript">
		$(document).ready(function(){
			$('.fancy').fancybox();
		});
	</script>		
		<?php
	}
	elseif($scout['isAddress']){
		echo '<h3>Users with address '.$scout['address'].'</h3>';
		echo '<p><strong>Total Users:</strong> '.number_format(count($scout['users'])).'</p>';
		if(count($scout['users']) == 0){
			echo '<p>No users found...</p>';
		}
		else{
			echo '<ul class="address-users">';
			foreach($scout['users'] as $scoutUser){
				echo '<li><strong><a href="'.SITE_URL.'/profile/user/'.$scoutUser['slug'].'" target="_blank">'.$scoutUser['username'].'</a></strong></li>';
			}
			echo '</ul>';
		}
	}
	else{
?>
	<ul>
		<li><strong>Total Users:</strong> <?= number_format($scout['users']) ?></li>
		<li><strong>Total Unique Addresses:</strong> <?= number_format($scout['addresses']) ?></li>
		<li><strong>Combined Balance:</strong> <?= number_format($scout['balance'], 8) ?></li>
	</ul>
	<div class="clear"></div>
	<hr>
	<h3><?= $scout['asset'] ?> User List</h3>
	<table class="admin-table data-table submissions-table mobile-table">
		<thead>
			<tr>
				<th>User</th>
				<th># Addresses</th>
				<th>Balance</th>
				<th>Last Checked</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach($scout['list'] as $item){
			?>
			<tr>
				<td class="post-title"><a href="?asset=<?= $item['username'] ?>" ><?= $item['username'] ?></a></td>
				<td><?= $item['addresses'] ?></td>
				<td><?= number_format($item['balance'], 8) ?></td>
				<td><?= date('Y/m/d \<\b\r\> H:i', strtotime($item['last_check'])) ?></td>
			</tr>
			<?php
			}
			?>
			
		</tbody>
	</table>
<link href="//cdn.datatables.net/1.10.3/css/jquery.dataTables.css" rel="stylesheet" />
<script type="text/javascript" src="//cdn.datatables.net/1.10.3/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">	
	$(document).ready(function(){
	
		var tables = $('.data-table').DataTable({
			searching: true,
			lengthChange: false,
			paging: true,
			iDisplayLength: 20,
			"order": [[ 2, "desc" ]]
		});	
		
	});
</script>	
<?php
	}
}
?>
