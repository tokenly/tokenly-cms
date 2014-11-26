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
	
echo '<table class="admin-table data-table submissions-table">
		<thead>
			<tr>
				<th>Title</th>
				<th>Author</th>
				<th>Status</th>
				<th>Publish Date</th>
				<th></th>
			</tr>
		</thead>
		<tbody>';
foreach($postList as $post){
	$editLink = '';
	$deleteLink = '';
	$titleLink = $post['title'];
	if(($user['userId'] == $post['userId'] AND $post['perms']['canEditSelfPost'])
		OR ($user['userId'] != $post['userId'] AND $post['perms']['canEditOtherPost'])){
		if($post['published'] == 0 OR ($post['published'] == 1 AND $post['perms']['canPublishPost'])){
			$editLink = '<a href="'.SITE_URL.'/'.$app['url'].'/submissions/edit/'.$post['postId'].'">View/Edit</a>';
			$titleLink = '<a href="'.SITE_URL.'/'.$app['url'].'/submissions/edit/'.$post['postId'].'">'.$post['title'].'</a>';
		}
	}
	
	if(($user['userId'] == $post['userId'] AND $post['perms']['canDeleteSelfPost'])
		OR ($user['userId'] != $post['userId'] AND $post['perms']['canDeleteOtherPost'])){
		if($post['published'] == 0 OR ($post['published'] == 1 AND $post['perms']['canPublishPost'])){
			$deleteLink = '<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/'.$post['postId'].'" class="delete">Delete</a>';
		}
	}
	
	
	echo '<tr>';
	echo '
		  <td class="post-title">'.$titleLink.'</td>
		  <td>'.$post['author'].'</td>
		  <td>'.$post['status'].'</td>
		  <td>'.date('Y/m/d \<\b\r\> H:i', strtotime($post['publishDate'])).'</td>
		  <td class="table-actions">
			'.$editLink.'
			'.$deleteLink.'
		  </td>
			';
	echo '</tr>';
	
}
echo '</tbody></table>';

}
?>
<link href="//cdn.datatables.net/1.10.3/css/jquery.dataTables.css" rel="stylesheet" />
<script type="text/javascript" src="//cdn.datatables.net/1.10.3/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('.data-table').DataTable({
			searching: true,
			lengthChange: false,
			paging: true,
			iDisplayLength: 20,
			"order": [[ 3, "desc" ]]
		});
	});
</script>
