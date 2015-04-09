<?php
foreach($blogList as $key => $row){
	if(!$perms['canManageAllBlogs'] AND 
		($row['userId'] != $user['userId'] AND (!isset($row['roles']['admin']) OR (isset($row['roles']['admin']) AND !in_array($user['userId'], $row['roles']['admin']))))){
			unset($blogList[$key]);
			continue;
	}
}

$table = $this->generateTable($blogList, array('fields' => array('blogId' => 'ID', 'name' =>'Blog Name',
																'active' => 'Active'),
												'class' => 'admin-table mobile-table data-table submissions-table',
												'actions' => array( array('text' => 'Edit',
																		 'data' => 'blogId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'),																		 
																		array('text' => 'Delete', 'class' => 'delete delete-blog',
																		 'data' => 'blogId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/')
																	),
												'options' => array(array('field' => 'active', 'params' => array('functionWrap' => 'boolToText')))));

?>
<div class="pull-right blog-submit-actions">
	<?php
	if($perms['canCreateBlogs']){
	?>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add" class="btn btn-large">Create a Blog</a>
	<?php
	}//endif
	?>
</div>
<h2>My Blogs</h2>
<hr>
<?=  $this->displayFlash('blog-message') ?>
<?php
if(count($blogList) == 0){
	echo '<p>No blogs created</p>';
}
else{
	echo '<table class="admin-table mobile-table data-table submissions-table">
			<thead>
				<tr>
					<th>ID</th>
					<th>Blog Name</th>
					<th>Active</th>
					<th class="no-sort"></th>
				</tr>
			</thead>
			<tbody>';
	foreach($blogList as $blog){
		$titleLink = $blog['name'];
		$actionLinks = '';
	
		$editLink = '';
		$deleteLink = '';
		$viewLink = '';

		$editLink = '<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'.$blog['blogId'].'" class="">Edit</a>';
		$titleLink = '<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'.$blog['blogId'].'" class="">'.$blog['name'].'</a>';
		
		if(($user['userId'] == $blog['userId'])
			OR ($user['userId'] != $blog['userId'] AND $perms['canManageAllBlogs'])){
				$deleteLink = '<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/'.$blog['blogId'].'" class="delete delete-blog">Delete</a>';
			
		}
		
		$actionLinks = $editLink.' '.$deleteLink;
		
		echo '<tr>';
		echo '<td>'.$blog['blogId'].'</td>
			  <td class="post-title">'.$titleLink.'</td>
			  <td>'.boolToText($blog['active']).'</td>
			  <td class="table-actions">
				'.$actionLinks.'
			  </td>';
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
		});		
		
		$('.delete-blog').click(function(e){
			var check = prompt('Are you sure? Type in the name of the blog to permanently delete.');
			var board = $(this).parent().parent().find('td').eq(1).find('a').html();
			
			if(check == false || check == null || check != board){
				e.preventDefault();
				return false;
			}
			
		});
		
	});
</script>
