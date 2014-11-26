<h2><?= $formType ?> Blog Post</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>">Go Back</a>
</p>
<p>
	Note: Post formatting can now be done via <strong>Markdown</strong>. <a href="<?= SITE_URL ?>/markdown-formatting" target="_blank">Click here to view more about markdown</a>.
	Editing in the <a href="http://inkpad.io" target="_blank">Inkpad</a> markdown editor auto saves and allows for collaborative writing, however text is not
	saved in our database until "Save & Submit" is clicked. You may still preview post content without saving first.
</p>
<p>
	Also be aware that WYSiWYG editor content cannot be automatically transfered to the markdown editor (you may do this manually).
</p>
<?php

if(isset($error) AND $error != nullz){
	echo '<p class="error">'.$error.'</p>';
}

if(isset($post)){
	$model = new Slick_Core_Model;
	$getAuthor = $model->get('users', $post['userId']);
	
	$imagePath = SITE_PATH.'/files/blogs';
	if(isset($post['image']) AND trim($post['image']) != '' AND file_exists($imagePath.'/'.$post['image'])){
		echo '<div style="float: right; vertical-align: top; width: 150px;"><strong>Featured Image:</strong><br><img style="max-width: 100%;" src="'.SITE_URL.'/files/blogs/'.$post['image'].'" alt="" /></div>';
		
	}
	if(isset($post['coverImage']) AND trim($post['coverImage']) != '' AND file_exists($imagePath.'/'.$post['coverImage'])){
		echo '<div style="clear: right;float: right; vertical-align: top; width: 150px;"><strong>Cover Image:</strong><br><img style="max-width: 100%;" src="'.SITE_URL.'/files/blogs/'.$post['coverImage'].'" alt="" /></div>';
		
	}
	?>
	<p>
		<a class="view-draft" href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/preview/<?= $post['postId'] ?>" target="_blank">Preview Post as Draft</a>
	</p>
	<p>
		<strong>Author:</strong> <a href="<?= SITE_URL ?>/profile/user/<?= $getAuthor['slug'] ?>" target="_blank"><?= $getAuthor['username'] ?></a>
		<?php
		$editorName = 'No one';
		if($post['editedBy'] != 0){
			$getEditor = $model->get('users', $post['editedBy']);
			$editorName = '<a href="'.SITE_URL.'/profile/user/'.$getEditor['slug'].'" target="_blank">'.$getEditor['username'].'</a>';
		}
		?>
		<br>
		<strong>Editor:</strong> <?= $editorName ?>
	</p>
	<?php
}
?>

<?= $form->display() ?>

<script type="text/javascript">
	$(document).ready(function(){
		<?php
		if(isset($post) AND $post['formatType'] == 'wysiwyg'){
		?>
		$('select[name="formatType"]').change(function(e){
			var thisVal = $(this).val();
			if(thisVal == 'markdown'){
				var check = confirm('Warning: Switching to the markdown editor may erase the current post content + excerpt. Are you sure you want to continue? Save/Submit to complete change.');
				if(check == null || check == false){
					$(this).val('wysiwyg');
					e.preventDefault();
				}
			}
		});
		<?php
		}//endif
		if(isset($post) AND $post['formatType'] == 'markdown'){
		?>
		setInterval(function(){
			
			var url = '<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/checkInkpad/<?= $post['postId'] ?>';
			$.get(url, function(data){
				if(data.error != null){
					console.log(data.error);
					return false;
				}
				if(!data.result.content){
					$('label[for="content"]').html('Content <span class="unsaved">[unsaved]</span>');
				}
				else{
					$('label[for="content"]').html('Content <span class="saved">[saved]</span>');
				}
				
				if(!data.result.excerpt){
					$('label[for="excerpt"]').html('Excerpt <span class="unsaved">[unsaved]</span>');
				}
				else{
					$('label[for="excerpt"]').html('Excerpt <span class="saved">[saved]</span>');
				}				
			});
		}, 10000);
		<?php
		}//endif
		?>
	});
</script>
