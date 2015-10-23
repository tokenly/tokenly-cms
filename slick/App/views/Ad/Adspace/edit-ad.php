<h2>Manage Adspace - <?= $adspace['label'] ?></h2>
<h3>Edit Advertisement Settings</h3>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/edit/<?= $adspace['adspaceId'] ?>"><i class="fa fa-mail-reply"></i> Go back</a>
</p>
<?= $this->displayFlash('message') ?>
<div class="clear"></div>
<p>
	<strong>Tracking URL ID:</strong> <a href="<?= SITE_URL ?>/<?= $app['url'] ?>/ad-tracker/view/<?= $ad['urlId'] ?>" target="_blank">#<?= $ad['urlId'] ?></a><br>
	<strong>Adspace Index #:</strong> <?= $ad_key ?><br>
	<strong>Destination URL:</strong> <a href="<?= $ad['data']['url'] ?>" target="_blank"><?= $ad['data']['url'] ?></a><br>
	<strong>Advertisement Image:</strong>
</p>
<div style="position: relative; width: 350px;">
	<a href="<?= SITE_URL.'/files/ads/'.$ad['data']['image'] ?>" target="_blank"><img src="<?= SITE_URL.'/files/ads/'.$ad['data']['image'] ?>" alt="" /></a>
</div>
<div class="clear"></div>
<br>
<div class="adspace-ad-form">
	<h4>Edit Settings</h4>
	<?= $form->display() ?>
</div>
<link rel="stylesheet" type="text/css" href="<?= THEME_URL ?>/css/jquery.datetimepicker.css"/ >
<script src="<?= THEME_URL ?>/js/jquery.datetimepicker.js"></script>
<script type="text/javascript">

	$(document).ready(function(){
		$('.datetimepicker').datetimepicker();
		
	});
</script>
