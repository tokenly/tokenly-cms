<?php
$table = $this->generateTable($moduleList, array('fields' => array('moduleId' => 'ID', 'name' =>'Name',
																'slug' => 'Slug', 'url' => 'URL', 'active' => 'Active'),
												'class' => 'admin-table mobile-table',
												'actions' => array( array('text' => 'Edit',
																		 'data' => 'moduleId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit-module/'),																		 
																		array('text' => 'Delete', 'class' => 'delete',
																		 'data' => 'moduleId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete-module/')
																	),
												'options' => array(array('field' => 'active', 'params' => array('functionWrap' => 'boolToText')))					
												));

?>
<h2><?= $getApp['name'] ?> Modules</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>">Go Back</a>
</p>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add-module/<?= $getApp['appId'] ?>">Add <?= $getApp['name'] ?> Module</a>
</p>
<?php
if(count($moduleList) == 0){
	echo '<p>No modules installed</p>';
}
else{
	 echo $table->display();
	 
}
?>
