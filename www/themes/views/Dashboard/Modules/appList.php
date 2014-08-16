<?php
$table = $this->generateTable($appList, array('fields' => array('appId' => 'ID', 'name' =>'Name',
																 'url' => 'URL', 'active' => 'Active'),
												'class' => 'admin-table mobile-table',
												'actions' => array(array('text' => 'View Modules',
																		 'data' => 'appId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/view/'),
																		 array('text' => 'Manage Settings', 'heading' => '', 'data' => 'appId',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/settings/'),
																		array('text' => 'Permission Keys', 'heading' => '', 'data' => 'appId',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/perms/'),																		 
																		  array('text' => 'Edit',
																		 'data' => 'appId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit-app/'),																		 
																		array('text' => 'Delete', 'class' => 'delete',
																		 'data' => 'appId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete-app/')
																	),
												'options' => array(array('field' => 'active', 'params' => array('functionWrap' => 'boolToText')))
												));

?>
<h2>Apps</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>">Go Back</a>
</p>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add-app">Add App</a>
</p>
<p>
	<em>Advanced use only</em>. Create a new app configuration and add or edit/enable/disable specific 
	modules for each app. Apps/Modules are the core behind majority of features in the system.
</p>
<?php
if(count($appList) == 0){
	echo '<p>No apps installed</p>';
}
else{
	 echo $table->display();
	 
}
?>
