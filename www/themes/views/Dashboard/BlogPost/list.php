<?php

foreach($postList as $key => $val){
	if($val['published'] == 1){
		$postList[$key]['status'] = 'Published';
		$postList[$key]['postDate'] = $val['publishDate'];
	}
	elseif($val['ready'] == 1){
		$postList[$key]['status'] = 'Ready';
	}
	elseif($val['status'] == 'editing'){
		$postList[$key]['status'] = 'Editing';
	}
	else{
		$postList[$key]['status'] = 'Draft';
	}
}

$table = $this->generateTable($postList, array('fields' => array('postId' => 'ID', 'title' =>'Title',
																'author' => 'Author', 'status' => 'Status',
																'postDate' => 'Posted Date/Time'),
												'class' => 'admin-table mobile-table',
												'actions' => array( array('text' => 'Edit',
																		 'data' => 'postId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'),																		 
																		array('text' => 'Delete', 'class' => 'delete',
																		 'data' => 'postId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/')
																	),
												'options' => array(array('field' => 'published', 'params' => array('functionWrap' => 'boolToText')),
															array('field' => 'publishDate', 'params' => array('functionWrap' => 'formatDate')),
															array('field' => 'postDate', 'params' => array('functionWrap' => 'formatDate'))
															)));

?>
<h2>Blog Posts</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>">Go Back</a>
</p>
<?php
if($perms['canWritePost']){
?>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add">Add Post</a>
</p>
<?php
}//endif
?>
<?php
if(count($postList) == 0){
	echo '<p>No posts added</p>';
}
else{
	
echo '<table class="admin-table mobile-table">
		<thead>
			<tr>
				<th>ID</th>
				<th>Title</th>
				<th>Author</th>
				<th>Status</th>
				<th>Posted Date</th>
				<th></th>
				<th></th>
			</tr>
		</thead>
		<tbody>';
foreach($postList as $post){
	$editLink = '';
	$deleteLink = '';
	
	if(($user['userId'] == $post['userId'] AND $perms['canEditSelfPost'])
		OR ($user['userId'] != $post['userId'] AND $perms['canEditOtherPost'])){
		if($post['published'] == 0 OR ($post['published'] == 1 AND $perms['canPublishPost'])){
			$editLink = '<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'.$post['postId'].'">View/Edit</a>';
		}
	}
	
	if(($user['userId'] == $post['userId'] AND $perms['canDeleteSelfPost'])
		OR ($user['userId'] != $post['userId'] AND $perms['canDeleteOtherPost'])){
		if($post['published'] == 0 OR ($post['published'] == 1 AND $perms['canPublishPost'])){
			$deleteLink = '<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/'.$post['postId'].'" class="delete">Delete</a>';
		}
	}
	
	
	echo '<tr>';
	echo '<td>'.$post['postId'].'</td>
		  <td>'.$post['title'].'</td>
		  <td>'.$post['author'].'</td>
		  <td>'.$post['status'].'</td>
		  <td>'.formatDate($post['postDate']).'</td>
		  <td>'.$editLink.'</td>
		  <td>'.$deleteLink.'</td>';
	echo '</tr>';
	
}
echo '</tbody></table>';

	// echo $table->display();
	 
}
?>
