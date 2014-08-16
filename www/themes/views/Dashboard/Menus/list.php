<?php
$table = $this->generateTable($menuList, array('fields' => array('menuId' => 'ID', 'name' =>'Name',
																'slug' => 'Slug'),
												'class' => 'admin-table mobile-table',
												'actions' => array( array('text' => 'Edit',
																		 'data' => 'menuId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'),																		 
																		array('text' => 'Delete', 'class' => 'delete',
																		 'data' => 'menuId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/')
																	)));

?>
<h2>Menus</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>">Go Back</a>
</p>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add">Add Menu</a>
</p>
<p>
	Create menus here which can be used in the current site theme. 
</p>
<?php
if(count($menuList) == 0){
	echo '<p>No menus added</p>';
}
else{
	 echo $table->display();
	 
}
?>
