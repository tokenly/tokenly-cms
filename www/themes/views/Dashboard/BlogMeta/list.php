<?php
$table = $this->generateTable($fieldList, array('fields' => array('metaTypeId' => 'ID', 'label' =>'Label', 'slug' => 'Slug',
																'active' => 'Active'),
												'class' => 'admin-table mobile-table',
												'actions' => array( array('text' => 'Edit',
																		 'data' => 'metaTypeId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'),																		 
																		array('text' => 'Delete', 'class' => 'delete',
																		 'data' => 'metaTypeId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/')
																	),
												'options' => array(array('field' => 'active', 'params' => array('functionWrap' => 'boolToText'))
																   )
												));

?>
<h2>Blog Post Metadata Types</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>">Go Back</a>
</p>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add">Add New Type</a>
</p>
<?php
if(count($fieldList) == 0){
	echo '<p>No types added</p>';
}
else{
	 echo $table->display();
	 
}
?>
