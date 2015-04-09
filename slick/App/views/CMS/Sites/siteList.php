<?php
$table = $this->generateTable($siteList, array('fields' => array('siteId' => 'ID', 'name' =>'Name',
																'domain' => 'Domain', 'url' => 'URL', 'isDefault' => 'Default'),
												'class' => 'admin-table mobile-table',
												'actions' => array( array('text' => 'Edit',
																		 'data' => 'siteId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'),																		 
																		array('text' => 'Delete', 'class' => 'delete',
																		 'data' => 'siteId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/')
																	),
												'options' => array(array('field' => 'isDefault', 'params' => array('functionWrap' => 'boolToText')))
												));

?>
<h2>Sites</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>">Go Back</a>
</p>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add">Add Sub-Site</a>
</p>
<p>
	<em>Advanced use only</em>. Create new sub-site configuration here. Domain must match the HTTP host
	of the sub site, which can either be a sub domain or its own domain. Once added, individual apps
	can be enabled for each site.
</p>
<?php
if(count($siteList) == 0){
	echo '<p>No Sites added</p>';
}
else{
	 echo $table->display();
	 
}
?>
