<h2>XCP Asset Cache</h2>
<?php
if($perms['canViewAllAssets']){
?>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add">Add Asset</a>
</p>
<?php
}//endif
if(count($assetList) == 0){
	echo '<p>No assets available in cache</p>';
}
else{
	$table = $this->generateTable($assetList, array('fields' => array('asset' => 'Asset'),
													 'actions' => array(array('text' => 'Edit Details',
																	'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/', 'data' => 'asset',
																	'heading' => ''))));
	echo $table->display();
}

?>
