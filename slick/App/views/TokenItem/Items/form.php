<h2><?= $formType ?> Token Item</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>">Go Back</a>
</p>
<?= $this->displayFlash('message') ?>
<?= $form->display() ?>
<?php
	if(isset($token_item)){
		if(trim($token_item['image']) != ''){
			$imagesize = getimagesize(SITE_PATH.'/files/tokenitems/'.$token_item['image']);
			echo '<div style="max-width: 400px; position: relative; text-align: center;"><p><a href="'.SITE_URL.'/files/tokenitems/'.$token_item['image'].'" target="_blank"><img src="'.SITE_URL.'/files/tokenitems/'.$token_item['image'].'" alt="" /></a><br>(natural dimensions: '.$imagesize[0].'*'.$imagesize[1].'px)</p></div>';
		}
	}
?>

<script type="text/javascript">
	$(document).ready(function(){
		
		
	});
</script>
