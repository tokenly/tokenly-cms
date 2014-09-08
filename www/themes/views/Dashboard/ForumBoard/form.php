<h2><?= $formType ?> Board</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>">Go Back</a>
</p>
<?php
if(isset($error) AND $error != null){
	echo '<p class="error">'.$error.'</p>';
}
?>
<?= $form->display() ?>

<?php
if(isset($boardMods)){
	echo '<h3>Board Moderators</h3>';
	if(count($boardMods) == 0){
		echo '<p>No moderators added yet</p>';
	}
	else{
		$table = $this->generateTable($boardMods, array('fields' => array('username' => 'Username'),
														'actions' => array(array('data' => 'userId', 'text' => 'Remove',
														 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/remove-mod/'.$getBoard['boardId'].'/',
														 'class' => 'delete'))));
		echo $table->display();
	}
	echo '<br>';
	echo $modForm->display();	
}
?>
