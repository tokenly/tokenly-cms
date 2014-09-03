<?php
$table = $this->generateTable($proxyList, array('fields' => array('proxyId' => 'ID', 'url' =>'URL',
																'slug' => 'Slug'),
												'class' => 'admin-table mobile-table',
												'actions' => array( array('text' => 'Edit',
																		 'data' => 'proxyId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'),																		 
																		array('text' => 'Delete', 'class' => 'delete',
																		 'data' => 'proxyId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/')
																	)));

?>
<h2>RSS Feed Proxy URLs</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>">Go Back</a>
</p>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add">Add Proxy URL</a>
</p>
<?php
if(count($proxyList) == 0){
	echo '<p>No proxies added</p>';
}
else{
	 echo $table->display();
	 
}
?>
