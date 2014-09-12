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
	
	if(($user['userId'] == $post['userId'] AND $post['perms']['canEditSelfPost'])
		OR ($user['userId'] != $post['userId'] AND $post['perms']['canEditOtherPost'])){
		if($post['published'] == 0 OR ($post['published'] == 1 AND $post['perms']['canPublishPost'])){
			$editLink = '<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'.$post['postId'].'">View/Edit</a>';
		}
	}
	
	if(($user['userId'] == $post['userId'] AND $post['perms']['canDeleteSelfPost'])
		OR ($user['userId'] != $post['userId'] AND $post['perms']['canDeleteOtherPost'])){
		if($post['published'] == 0 OR ($post['published'] == 1 AND $post['perms']['canPublishPost'])){
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

}
?>
