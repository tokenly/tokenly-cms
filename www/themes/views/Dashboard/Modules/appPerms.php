<h2>Manage App Permission Keys - <?= $thisApp['name'] ?></h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>">Go Back</a>
</p>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/perms/<?= $thisApp['appId'] ?>/add">Add New Setting</a>
</p>
<?php
if(count($appPerms) == 0){
	echo 'No permission keys found';
}
else{

$table = $this->generateTable($appPerms, array('fields' => array('permKey' => 'Key'),
												  'class' => 'admin-table mobile-table',
												  'actions' => array(
																	 array('text' => 'Edit', 'data' => 'permId',
																			'heading' => '', 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/perms/'.$thisApp['appId'].'/edit/'
																			),		
																	  array('text' => 'Delete', 'data' => 'permId', 'class' => 'delete',
																			'heading' => '', 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/perms/'.$thisApp['appId'].'/delete/'
																			)		
																			)));
																			
																			
echo $table->display();

}																		
?>
