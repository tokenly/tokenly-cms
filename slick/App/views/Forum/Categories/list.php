<?php
$table = $this->generateTable($categoryList, array('fields' => array('categoryId' => 'ID', 'name' =>'Name',
																'slug' => 'Slug'),
												'class' => 'admin-table mobile-table',
												'actions' => array( array('text' => 'Edit',
																		 'data' => 'categoryId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'),																		 
																		array('text' => 'Delete', 'class' => 'delete',
																		 'data' => 'categoryId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/')
																	)));

?>
<h2>Forum Categories</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>">Go Back</a>
</p>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add">Add Category</a>
</p>

<?php
if(count($categoryList) == 0){
	echo '<p>No categories added</p>';
}
else{
	 echo $table->display();
	 
}
?>
