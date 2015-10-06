<?php
$table = $this->generateTable($groupList, array('fields' => array('groupId' => 'ID', 'name' =>'Name',
																'slug' => 'Slug', 'isDefault' => 'Default'),
												'class' => 'admin-table mobile-table',
												'actions' => array( array('text' => 'Edit',
																		 'data' => 'groupId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'),
																   array('text' => 'View Members',
																		 'data' => 'groupId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/members/'),
																		array('text' => 'Delete', 'class' => 'delete',
																		 'data' => 'groupId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/')
																	),
												'options' => array(array('field' => 'isDefault', 'params' => array('functionWrap' => 'boolToText')))
												));

?>
<h2>User Groups</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>">Go Back</a>
</p>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add">Add Group</a>
</p>
<p>
	Groups granted access on a per site and per feature level. Edit a group to change what
	modules across the site members of said group have access to. Not all modules require
	explicit group access (such as basic accounts modules).
</p>
<?php
if(count($groupList) == 0){
	echo '<p>No groups added</p>';
}
else{
	 echo $table->display();
	 
}
?>
