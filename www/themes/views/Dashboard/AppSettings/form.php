<h2><?= $thisApp['name'] ?> Settings</h2>
<hr>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>">Go Back</a>
</p>
<?php
if(isset($message) AND trim($message) != ''){
	echo '<p class="error">'.$message.'</p>';
}

if(count($appSettings) == 0){
	echo '<p>No settings found.</p>';
}
else{
?>
<?= $form->display() ?>
<?php
}
?>
