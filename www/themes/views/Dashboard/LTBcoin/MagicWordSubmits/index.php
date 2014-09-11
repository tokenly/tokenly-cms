<h2>Magic Word Submissions</h2>
<p>
	Below is an archive of all user submitted "magic words" in the system.
</p>
<?php
if(count($words) == 0){
	echo '<p>No submissions yet!</p>';
}
else{
	echo '<p><strong>Total Submissions:</strong> '.count($words).'</p>';
	$table = $this->generateTable($words, array('class' => 'admin-table mobile-table', 'page' => 50, 'pageUrl' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'?page=',
												'fields' => array('username' => 'Username', 'word' => 'Magic Word', 'itemName' => 'Post Name', 'itemType' => 'Post Type',
																  'submitDate' => 'Date Submitted'),
												'actions' => array(array('text' => 'Delete', 'class' => 'delete', 'data' => 'submitId', 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/', 'heading' => '')),
												'options' => array(array('field' => 'submitDate', 'params' => array('functionWrap' => 'formatDate')))));
												
	echo $table->display();
	
}

?>
