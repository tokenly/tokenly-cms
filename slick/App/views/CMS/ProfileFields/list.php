<?php
$table = $this->generateTable($fieldList, array('fields' => array('fieldId' => 'ID', 'label' =>'Label',
																'active' => 'Active', 'public' => 'Public'),
												'class' => 'admin-table mobile-table',
												'actions' => array( array('text' => 'Edit',
																		 'data' => 'fieldId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'),																		 
																		array('text' => 'Delete', 'class' => 'delete',
																		 'data' => 'fieldId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/')
																	),
												'options' => array(array('field' => 'active', 'params' => array('functionWrap' => 'boolToText')),
																   array('field' => 'public', 'params' => array('functionWrap' => 'boolToText')))
												));

?>
<h2>User Profile Fields</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>">Go Back</a>
</p>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add">Add New Field</a>
</p>
<?php
if(count($fieldList) == 0){
	echo '<p>No fields added</p>';
}
else{
	 echo $table->display();
	 
}
?>
