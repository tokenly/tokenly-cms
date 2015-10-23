<h2><?= $formType ?> Tracking URL</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>">Go Back</a>
</p>
<?= $this->displayFlash('message') ?>
<?= $form->display() ?>
<?php
	if(isset($tracking_url)){
		if(trim($tracking_url['image']) != ''){
			$imagesize = getimagesize(SITE_PATH.'/files/ads/'.$tracking_url['image']);
			echo '<div style="max-width: 400px; position: relative; text-align: center;"><p><a href="'.SITE_URL.'/files/ads/'.$tracking_url['image'].'" target="_blank"><img src="'.SITE_URL.'/files/ads/'.$tracking_url['image'].'" alt="" /></a><br>(natural dimensions: '.$imagesize[0].'*'.$imagesize[1].'px)</p></div>';
		}
	}
?>
