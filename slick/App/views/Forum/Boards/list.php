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
												'options' => array(array('field' => 'active', 'params' => array('functionWrap' => 'boolToText')))));

?>
<h2>Forum Boards</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>">Go Back</a>
</p>
<?php
if($perms['canManageAllBoards']){
?>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add">Add Board</a>
</p>

<?php
}//endif
if(count($boardList) == 0){
	echo '<p>No boards added</p>';
}
else{
	 echo $table->display();
	 
}
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('.delete-board').click(function(e){
			var check = prompt('Are you sure? Type in the name of the board to permanently delete.');
			var board = $(this).parent().parent().find('td').eq(1).html();
			
			if(check == false || check == null || check != board){
				e.preventDefault();
				return false;
			}
			
		});
		
	});
</script>
