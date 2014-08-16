<h2>Edit Address</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>">Go Back</a>
</p>
<h3><?= $address['address'] ?></h3>
<?php
if(isset($message) AND trim($message) != ''){
	echo '<p class="error">'.$message.'</p>';
}
?>
<?= $form->display() ?>
