<?php
$table = $this->generateTable($itemList, array('fields' => array('label' =>'Label',
																'dashGroup' => 'Dash Group'),
												'class' => 'admin-table mobile-table',
												'actions' => array( array('text' => 'Edit',
																		 'data' => 'itemId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'),																		 
																		array('text' => 'Delete', 'class' => 'delete',
																		 'data' => 'itemId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/')
																	)));

?>
<h2>Dashboard Menu Management</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>">Go Back</a>
</p>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add">Add Item</a>
</p>

<?php
if(count($itemList) == 0){
	echo '<p>No items added</p>';
}
else{
	 echo $table->display();
	 
}
?>
