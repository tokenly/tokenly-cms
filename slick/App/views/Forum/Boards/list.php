
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
	 ?>
     <table class="admin-table mobile-table table table-striped table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Category</th>
                <th>Active</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach($boardList as $board){
            ?>
            <tr>
                <td><strong><?= $board['name'] ?></strong></td>
                <td><?= $board['category'] ?></td>
                <td><?= boolToColorText($board['active']) ?></td>
                <td class="table-actions">
                    <?php
                    echo '<a href="'.SITE_URL.'/forum/board/'.$board['slug'].'" class="btn btn-sm btn-small" target="_blank">View</a>';
                    echo '<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'.$board['boardId'].'" class="btn btn-sm btn-small">Edit</a>';
                    echo '<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/'.$board['boardId'].'" class="btn btn-sm btn-small delete delete-board">Delete</a>';
                    ?>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
     </table>
     
     <?php
	 
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
