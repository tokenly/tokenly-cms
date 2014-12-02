<h2><?= $formType ?> Article</h2>
<?php
if(isset($post)){
	echo '<h3>'.$post['title'].'</h3>';
	
	$oldPreview = '';
	if($old_version AND $old_version['versionId'] != $post['version']){
		$oldPreview = '/'.$old_version['num'];
	}
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
	<li><a class="view-draft" href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/preview/<?= $post['postId'] ?><?= $oldPreview ?>" target="_blank">Preview Draft</a></li>
	<?php
	}
	?>
</ul>
<div class="clear"></div>
<div class="blog-form">
	<?= $form->open() ?>
	<?=  $this->displayFlash('blog-message') ?>
	<div class="ltb-data-tab" id="blog-content" style="">
		<?php
		$excpStyle = 'display: none;';
		if(isset($post) AND trim($post['excerpt']) != ''){
			$generateExcerpt = shortenMsg(strip_tags($post['content']), 500);
			if($generateExcerpt != $post['excerpt']){
				$excpStyle = '';
				$form->field('autogen-excerpt')->setChecked(1);
			}
		}
		if(isset($post)){
			if($old_version AND $old_version['versionId'] != $post['version']){
				echo '<p><strong>Viewing Content Version #'.$old_version['num'].' (save/submit to use this version)</strong></p>';
			}
		}
		?>
		<?= $form->field('title')->display() ?>
		<?= $form->field('formatType')->display() ?>		
		<?= $form->field('content')->display() ?>
		<?= $form->field('autogen-excerpt')->display() ?>
		<div id="excerpt-cont" style="<?= $excpStyle ?>">
			<?= $form->field('excerpt')->display() ?>
		</div>
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
	<?php
	if(isset($post)){
	?>
	<div class="ltb-data-tab" id="discussion" style="display: none;">
		<p>
			Private editorial discussion coming soon!
		</p>
	</div>	
	<div class="ltb-data-tab" id="versions" style="display: none;">
		<p>
			<strong>Note:</strong> Changes to article settings (categories, title, status etc.) are not tracked in versions, only post content, excerpt and formatting.
		</p>
		<p>
			<strong>Current Version:</strong> #<?= $current_version ?>
			<?php
			if($old_version AND $old_version['versionId'] != $post['version']){
				echo ' (viewing #'.$old_version['num'].')';
			}
			?>
		</p>
		<div class="version-compare-cont">
			<label>Compare Version Changes:</label>
			<select name="v1">
				<?php
				foreach($versions as $version){
					$selected = '';
					if(($old_version AND $old_version['num'] == $version['num']) OR (!$old_version AND $version['versionId'] == $post['version'])){
						$selected = 'selected';
					}
					echo '<option value="'.$version['num'].'" '.$selected.'>#'.$version['num'].'</option>';
				}
				?>
			</select>
			<select name="v2">
				<?php
				$compareDisable = '';
				if($current_version == 1){
					echo '<option value="0">[N/A]</option>';
					$compareDisable = 'disabled';
				}
				else{
					$usePrevNum = $current_version - 1;
					if($old_version AND $old_version['num'] > 1){
						$usePrevNum = $old_version['num'] - 1;
					}
					foreach($versions as $version){
						$selected = '';
						if($version['num'] == $usePrevNum){
							$selected = 'selected';
						}
						
						echo '<option value="'.$version['num'].'" '.$selected.'>#'.$version['num'].'</option>';
					}
				}
				?>
			</select>
			<input type="button" id="compare-versions" value="Go" <?= $compareDisable ?> />
		</div>
		<div id="compare-result" style="display: none;"></div>
		<a id="compare-result-trigger" class="fancy" style="display: none;" href="#compare-result"></a>
		<div class="clear"></div>
		<table class="admin-table">
			<thead>
				<th>Version #</th>
				<th>User</th>
				<th>Lines Changed</th>
				<th>Date/Time</th>
				<th></th>
			</thead>
			<tbody>
				<?php
				foreach($versions as $version){
					$versionImg = '';
					if($version['user']['avatar'] != ''){
						$versionImg = '<span class="mini-avatar"><img src="'.SITE_URL.'/files/avatars/'.$version['user']['avatar'].'" alt="" /></span> ';
					}
					$versionNumber = '#'.$version['num'];
					if(($old_version AND $old_version['versionId'] == $version['versionId'])
						OR (!$old_version AND $version['versionId'] == $post['version'])){
						$versionNumber = '<strong>'.$versionNumber.'</strong>';	
						}
					?>
					<tr>
						<td><?= $versionNumber ?></td>
						<td><a href="<?= SITE_URL ?>/profile/user/<?= $version['user']['slug'] ?>" target="_blank"><?= $versionImg ?><?= $version['user']['username'] ?></a></td>
						<td><?= number_format($version['changes']) ?></td>
						<td><?= date('Y/m/d \<\b\r\> H:i', strtotime($version['versionDate'])) ?></td>
						<td class="table-actions">
							<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/edit/<?= $post['postId'] ?>/<?= $version['num'] ?>">View Version</a>
							<?php
							if($version['versionId'] != $post['version']
								AND (
									($post['userId'] == $user['userId'] AND $perms['canDeleteSelfPostVersion'])
									OR
									($post['userId'] != $user['userId'] AND $perms['canDeleteOtherPostVersion'])
									)
							){
							?>
								<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/edit/<?= $post['postId'] ?>/<?= $version['num'] ?>/delete" class="delete">Delete Version</a>
							<?php
							}//endif
							?>
						</td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
	</div>
	<?php
	}//endif
	?>
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
	function capitalize(s)
	{
		return s[0].toUpperCase() + s.slice(1);
	}	
	
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
		$('#autogen-excerpt').click(function(e){
			if($(this).is(':checked')){
				$('#excerpt-cont').slideDown();
			}
			else{
				$('#excerpt-cont').slideUp();
			}
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
		}//endif
		
		if(isset($post) AND $current_version > 1){
		?>
		$('#compare-versions').click(function(e){
			var v1 = $('select[name="v1"]').val();
			var v2 = $('select[name="v2"]').val();
			if(v1 < v2){
				var _v1 =  v1;
				var _v2 = v2;
				v1 = _v2;
				v2 = _v1;
			}
			var url = '<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/compare/<?= $post['postId'] ?>/' + v1 + '/' + v2;
			$.get(url, function(data){
				console.log(data);
				var first = v1;
				var second = v2;
				if(v2 < v1){
					first = v2;
					second = v1;
				}
				var result = '<h3>Version Changes #' + first + ' > #' + second + '</h3>';
				result = result + '<p><strong>Total Lines Changed:</strong> ' + data.num + '</p>';
				$.each(data.lines, function(idx, val){
					var changeLength = 0;
					for(var i in val){
						changeLength++;
					}
					if(changeLength > 0){
						result = result + '<h4>' + capitalize(idx) + '</h4>';
						result = result + '<table class="version-compare-table">';
						result = result + '<thead>';
						result = result + '<tr>';
						result = result + '<th>Line #</th>';
						result = result + '<th>Version #' + v2 + ' (<a href="<?= SITE_URL ?>/profile/user/' + data.v2_user.slug + '" target="_blank">' + data.v2_user.username + '</a>)</th>';
						result = result + '<th></th>';
						result = result + '<th>Version #' + v1 + ' (<a href="<?= SITE_URL ?>/profile/user/' + data.v1_user.slug + '" target="_blank">' + data.v1_user.username + '</a>)</th>';
						result = result + '</tr>';
						result = result + '</thead>';
						result = result + '<tbody>';
						
						$.each(val, function(lineNum, changes){
							lineMod = '<->';
							if(changes.old.trim() == '' && changes.new.trim() != ''){
								lineMod = '+';
							}
							if(changes.new.trim() == '' && changes.old.trim() != ''){
								lineMod = '-';
							}
							
							result = result + '<tr>';
							result = result + '<td class="line-num">' + (parseInt(lineNum) + 1) + '</td>';
							result = result + '<td class="line-change"><pre>' + changes.old + '</pre></td>';
							result = result + '<td class="line-mod">' + lineMod + '</td>';
							result = result + '<td class="line-change"><pre>' + changes.new + '</pre></td>';
							result = result + '</tr>';
						});
						
						result = result + '</tbody>';
						result = result + '</table>';		
					}
				});

				$('#compare-result').html(result);
				$('#compare-result-trigger').click();
				
			});
		});
		<?php
		}
		?>
	});
</script>
