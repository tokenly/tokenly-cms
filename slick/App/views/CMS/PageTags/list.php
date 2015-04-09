<?php
$table = $this->generateTable($tagList, array('fields' => array('tagId' => 'ID', 'tag' =>'Tag',
																'class' => 'Class'),
												'class' => 'admin-table mobile-table',
												'actions' => array( array('text' => 'Edit',
																		 'data' => 'tagId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'),																		 
																		array('text' => 'Delete', 'class' => 'delete',
																		 'data' => 'tagId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/')
																	)));

?>
<h2>Page Tags</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>">Go Back</a>
</p>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add">Add Page Tag</a>
</p>
<p>
	<em>Advanced use only</em>. Page tags allow you to load PHP class files and drop their outputs into 
	pages or content blocks, simply by typing something like [MY_TAG]. Example: Photo gallery, or an advanced contact form
</p>
<?php
if(count($tagList) == 0){
	echo '<p>No tags added</p>';
}
else{
	 echo $table->display();
	 
}
?>
