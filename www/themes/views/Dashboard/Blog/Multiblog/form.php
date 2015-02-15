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
		$table = $this->generateTable($blogRoles, array('fields' => array('username' => 'Username', 'type' => 'Role'),
														'actions' => array(array('data' => 'userId', 'text' => 'Remove',
														 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/remove-role/'.$getBlog['blogId'].'/',
														 'class' => 'delete')),
														 'options' => array(array('field' => 'username', 'params' => array('functionWrap' => 'linkify_username')))));
		echo $table->display();
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
