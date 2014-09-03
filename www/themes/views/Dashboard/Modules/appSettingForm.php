<h2><?= $formTitle ?> - <?= $thisApp['name'] ?></h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/settings/<?= $thisApp['appId'] ?>">Go Back</a>
</p>
<?php
if($error != ''){
	echo '<p class="error">'.$error.'</p>';
}
?>
					
<?= $form->display() ?>
