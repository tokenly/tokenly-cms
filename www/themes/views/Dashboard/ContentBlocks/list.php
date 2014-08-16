<?php
$table = $this->generateTable($blockList, array('fields' => array('blockId' => 'ID', 'name' =>'Name',
																'slug' => 'Slug', 'active' => 'Active'),
												'class' => 'admin-table mobile-table',
												'actions' => array( array('text' => 'Edit',
																		 'data' => 'blockId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'),																		 
																		array('text' => 'Delete', 'class' => 'delete',
																		 'data' => 'blockId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/')
																	),
												'options' => array(array('field' => 'active', 'params' => array('functionWrap' => 'boolToText')))
												));

?>
<h2>Content Blocks</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>">Go Back</a>
</p>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add">Add Block</a>
</p>
<p>
	Content blocks can be used throughout the current website theme to display manageable content
	on areas outside of the typical content areas. Example: ad banner spot displayed on blog template. 
	Or contact info in the website footer.
</p>
<?php
if(count($blockList) == 0){
	echo '<p>No blocks added</p>';
}
else{
	 echo $table->display();
	 
}
?>
