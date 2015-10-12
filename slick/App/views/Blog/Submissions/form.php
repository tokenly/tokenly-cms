<?php
if(isset($post)){
?>
<div class="pull-right blog-submit-actions">
	<a href="#contributors" class="fancy contrib-trigger btn btn-large">Contributors</a>
	<div id="contributors" style="display: none;">
		<h3>Article Contributors</h3>
		<p>
			Make sure to save any content changes before attemping<br> to invite or change any contributors (page reloads).
		</p>
		<?php
		
		$contribList[] = array('username' => $post['author']['username'], 'slug' => $post['author']['slug'],
							   'role' => 'Author', 'share' => '*');
							   
		$contribList = array_merge($contribList, $contributor_list);
		
		$changeRoles = false;
		$changeShares = false;
		
		if(count($contribList) > 1){
			if($user['userId'] == $post['userId']){
				$changeRoles = true;
			}
			
			if($perms['canManageAllBlogs']){
				$changeRoles = true;
				$changeShares = true;
			}
		}
		
		if($changeRoles OR $changeShares){
			echo '<form action="" method="post">';
		}
		
		echo '<table class="contrib-table">
				<thead>
					<tr>
						<th>Username</th>
						<th>Role</th>
						<th>Reward Share</th>
					</tr>
				</thead>
				<tbody>';
		$authorTotal = 100;
		foreach($contribList as $contrib){
			
			$contribUser = '<a href="'.SITE_URL.'/profile/user/'.$contrib['slug'].'" target="_blank">'.$contrib['username'].'</a>';
			$contribShare = $contrib['share'];
			$contribRole = $contrib['role'];
			
			if($contribShare != '*'){
				$contribShare = convertFloat($contribShare).'%';
				$authorTotal -= $contrib['share']; 
			}
			
			$rowClass = '';
			if(isset($contrib['accepted'])){
				if($contrib['accepted'] == 0){
					$rowClass = 'pending';
					$contribUser .= ' [pending]';
					$delText = 'Cancel Request';
				}
				else{
					$delText = 'Remove Contributor';
				}
				
				if($changeRoles){
					$contribRole = '<input type="text" value="'.$contrib['role'].'" name="role_'.$contrib['contributorId'].'" required />';
				}
				if($changeShares){
					$contribShare = '<input type="text" value="'.convertFloat($contrib['share']).'" name="share_'.$contrib['contributorId'].'" required />';
				}				
				
				if(($user['userId'] == $post['userId'] AND $contrib['accepted'] == 0)
				   OR $perms['canManageAllBlogs']
				   OR ($user['userId'] == $contrib['userId'])){
					$contribShare .= ' <a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'.$post['postId'].'/contributors/delete/'.$contrib['contributorId'].'" class="delete" title="'.$delText.'"><i class="fa fa-times-circle"></i></a>';
				}
			}
			echo '<tr class="'.$rowClass.'">
					<td>'.$contribUser.'</td>
					<td>'.$contribRole.'</td>
					<td>'.$contribShare.'</td>
				  </tr>';
		}
		
		echo '</tbody></table>';
		
		if($changeRoles OR $changeShares){
			echo '<input type="hidden" name="update-contribs" value="1" />';
			echo '<input type="submit" value="Update Contributors" /></form>';
		}
		
		echo '<p><strong>Author Reward Share:</strong> '.convertFloat($authorTotal).'%</p>';
		echo '<p><small>* original author receives whatever reward share percentage is left over.</small></p>';
		
		if($post['status'] == 'published'){
			echo '<hr><p><em>Article finished, closed to new participants.</em></p>';
		}
		else{
			if($post['userId'] == $user['userId'] OR $perms['canManageAllBlogs']){

				?>
				<hr>
				<h4>Invite a Contributor</h4>
				<form action="" method="post">
					<label for="username">Username</label>
					<input type="text" id="username" name="username" required />
					<label for="role">Role</label>
					<input type="text" id="role" name="role" value="Editor" required />
					<label for="share">Proposed Reward Share (%)</label>
					<input type="text" id="share" name="share" value="20" required />
					<input type="submit" name="invite-contrib" value="Invite Contributor" />
				</form>
				<?php
			}
			elseif($post['user_blog_role'] AND !$contributor AND !$post['pending_contrib']){
				?>
				<hr>
				<h4>Become a Contributor</h4>
				<p>
					Please review the article before requesting to become a contributor.
				</p>
				<form action="" method="post">
					<label for="role">Role</label>
					<input type="text" id="role" name="role" value="Editor" required />
					<label for="share">Desired Reward Share (%)</label>
					<input type="text" id="share" name="share" value="20" required  />
					<input type="submit" name="request-contrib" value="Request Contributor Access" />
				</form>
				<?php
			}
		}
		?>
	</div>
</div>
<?php
}//endif
?>
<h2><?= $formType ?> Article
<?php
if(isset($post)){
	echo '<br><span class="text-default">'.$post['title'].'</span>';
	
	$oldPreview = '';
	if($old_version AND $old_version['versionId'] != $post['version']){
		$oldPreview = '/'.$old_version['num'];
	}
}
?>
</h2>
<a name="post-top" id="post-top"></a>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>">Back to Submissions</a>
	<?php
	$model = new \Core\Model;
	$newsroom = $model->get('modules', 'newsroom', array(), 'slug');
	$checkNewsroom = \App\AppControl::checkModuleAccess($newsroom['moduleId'], false, false);

	if($checkNewsroom AND isset($post)){
		echo '<br><a href="'.SITE_URL.'/'.$app['url'].'/'.$newsroom['url'].'">Back to Newsroom</a>';
	}
	?>
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
	<li><a href="#" class="tab active" data-tab="blog-content">1. Content</a></li>
	<li><a href="#" class="tab" data-tab="status-cat">2. Status &amp; Category</a></li>
	<li><a href="#" class="tab" data-tab="meta-data">3. Meta Content</a></li>
	<?php
	if(isset($post)){
	?>
	<li><a href="#" class="tab" data-tab="discussion">Talk</a></li>
	<li><a href="#" class="tab" data-tab="versions">Versions</a></li>
	<li><a class="view-draft" href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/preview/<?= $post['postId'] ?><?= $oldPreview ?>" target="_blank">Preview</a></li>
	<?php
	}
	?>
