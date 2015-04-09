<h2><?= $formType ?> Tracking URL</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>">Go Back</a>
</p>
<?= $this->displayFlash('message') ?>
<?= $form->display() ?>
