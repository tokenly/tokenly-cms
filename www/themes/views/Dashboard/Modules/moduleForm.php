<h2><?= $formType ?> <?= $getApp['name'] ?> Module</h2>
<p>

	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/view/<?= $getApp['appId'] ?>">Go Back</a>
</p>
<?php
if(isset($error) AND $error != null){
	echo '<p class="error">'.$error.'</p>';
}
?>
<?= $form->display() ?>
