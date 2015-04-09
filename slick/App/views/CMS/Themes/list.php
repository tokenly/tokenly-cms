<?php
$table = $this->generateTable($themeList, array('fields' => array('themeId' => 'ID', 'name' =>'Name',
																'location' => 'Dir Location', 'active' => 'Enabled'),
												'class' => 'admin-table mobile-table',
												'actions' => array( array('text' => 'Edit',
																		 'data' => 'themeId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'),																		 
																		array('text' => 'Delete', 'class' => 'delete',
																		 'data' => 'themeId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/')
																	),
												'options' => array(array('field' => 'active', 'params' => array('functionWrap' => 'boolToText')))
												));

?>
<h2>Themes</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>">Go Back</a>
</p>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add">Add Theme</a>
</p>
<p>
	Install &amp; enable site/sub-site themes here. Make sure the "Dir Location" matches the correct directory in the themes folder.
	Themes are shared across all sub-sites. To change the theme on a sub-site, go to that sites' theme dashboard and enable desired theme.
</p>
<?php
if(count($themeList) == 0){
	echo '<p>No themes added</p>';
}
else{
	 echo $table->display();
	 
}
?>
