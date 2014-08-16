<h2>Edit Report #<?= $report['reportId'] ?></h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/view/<?= $report['reportId'] ?>">Go Back</a>
</p>
<?php
if(isset($message) AND trim($message) != ''){
	echo '<p class="error">'.$message.'</p>';
}
?>
<?= $form->display() ?>
