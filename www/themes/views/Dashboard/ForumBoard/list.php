<?php
$table = $this->generateTable($boardList, array('fields' => array('boardId' => 'ID', 'name' =>'Name',
																'slug' => 'URL', 'category' => 'Category',
																'active' => 'Active'),
												'class' => 'admin-table mobile-table',
												'actions' => array( array('text' => 'Edit',
																		 'data' => 'boardId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'),																		 
																		array('text' => 'Delete', 'class' => 'delete',
																		 'data' => 'boardId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/')
																	),
												'options' => array(array('field' => 'active', 'params' => array('functionWrap' => 'boolToText')))));

?>
<h2>Forum Boards</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>">Go Back</a>
</p>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add">Add Board</a>
</p>

<?php
if(count($boardList) == 0){
	echo '<p>No boards added</p>';
}
else{
	 echo $table->display();
	 
}
?>