</ul>
<div class="clear"></div>
<div class="blog-form">
	<?= $form->open() ?>
	<?=  $this->displayFlash('blog-message') ?>
	<?php
	if(isset($post) AND !$unlock_post){
		if($post['status'] == 'published'){
			echo '<p><strong>This post is marked as complete and may only be modified by the original author or a relevant ACT member.</strong></p>';
		}
		else{
			echo '<p><strong>You must be accepted as a contributor before you can modify this post.</strong></p>';
		}
	}
	?>
	<div class="ltb-data-tab" id="blog-content" style="">
		<div class="pull-right">
			<?php
			if(!isset($post) OR (isset($post) AND $unlock_post)){
				echo '<input type="button" class="tab-btn Slick_UI_Button" data-tab="status-cat" value="Next" />';
			}
			?>
		</div>					
		<h4>Step 1: Content</h4>
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
		<?= $form->field('title')->display('div') ?>
		<?= $form->field('url')->display('div') ?>
		<?= $form->field('formatType')->display('div') ?>		
		<?= $form->field('content')->display('div') ?>
		<?= $form->field('autogen-excerpt')->display('div') ?>
		<div id="excerpt-cont" style="<?= $excpStyle ?>">
			<?= $form->field('excerpt')->display('div') ?>
		</div>
		<div class="clear"></div>
		<div class="pull-right">
			<?php
			if(!isset($post) OR (isset($post) AND $unlock_post)){
				echo '<input type="button" class="tab-btn Slick_UI_Button" data-tab="status-cat" value="Next" />';
				echo $form->displaySubmit();
			}
			?>		
		</div>			
	</div>
	<div class="ltb-data-tab" id="status-cat" style="display: none;">
		<div class="pull-right">
			<?php
			if(!isset($post) OR (isset($post) AND $unlock_post)){
				echo '<input type="button" class="tab-btn Slick_UI_Button" data-tab="meta-data" value="Next" />';
			}
			?>
		</div>			
		<h4>Step 2: Status & Category</h4>
		<?= $form->field('status')->display('div') ?>
		<?= $form->field('publishDate')->display('div') ?>
		<?php
		if($form->field('featured')){
			echo $form->field('featured')->display('div');
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
				echo $form->field('image')->display('div');
			}
		?>
		<?= $form->field('coverImage')->display('div') ?>
		<?= $form->field('categories')->display('div') ?>
		<p>
			<small>
				* Categories are automatically approved for users with self-publishing permissions (e.g editors and independent writers),<br>
				all others must be approved by either a blog admin or an editor. <br>
				Articles must be in at least one approved category in order to be considered "published".
			</small>
		</p>
		<div class="clear"></div>
		<div class="pull-right">
			<?php
			if(!isset($post) OR (isset($post) AND $unlock_post)){
				echo '<input type="button" class="tab-btn Slick_UI_Button" data-tab="meta-data" value="Next" />';
				echo $form->displaySubmit();
			}
			?>
		</div>	
	</div>	
	<div class="ltb-data-tab" id="meta-data" style="display: none;">
		<h4>Final Step: Meta Content</h4>
		<?php
		if(isset($post)){
					
			?>
			<ul>
				<li><strong>Author:</strong> <a href="<?= SITE_URL ?>/profile/user/<?= $post['author']['slug'] ?>" target="_blank"><?= $post['author']['username'] ?></a></li>
				<li><strong>Word Count:</strong> <?= number_format($post['word_count']) ?></li>
				<li><strong>Views:</strong> <?= number_format($post['views']) ?></li>
				<li><strong>Comments:</strong> <?= number_format($post['commentCount']) ?></li>
				<?php
				if(isset($perms['canUseMagicWords']) AND $perms['canUseMagicWords']){
					echo '<li><strong>Magic Words:</strong> '.number_format($magic_word_count).'</li>';
				}
				?>
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
		<div class="clear"></div>
		<div class="pull-right">
			<?php
			if(!isset($post) OR (isset($post) AND $unlock_post)){
				echo $form->displaySubmit();
			}
			?>
		</div>	
	</div>	
	<?php
	if(isset($post)){
	?>
	<div class="ltb-data-tab" id="discussion" style="display: none;">
		<?php
		if(count($private_comments) > 0 AND $perms['canPostComment']){
		?>
		<p class="pull-right">
			<a href="#comment-reply" class="btn btn-small">Post Reply</a>
		</p>
		<?php
		}//endif
		?>
		<p>
			<strong>Private Editorial Comments</strong>
		</p>
		<div class="clear"></div>
		<div class="private-comments">
		<?php
		if(!isset($private_comments) OR count($private_comments) == 0){
			echo '<p class="no-comments">No comments yet</p><ul class="comment-list"></ul>';
		}
		else{
			echo '<ul class="comment-list">';
			foreach($private_comments as $comment){
				$avatar = '';
				$commentClass = '';
				$editTime = '';
				$controls = '';
				if($comment['buried'] == 0){
					if(trim($comment['author']['avatar']) != '' 
					AND file_exists(SITE_PATH.'/files/avatars/'.$comment['author']['avatar'])){
						$avatar = '<div class="comment-avatar"><a href="'.SITE_URL.'/profile/user/'.$comment['author']['slug'].'" target="_blank"><img src="'.SITE_URL.'/files/avatars/'.$comment['author']['avatar'].'" alt="" /></a>	</div>';
					}
					
					$controls = '<div class="controls">';
					if($user AND (($comment['author']['userId'] == $user['userId'] AND $perms['canEditSelfComment'])
						OR ($comment['author']['userId'] != $user['userId'] AND $perms['canEditOtherComment']))){
						$controls .= '
									<a href="#comment-reply" data-comment-id="'.$comment['commentId'].'" data-message="'.base64_encode($comment['message']).'" class="edit-comment btn btn-small btn-blue">Edit</a> ';
					}
					if($user AND (($comment['author']['userId'] == $user['userId'] AND $perms['canDeleteSelfComment'])
						OR ($comment['author']['userId'] != $user['userId'] AND $perms['canDeleteOtherComment']))){
						$controls .= '<a href="#" class="delete delete-comment pull-right btn btn-small btn-blue" data-comment-id="'.$comment['commentId'].'">Delete</a>';
									
					}
					
					$controls .= '<div class="clear"></div></div>';
					
					$editTime = '<br><span class="last-edit"></span>';
					if($comment['editTime'] != null){
						$editTime = '<br><span class="last-edit">Last Edited: '.formatDate($comment['editTime']).'</span>';
					}
					$authorName = '<a href="'.SITE_URL.'/profile/user/'.$comment['author']['slug'].'" target="_blank">'.$comment['author']['username'].'</a> says..';
				}
				else{
					$commentClass = 'buried';
					$authorName = '';
				}
				
				echo '<li class="'.$commentClass.'">
						<a name="comment-'.$comment['commentId'].'"></a>
							'.$avatar.'
						<div class="comment-author">
							'.$authorName.'
						</div>
						<div class="comment-content">
							'.markdown($comment['message']).'
						</div>
						<div class="clear"></div>
						<div class="comment-date">
							<small>Posted on '.formatDate($comment['commentDate']).'
							'.$editTime.'</small>
						</div>
						'.$controls.'
					</li>';
			}
			echo '</ul>';

			
		}
		?>
		</div>
		<?php
		if($perms['canPostComment']){
		?>
		<a name="comment-reply"></a>
		<h4>Post Comment</h4>
		<p>
			<em>Note: @mention notifications only apply to relevant users (post contributors, author and blog ACT members).</em>
		</p>
		<?= $comment_form->displayFields() ?>
		<input type="hidden" id="comment-list-hash" value="<?= $comment_list_hash ?>" />
		<input type="hidden" id="edit-comment-id" value="0" />
		<input type="button" id="submit-comment" value="Post Comment" />
		<p>
			<strong><a href="#" id="cancel-edit" style="display: none;">Cancel</a></strong>
		</p>
		<?php
		}
		?>
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
		<table class="admin-table mobile-table">
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
		$('.tab-btn').click(function(e){
			var tab = $(this).data('tab');
			$('.blog-tabs').find('.tab').removeClass('active');
			$('.blog-tabs').find('.tab[data-tab="' + tab + '"]').addClass('active');
			$('.blog-form').find('.ltb-data-tab').hide();
			$('.blog-form').find('.ltb-data-tab#' + tab).show();
			document.getElementById('post-top').scrollIntoView();   			
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
		if(isset($post)){
			if($post['formatType'] == 'wysiwyg'){
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
			
			if($current_version > 1){
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
			$('#submit-comment').click(function(e){
				$(this).attr('disabled', 'disabled');
				var thisLink = $(this);
				if($(this).hasClass('edit')){
					var url = '<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/edit/<?= $post['postId'] ?>/comments/edit';
					var getMessage = $('textarea[name="message"]').val();
					var id = $('#edit-comment-id').val();
					$.post(url, {message: getMessage, commentId: id, no_edit: 1}, function(data){
						if(data.error != null){
							alert(data.error);
							return false;
						}
						
						$('a[name="comment-' + id + '"]').parent().find('.comment-content').html(data.comment.html_content);
						$('a[name="comment-' + id + '"]').parent().find('.edit-comment').data('message', data.comment.encoded);
						$('a[name="comment-' + id + '"]').parent().find('.last-edit').html('Last Edited: ' + data.comment.formatEditDate);
						$('textarea[name="message"]').val('');
						$('#discussion .markdown-preview-cont').html('');
						$('#cancel-edit').hide();
						thisLink.removeClass('edit').val('Post Comment');
						$('#comment-list-hash').val(data.new_hash);
						$('#edit-comment-id').val(0);
						thisLink.removeAttr('disabled');
						
					});
				}
				else{
					var url = '<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/edit/<?= $post['postId'] ?>/comments/post';
					var getMessage = $('textarea[name="message"]').val();
					$.post(url, {message: getMessage, no_edit: 1}, function(data){
						if(data.error != null){
							alert(data.error);
							return false;
						}
						var new_html = '<li><a name="comment-' + data.comment.commentId + '"></a>';
						if(data.comment.author.avatar != ''){
							new_html = new_html + '<div class="comment-avatar"><a href="<?= SITE_URL ?>/profile/user/' + data.comment.author.slug + '" target="_blank"><img src="<?= SITE_URL ?>/files/avatars/' + data.comment.author.avatar + '" alt="" /></a></div>';
						}
						new_html = new_html + '<div class="comment-author"><a href="<?= SITE_URL ?>/profile/user/ ' + data.comment.author.slug + '" target="_blank">' + data.comment.author.username + '</a> says...</div>';
						new_html = new_html + '<div class="comment-content">' + data.comment.html_content + '</div><div class="clear"></div>';
						new_html = new_html + '<div class="comment-date"><small>Posted on ' + data.comment.formatDate + '</small></div>';
						new_html = new_html + '<div class="controls">';
						var editSelfPerm = <?= intval($perms['canEditSelfComment']) ?>;
						var deleteSelfPerm = <?= intval($perms['canDeleteSelfComment']) ?>;
						
						if(editSelfPerm == 1){
							new_html = new_html + '<a href="#comment-reply" data-comment-id="' + data.comment.commentId + '" data-message="' + data.comment.encoded + '" class="edit-comment btn btn-small btn-blue">Edit</a>';
						}
						
						if(deleteSelfPerm == 1){
							new_html = new_html + '<a href="#" style="float: right;" data-comment-id="' + data.comment.commentId + '" class="delete-comment delete btn btn-small btn-blue">Delete</a>';
						}

						new_html = new_html + '</div></li>';
						$('.comment-list').append(new_html);
						$('textarea[name="message"]').val('');
						$('#discussion .markdown-preview-cont').html('');
						$('#comment-list-hash').val(data.new_hash);
						$('p.no-comments').remove();
						thisLink.removeAttr('disabled');
					});
				}
			});
			
			$('.content').delegate('.delete-comment', 'click', function(e){
				e.preventDefault();
				var url = '<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/edit/<?= $post['postId'] ?>/comments/delete';
				var id = $(this).data('comment-id');
				var thisLink = $(this);
				$.post(url, {commentId: id, no_edit: 1}, function(data){
					if(data.error != null){
						alert(data.error);
						return false;
					}
						
					thisLink.parent().parent().remove();
				});
			});
			
			$('.content').delegate('.edit-comment', 'click', function(e){
				var id = $(this).data('comment-id');
				var message = $(this).data('message');
				var decode = window.atob(message);
				$('#discussion').find('textarea[name="message"]').val(decode);
				$('#edit-comment-id').val(id);
				$('#submit-comment').addClass('edit').val('Edit Comment');
				$('#cancel-edit').show();
			});
			
			$('#cancel-edit').click(function(e){
				e.preventDefault();
				$('#discussion').find('textarea[name="message"]').val('');
				$('#edit-comment-id').val(0);
				$('#submit-comment').removeClass('edit').val('Post Comment');
				$(this).hides();
			});
			
			window.hashCheck = setInterval(function(){
				var url = '<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/edit/<?= $post['postId'] ?>/comments/check';
				var curHash = $('#comment-list-hash').val();
				$.get(url, function(data){
					if(curHash != data.hash){
						var url2 = '<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/edit/<?= $post['postId'] ?>/comments/get';
						$.get(url2, function(data2){
							$('#comment-list-hash').val(data2.new_hash);
							var new_html = '';
							$.each(data2.comments, function(idx, comment){
								new_html = new_html +  '<li><a name="comment-' + comment.commentId + '"></a>';
								if(comment.author.avatar != ''){
									new_html = new_html + '<div class="comment-avatar"><a href="<?= SITE_URL ?>/profile/user/' + comment.author.slug + '" target="_blank"><img src="<?= SITE_URL ?>/files/avatars/' + comment.author.avatar + '" alt="" /></a></div>';
								}
								new_html = new_html + '<div class="comment-author"><a href="<?= SITE_URL ?>/profile/user/ ' + comment.author.slug + '" target="_blank">' + comment.author.username + '</a> says...</div>';
								new_html = new_html + '<div class="comment-content">' + comment.html_content + '</div><div class="clear"></div>';
								var editDate = '';
								if(comment.editTime != null){
									editDate = '<br><span class="last-edit">Last Edited: ' + comment.formatEditDate + '</span>';
								}
								new_html = new_html + '<div class="comment-date"><small>Posted on ' + comment.formatDate + editDate + '</small></div>';
								new_html = new_html + '<div class="controls">';
								var editSelfPerm = <?= intval($perms['canEditSelfComment']) ?>;
								var deleteSelfPerm = <?= intval($perms['canDeleteSelfComment']) ?>;
								
								if(editSelfPerm == 1){
									new_html = new_html + '<a href="#comment-reply" data-comment-id="' + comment.commentId + '" data-message="' + comment.encoded + '" class="edit-comment btn btn-small btn-blue">Edit</a>';
								}
								
								if(deleteSelfPerm == 1){
									new_html = new_html + '<a href="#" style="float: right;" data-comment-id="' + comment.commentId + '" class="delete-comment delete btn btn-small btn-blue">Delete</a>';
								}

								new_html = new_html + '</div></li>';
							});
							$('.comment-list').html(new_html);
							
						});
						if(!$('.tab[data-tab="discussion"]').hasClass('active')){
							$('.tab[data-tab="discussion"]').addClass('new-comments');
						}
					}
				});
			
			}, 5000);
			
			$('.tab[data-tab="discussion"]').click(function(e){
				if($(this).hasClass('new-comments')){
					$(this).removeClass('new-comments');
				}
			});
			<?php
		}//end if(isset($post))
		?>
		
		$('.blog-form').find('input[type="submit"]').click(function(e){
			var catFound = false;
			$('input[name="categories[]"]').each(function(){
				if($(this).is(':checked')){
					catFound = true;
				}
			});
			
			if(!catFound){
				var check = confirm('You have not selected any article categories (required to get published), are you sure you want to submit?');
				if(!check || check == null){
					e.preventDefault();
					return false;
				}
			}
			
		});
	});
</script>
