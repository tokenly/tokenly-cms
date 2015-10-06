<?php
$table = $this->generateTable($pageList, array('fields' => array('pageId' => 'ID', 'name' =>'Name',
																'url' => 'URL', 'active' => 'Active'),
												'class' => 'admin-table mobile-table',
												'actions' => array( array('text' => 'Edit',
																		 'data' => 'pageId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'),																		 
																		array('text' => 'Delete', 'class' => 'delete',
																		 'data' => 'pageId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/')
																	),
												'options' => array(array('field' => 'active', 'params' => array('functionWrap' => 'boolToText')),
																   array('field' => 'url', 'params' => array('preText' => '<a href="'.SITE_URL.'/', 'postText' => '" target="_blank">View page</a>'))
												)));

?>
<h2>Pages</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>">Go Back</a>
</p>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add">Add Page</a>
</p>

<?php
if(count($pageList) == 0){
	echo '<p>No pages added</p>';
}
else{
	 echo $table->display();
	 
}
?>
