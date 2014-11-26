<h2><?= $formType ?> Article</h2>
<?php
if(isset($post)){
	echo '<h3>'.$post['title'].'</h3>';
}
?>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>">Go Back</a>
</p>
<?= $this->displayBlock('dashboard-blog-submission-form') ?>
<div class="clear"></div>
<?php
if(isset($post) AND $post['published'] == 1){
	$liveLink = SITE_URL.'/'.$blogApp['url'].'/'.$postModule['url'].'/'.$post['url'];
	echo '<p><strong>Live Link:</strong> <a href="'.$liveLink.'" target="_blank">'.$liveLink.'</a></p>';
}
?>
<ul class="ltb-stat-tabs blog-tabs" data-tab-type="blog-form">
	<li><a href="#" class="tab active" data-tab="blog-content">Content</a></li>
	<li><a href="#" class="tab" data-tab="status-cat">Status &amp; Category</a></li>
	<li><a href="#" class="tab" data-tab="meta-data">Meta Data</a></li>
	<?php
	if(isset($post)){
	?>
	<li><a href="#" class="tab" data-tab="discussion">Discussion</a></li>
	<li><a href="#" class="tab" data-tab="versions">Versions</a></li>
	<li><a class="view-draft" href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/preview/<?= $post['postId'] ?>" target="_blank">Preview Draft</a></li>
	<?php
	}
	?>
</ul>
<div class="clear"></div>
<div class="blog-form">
	<?= $form->open() ?>
	<?=  $this->displayFlash('blog-message') ?>
	<div class="ltb-data-tab" id="blog-content" style="">
		<?= $form->field('title')->display() ?>
		<?= $form->field('content')->display() ?>
		<?= $form->field('excerpt')->display() ?>
		<?= $form->field('formatType')->display() ?>
	</div>
	<div class="ltb-data-tab" id="status-cat" style="display: none;">
		<?= $form->field('status')->display() ?>
		<?= $form->field('publishDate')->display() ?>
		<?php
		if($form->field('featured')){
			echo $form->field('featured')->display();
		}
		?>
		<div class="clear"></div>
		<?php
		if(isset($post)){
			$imagePath = SITE_PATH.'/files/blogs';
			if(isset($post['image']) AND trim($post['image']) != '' AND file_exists($imagePath.'/'.$post['image'])){
				echo '<div style="float: right; vertical-align: top; width: 150px;"><strong>Featured Image:</strong><br><img style="max-width: 100%;" src="'.SITE_URL.'/files/blogs/'.$post['image'].'" alt="" /></div>';
				
			}
			if(isset($post['coverImage']) AND trim($post['coverImage']) != '' AND file_exists($imagePath.'/'.$post['coverImage'])){
				echo '<div style="clear: right;float: right; vertical-align: top; width: 150px;"><strong>Cover Image:</strong><br><img style="max-width: 100%;" src="'.SITE_URL.'/files/blogs/'.$post['coverImage'].'" alt="" /></div>';
				
			}
		}
		?>
		<?php
			if($form->field('image')){
				echo $form->field('image')->display();
			}
		?>
		<?= $form->field('coverImage')->display() ?>
		<?= $form->field('categories')->display() ?>
	</div>	
	<div class="ltb-data-tab" id="meta-data" style="display: none;">
		<?php
		if(isset($post)){
		
			$editorName = 'No one';
			if($post['editedBy'] != 0){
				$editorName = '<a href="'.SITE_URL.'/profile/user/'.$post['editor']['slug'].'" target="_blank">'.$post['editor']['username'].'</a>';
			}
					
			?>
			<ul>
				<li><strong>Author:</strong> <a href="<?= SITE_URL ?>/profile/user/<?= $post['author']['slug'] ?>" target="_blank"><?= $post['author']['username'] ?></a></li>
				<li><strong>Editor:</strong> <?= $editorName ?></li>
				<li><strong>Views:</strong> <?= number_format($post['views']) ?></li>
				<li><strong>Comments:</strong> <?= number_format($post['commentCount']) ?></li>
			</ul>			
			<?php
		}

		if($form->field('userId')){
			echo $form->field('userId')->display();
		}
		if($form->field('editedBy')){
			echo $form->field('editedBy')->display();
		}

		foreach($form->fields as $fieldName => $field){
			if(strpos($fieldName, 'meta_') === 0){
				echo $field->display();
			}
		}
		?>
		<?= $form->field('notes')->display() ?>
	</div>	
	<div class="ltb-data-tab" id="discussion" style="display: none;">
		<p>
			Private editorial discussion coming soon!
		</p>
	</div>	
	<div class="ltb-data-tab" id="versions" style="display: none;">
		<p>
			Version history coming soon!
		</p>
	</div>
	<div class="clear"></div>
	<div class="pull-right">
		<?= $form->displaySubmit() ?>
	</div>	
	<?php
	if(!isset($post) AND !$perms['canBypassSubmitFee']){
		echo '<p><em>You have '.number_format($num_credits).' submission '.pluralize('credit', $num_credits, true).'</em></p>';
	}
	?>
	<?= $form->close() ?>
</div>

<link rel="stylesheet" type="text/css" href="<?= THEME_URL ?>/css/jquery.datetimepicker.css"/ >
<script src="<?= THEME_URL ?>/js/jquery.datetimepicker.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#datetimepicker').datetimepicker();
		$('.ltb-stat-tabs').find('.tab').click(function(e){
			e.preventDefault();
			var tab = $(this).data('tab');
			var type = $(this).parent().parent().data('tab-type');
			$('.' + type).find('.ltb-data-tab').hide();
			$('.' + type).find('.ltb-data-tab#' + tab).show();
			$(this).parent().parent().find('.tab').removeClass('active');
			$(this).addClass('active');
		});
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
		}
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
