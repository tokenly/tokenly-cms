<h2>Token Item Inventory</h2>
<?php
if(count($balances) == 0){
	echo '<p>No token balances found, have you registered and verified a bitcoin address in the <a href="'.SITE_URL.'/dashboard/tokenly/address-manager" target="_blank">Address Manager</a>?</p>';
}
elseif(!$token_items OR count($token_items) == 0){
	echo '<p>You have no items.</p>';
}
else{
	//show items grid
	$total_count = 0;
	foreach($token_items as $item){
		$total_count += $item['count'];
	}
	echo '<p><strong># Unique Items:</strong> '.number_format(count($token_items)).'
				<br><strong>Total # Items:</strong> '.number_format($total_count).'
			</p>';
	echo '<ul class="token-item-list">';
	foreach($token_items as $item){
		echo '<li>';
		?>
		<div class="token-item-wrap">
			<a href="<?= SITE_URL ?>/items/item/<?= $item['slug'] ?>" target="_blank" class="token-item-link"></a>
			<div class="token-item-image">
				<?php
					if(trim($item['image']) == ''){
						//show default image
						echo '<img src="'.SITE_URL.'/resources/no-image.gif" alt="" />';
					}
					else{
						echo '<img src="'.SITE_URL.'/files/tokenitems/'.$item['image'].'" alt="" />';
					}
				?>
			</div>
			<div class="token-item-title">
				<?= $item['name'] ?>
				<span><?= $item['token'] ?></span>
			</div>
			<div class="token-item-count">
				<?= number_format($item['count']) ?>
			</div>
		</div>
		<?php
		echo '</li>';
	}
	echo '</ul>';
}
?>
<div class="clear"></div>
