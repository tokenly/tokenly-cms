<h2><?= $formType ?> Category</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>">Go Back</a>
</p>
<?php
if(isset($category)){
	if($category['image'] != '' AND file_exists(SITE_PATH.'/files/blogs/'.$category['image'])){
		echo '<p style="float: right; width: 150px; position: relative;"><img src="'.SITE_URL.'/files/blogs/'.$category['image'].'" style="max-width: 100%;" alt="" /></p>';
	}
	
}
if(isset($error) AND $error != null){
	echo '<p class="error">'.$error.'</p>';
}
?>
<?= $form->display() ?>
