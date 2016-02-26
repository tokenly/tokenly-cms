<h1>Token Item: <?= $token_item['name'] ?></h1>
<hr>
<div class="token-item-single">
	<div class="token-item-image pull-right">
		<?php
			if(trim($token_item['image']) == ''){
				//show default image
				echo '<img src="'.SITE_URL.'/resources/no-image.gif" alt="" />';
			}
			else{
				echo '<a href="'.SITE_URL.'/files/tokenitems/'.$token_item['image'].'" target="_blank"><img src="'.SITE_URL.'/files/tokenitems/'.$token_item['image'].'" alt="" /></a>';
			}
		?>
	</div>
	<div class="token-item-info">
		<p>
			<strong>Token:</strong> <a href="https://blockscan.com/assetInfo/<?= $token_item['token'] ?>" target="_blank"><?= $token_item['token'] ?> <i class="fa fa-info-circle"></i></a>
			<br>
			<strong>Amount Required:</strong> <?= rtrim(rtrim(number_format($token_item['min_token'] / SATOSHI_MOD, 8), '0'), '.') ?>
		</p>
		<?php
		if(trim($token_item['description']) != ''){
		?>
			<strong>Description:</strong>
			<?= markdown($token_item['description']) ?>
		<?php
		}
		if(is_array($token_item['properties'])){
			foreach($token_item['properties']  as $k => $prop){
				if(trim($prop['value']) == ''){
					unset($token_item['properties'][$k]);
					continue;
				}
			}
			if(count($token_item['properties']) > 0){
				echo '<p><strong>Additional Properties:</strong></p>';
				echo '<ul class="token-item-properties">';
				foreach($token_item['properties'] as $prop){
					if(trim($prop['description']) != ''){
						echo '<li><strong><a href="#prop-'.$prop['id'].'" class="fancy">'.$prop['name'].'</a>:</strong> '.$prop['value'];
						echo '<div id="prop-'.$prop['id'].'" style="display: none;">
									<h3>Property: '.$prop['name'].'</h3>
									'.markdown($prop['description']).'
								</div>';
						echo '</li>';
					}
					else{
						echo '<li><strong>'.$prop['name'].':</strong> '.$prop['value'].'</li>';
					}
				}
				echo '</ul>';
			}
		}
		?>
	</div>
</div>
<?php
/*
 * info to show
 * 	- what token is associated with it
 * 	- link to blockscan for token
 * 	- show min amount required for 1 of this item
 * 	- list off properties
 * 	- style it
 * */
?>
<div class="clear"></div>
