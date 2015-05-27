<h2><?= $formType ?> Blog
<?php
if($formType == 'Edit'){
	echo ' - '.$getBlog['name'];
}
?>
</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>">Go Back</a>
</p>
<hr>
<?=  $this->displayFlash('blog-message') ?>
<?php
if(isset($error) AND $error != null){
	echo '<p class="error">'.$error.'</p>';
}
?>
<?php
if(isset($blogRoles)){
	
	?>
	<ul class="ltb-stat-tabs blog-tabs" data-tab-type="blog-form">
		<li><a href="#" class="tab active" data-tab="blog-info">Blog Info</a></li>
		<li><a href="#" class="tab" data-tab="blog-roles">User Roles</a></li>
		<li><a href="#" class="tab" data-tab="blog-settings">Settings</a></li>
	</ul>
	<div class="clear"></div>	
	<div class="blog-form">
	<div class="ltb-data-tab" id="blog-info" style="">
		<?php
		if($getBlog['image'] != '' AND file_exists(SITE_PATH.'/files/blogs/'.$getBlog['image'])){
			echo '<div class="pull-right"><p><img src="'.SITE_URL.'/files/blogs/'.$getBlog['image'].'" style="max-width: 150px;" alt="" /></p></div>';
		}	
		?>
		<?= $form->display() ?>
	</div>
	<div class="ltb-data-tab" id="blog-roles" style="display: none;">
	<?php
	echo '<h3>Blog Roles - Autonomous Content Team</h3>';
	if(count($blogRoles) == 0){
		echo '<p>No roles added yet</p>';
	}
	else{
		echo '<table class="admin-table">
				<thead>
					<tr>
						<th></th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tbody>';
		
		foreach($blogRoles as $role){
			?>
			<tr>
				<td>
					<?php
					if($role['userId'] == 0 AND $role['token'] != ''){
						echo '<strong>'.$role['token'].'</strong>';
					}
					else{
						echo linkify_username($role['username']);
						if($role['token'] != ''){
							echo ' ['.$role['token'].']';
						}
					}
					?>
				
				</td>
				<td><?= $role['type'] ?></td>
				<td>
					<?php
					if($role['userId'] == 0 OR $role['token'] == ''){
						echo '<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/remove-role/'.$getBlog['blogId'].'/'.$role['userRoleId'].'" class="delete">Remove</a>';
					}
					?>
				</td>
			</tr>
			<?php
		}
		
		echo '</tbody></table>';
	}
	echo '<br>';
	echo $roleForm->display();	
	?>
	<p><strong>Description of available Blog Roles:</strong></p>
	<ul>
		<li><strong>Writer:</strong> Can post submissions in any category within this blog, including non-public categories.</li>
		<li><strong>Independent Writer:</strong> Same permissions as regular writer, but can self-publish to any category, bypassing editorial queue.</li>
		<li><strong>Editor:</strong> Can see submissions for this blog in the editorial queue, permissions to edit and publish posts.</li>
		<li><strong>Admin:</strong> Has access to manage blog title, description, image and user roles, as well as manage available categories (but cannot delete the blog).</li>
	</ul>
	</div>
	<div class="ltb-data-tab" id="blog-settings" style="display: none;">
		<h3>Blog Settings</h3>
		<?php
		$settingForm->setSubmitText('Update Settings');
		echo $settingForm->display();
		?>
	</div>
	<div class="clear"></div>
	</div>
	
<script type="text/javascript">

	$(document).ready(function(){
		$('.ltb-stat-tabs').find('.tab').click(function(e){
			e.preventDefault();
			var tab = $(this).data('tab');
			var type = $(this).parent().parent().data('tab-type');
			$('.' + type).find('.ltb-data-tab').hide();
			$('.' + type).find('.ltb-data-tab#' + tab).show();
			$(this).parent().parent().find('.tab').removeClass('active');
			$(this).addClass('active');
		});
		
	});
	</script>
	<?php
}
else{
?>
<?= $form->display() ?>
<?php
}
?>
