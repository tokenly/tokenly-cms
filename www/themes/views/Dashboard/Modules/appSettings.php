<h2>Manage App Settings - <?= $thisApp['name'] ?></h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>">Go Back</a>
</p>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/settings/<?= $thisApp['appId'] ?>/add">Add New Setting</a>
</p>
<?php
if(count($appSettings) == 0){
	echo 'No settings found';
}
else{

$table = $this->generateTable($appSettings, array('fields' => array('label' => 'Name', 'metaKey' => 'Key', 'type' => 'Type'),
												  'class' => 'admin-table mobile-table',
												  'actions' => array(array('text' => 'Show Value', 'data' => 'appMetaId',
																			'heading' => '', 'class' => 'fancy', 'url' => '#'),
																	 array('text' => 'Edit', 'data' => 'appMetaId',
																			'heading' => '', 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/settings/'.$thisApp['appId'].'/edit/'
																			),		
																	  array('text' => 'Delete', 'data' => 'appMetaId', 'class' => 'delete',
																			'heading' => '', 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/settings/'.$thisApp['appId'].'/delete/'
																			)		
																			)));
																			
																			
echo $table->display();

foreach($appSettings as $appSetting){
	echo '<div id="'.$appSetting['appMetaId'].'" style="display: none;">'.$appSetting['metaValue'].'</div>';
}
}																		
?>
