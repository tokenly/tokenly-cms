<?php
$table = $this->generateTable($commentList, array('fields' => array('commentId' => 'ID', 
																'author' => 'Author', 'postTitle' => 'Post', 
																'message' => 'Comment',
																'commentDate' => 'Date/Time'),
												'class' => 'admin-table mobile-table',
												'actions' => array(array('text' => 'View Article/Comment',
																		 'data' => 'postURL', 'heading' => '',
																		 'url' => SITE_URL.'/blog/post/', 'target' => '_blank'),
																		array('text' => 'Delete', 'class' => 'delete',
																		 'data' => 'commentId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/')																		 
																	),
												'options' => array(
															array('field' => 'commentDate', 'params' => array('functionWrap' => 'formatDate')),
															array('field' => 'message', 'params' => array('functionWrap' => 'shorten'))
															)));

?>
<h2>Blog Comments</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>">Go Back</a>
</p>
<?php
if(count($commentList) == 0){
	echo '<p>No comments made</p>';
}
else{
	 echo $table->display();
	 
}
?>
