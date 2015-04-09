<h2><?= $formType ?> Site</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>">Go Back</a>
</p>
<?php
if(isset($thisSite)){
	$imagePath = SITE_PATH.'/files/sites';
	if(isset($thisSite['image']) AND trim($thisSite['image']) != '' AND file_exists($imagePath.'/'.$thisSite['image'])){
		echo '<div style="float: right; vertical-align: top; width: 150px;"><img style="max-width: 100%;" src="'.SITE_URL.'/files/sites/'.$thisSite['image'].'" alt="" /></div>';
		
	}
}

if(isset($error) AND $error != null){
	echo '<p class="error">'.$error.'</p>';
}
?>
<?= $form->display() ?>
