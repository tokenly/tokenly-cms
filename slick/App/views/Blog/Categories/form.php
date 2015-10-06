<h2><?= $formType ?> Category</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>">Go Back</a>
</p>
<hr>
<?php
if(isset($category)){
	echo '<p class="pull-right">
			<a href="'.SITE_URL.'/blog/'.$category['blog']['slug'].'/'.$category['slug'].'" target="_blank" class="btn">View Category</a>
		</p>';	
	if($category['image'] != '' AND file_exists(SITE_PATH.'/files/blogs/'.$category['image'])){
		echo '<div class="pull-right"><p><img src="'.SITE_URL.'/files/blogs/'.$category['image'].'" style="max-width: 150px;" alt="" /></p></div>';
	}
	echo '<p>
		<strong>Blog: </strong>'.$category['blog']['name'].'	
	</p>';
}
if(isset($error) AND $error != null){
	echo '<p class="error">'.$error.'</p>';
}
?>
<?= $form->display() ?>
<?php
if(!isset($category)){
	?>
	<script type="text/javascript">
		$(document).ready(function(e){
			$('select[name="blogId"]').change(function(e){
				var blogId = $(this).val();
				$('select[name="parentId"]').find('option').each(function(){
					var optBlog = $(this).data('blog');
					if(optBlog != blogId && $(this).val() != 0){
						$(this).attr('hidden', 'hidden');
					}
					else{
						$(this).removeAttr('hidden');
					}
				});
			});	
		});
	</script>
	<?php
}
?>
