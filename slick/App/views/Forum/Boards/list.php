<?php
$table = $this->generateTable($boardList, array('fields' => array('boardId' => 'ID', 'name' =>'Name',
																'slug' => 'URL', 'category' => 'Category',
																'active' => 'Active'),
												'class' => 'admin-table mobile-table',
												'actions' => array( array('text' => 'Edit',
																		 'data' => 'boardId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'),																		 
																		array('text' => 'Delete', 'class' => 'delete delete-board',
																		 'data' => 'boardId', 'heading' => '',
																		 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/')
																	),
												'options' => array(array('field' => 'active', 'params' => array('functionWrap' => 'boolToColorText')))));

?>
<h2>Forum Board Management</h2>
<?= $this->displayFlash('message') ?>
<?php
if($perms['canManageAllBoards']){
?>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add" class="btn">Add Board</a>
</p>

<?php
}//endif
if(count($boardList) == 0){
	echo '<p>No boards added</p>';
}
else{
     echo '<p><strong># Boards:</strong> '.count($boardList).'</p>';
	 echo $table->display();
	 
}
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('.delete-board').click(function(e){
			var check = prompt('Are you sure? Type in the name of the board to permanently delete. Please contact an admin first if you want posts in this board to be moved/archived');
			var board = $(this).parent().parent().find('td').eq(1).html();
			
			if(check == false || check == null || check != board){
				e.preventDefault();
				return false;
			}
			
		});
		
	});
</script>
