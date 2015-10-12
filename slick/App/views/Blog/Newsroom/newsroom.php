<?php
function getCategoryParent($parentId, $blogs, $output = array())
{
	foreach($blogs as $blog){
		foreach($blog['categories'] as $cat){
			if($cat['categoryId'] == $parentId){
				$output[] = $cat;
				return getCategoryParent($cat['parentId'], $blogs, $output);
			}
		}
	}
	return $output;
}
$blogImage = '';
if(trim($blog['image']) != '' AND file_exists(SITE_PATH.'/files/blogs/'.$blog['image'])){
	$blogImage = '<span class="blog-avatar"><img src="'.SITE_URL.'/files/blogs/'.$blog['image'].'" alt="" /></span>';
}

?>
<p class="pull-right text-center" style="width: 150px; font-size: 12px;">
	<?= $blogImage ?>
	<strong>Server Time:</strong><br> <span class="server-time"><?= date('Y/m/d  H:i') ?></span>
</p>
<h2>The Newsroom</h2>
<div class="newsroom-cont">
<?php echo $this->displayBlock('dashboard-newsroom'); ?>
<?php

	echo '<div class="newsroom-filter">
							<div class="form-group">
								<label>Filter Posts:</label>
								<select class="status_filter">
									<option value="pending">Pending Review / Category Approval</option>
									<option value="draft">Drafts</option>
									<option value="published">Finished & Published</option>
									<option value="all">Show All</option>
								</select>
							</div>';
	echo '<div class="form-group"><label>Load More Posts:</label>
	<select id="load_posts">';			
	for($i = 25; $i <= 200; $i=$i*2){
		$selected = '';
		if(isset($_GET['load']) AND $_GET['load'] == $i){
			$selected = 'selected';
		}
		echo '<option '.$selected.' >'.$i.'</option>';
	}
	$allSelect = '';
	if(isset($_GET['load']) AND $_GET['load'] == 'all'){
		$allSelect = 'selected';
	}
	echo '<option '.$allSelect.'>all</option>';
	echo '</select></div>';
	echo '</div>';
	echo '<hr>';
	
	if(count($blogs) > 1){
		echo '<p><a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'"><i class="fa fa-mail-reply"></i> Back to Blog newsroom list</a></p>';
	}

	echo $this->displayFlash('blog-message');		
	$profModel = new \App\Profile\User_Model;


		echo '<h3>'.$blog['name'].'</h3>';
		
	if(isset($blog_rooms['num_posts'])){

		$numLoaded = count($blog_room);

		echo '<p><strong>Loaded '.$numLoaded.' '.pluralize('post', $numLoaded).' out of '.$blog_rooms['num_posts'].'</strong></p>';
	}		
		
		echo '<div class="'.$blog['slug'].'_posts">';
		?>
		<div class="newsroom-stats-cont">
			<ul class="ltb-pop-stats">
				<li><strong>Posts Published:</strong> <?= number_format($blog['stats']['posts_published']) ?></li>
				<li><strong>Posts Submitted:</strong> <?= number_format($blog['stats']['posts_submitted']-1) ?></li>
				<li><strong>Total Participants:</strong> <?= number_format($blog['stats']['total_contribs']) ?></li>
				<li><strong>Total Views:</strong> <?= number_format($blog['stats']['total_views']) ?></li>
				<li><strong>Total Comments:</strong> <?= number_format($blog['stats']['total_comments']) ?></li>
			</ul>
			<div class="clear"></div>
		</div>
		<?php
		$teamList = array();
		foreach($blog['team'] as $member){
			$teamList[] = '<a href="'.SITE_URL.'/profile/user/'.$member['slug'].'" title="'.$member['role_nice'].'" target="_blank">'.$member['username'].'</a>';
		}
		if(count($teamList) > 0){
			
		?>
			<p>
				<small><em><strong><?= $blog['name'] ?> Autonomous Content Team: <?= join(', ', $teamList) ?></strong></em></small><br>
				<small><em><strong><a href="#<?= $blog['slug'] ?>_contrib_list" class="fancy">View All Contributors</a></strong></em></small>
				<div id="<?= $blog['slug'] ?>_contrib_list" style="display: none;" class="all-contrib-list">
					<div class="newsroom-manage">
					<h3><?= $blog['name'] ?> Contributors (<?= $blog['stats']['total_contribs'] ?>)</h3>
					<?php
					$blogContribList = array();
					foreach($blog['stats']['contrib_list'] as $blogContrib){
						$getAvatar = $profModel->getUserAvatar($blogContrib['userId']);
						if(!isExternalLink($getAvatar)){
							$getAvatar = SITE_URL.'/files/avatars/'.$getAvatar;
						}
						$blogContribList[] = '<a href="'.SITE_URL.'/profile/user/'.$blogContrib['slug'].'" target="_blank"><span class="mini-avatar"><img src="'.$getAvatar.'" alt="" /></span> '.$blogContrib['username'].' ('.$blogContrib['count'].')</a>';
					}
					echo join(', ', $blogContribList);
					?>
					</div>
				</div>
			</p>
		<?php
		}
		
		if(!$blog_room){
			echo '<p>No blog posts submitted yet!</p></div><hr>';
		}
		else{
		$time = time();
		echo '<table class="admin-table mobile-table data-table '.$blog['slug'].'_posts submissions-table">
				<thead>
					<tr>
						<th>ID</th>
						<th>Title</th>
						<th>Status</th>
						<th>Publish Date</th>
						<th class="no-sort"></th>
					</tr>
				</thead>
				<tbody>';
		foreach($blog_room as $post){
			
			$titleLink = $post['title'];
			$titleLink = '<a href="'.SITE_URL.'/'.$app['url'].'/submissions/edit/'.$post['postId'].'" class="">'.$post['title'].'</a><br>
						  <small>Author: <a href="'.SITE_URL.'/profile/user/'.$post['user_slug'].'" target="_blank">'.$post['username'].'</a>';
						  
			foreach($post['contributors'] as $postContrib){
				$titleLink .= '<br>'.$postContrib['role'].': <a href="'.SITE_URL.'/profile/user/'.$postContrib['slug'].'" target="_blank">'.$postContrib['username'].'</a> ';
			}
			
			$titleLink .= '</small>';
			
			$status_text = $post['table_status'][$blog['blogId']];
			switch($post['table_status'][$blog['blogId']]){
				case 'published':
					$status_class = 'text-success';
					$status_text = 'Published';
					break;
				case 'editing':
				case 'finish-pending':
					$status_class = 'text-progress';
					$status_text = 'Pending Approval';
					break;
				case 'ready':
				case 'review':
					$status_class = 'text-pending';
					$status_text = 'Ready for Review';
					break;
				case 'draft':
				default:
					$status_class = 'text-default';
					$status_text = 'Draft';
					break;
			}
			
			$commentIcon = '';
			if($post['new_comments']){
				$commentIcon = '<i class="fa fa-comment text-success" title="New Editorial Comments"></i> ';
			}
			if($post['is_contributor']){
				$commentIcon .= ' <i class="fa fa-user text-pending" title="Contributing"></i> ';
			}
			elseif(isset($post['contributors'][0])){
				$commentIcon .= ' <i class="fa fa-group text-progress" title="Has Contributors"></i> ';
			}		
			
			$publishTime = strtotime($post['publishDate']);
			$postTime = strtotime($post['postDate']);
			$editTime = strtotime($post['editTime']);
						  				  
?>
			<tr>
				<td><?= $post['postId'] ?></td>
				<td class="post-title"><?= $commentIcon ?><?= $titleLink ?></td>
				<td><span class="<?= $status_class ?>"><?= $status_text ?></span></td>
				<td><?= date('Y/m/d \<\b\r\> H:i', $publishTime) ?></td>
				<td class="table-actions">
					<a href="#manage-<?= $post['postId'] ?>" class="fancy">Manage</a>
					<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/submissions/edit/<?= $post['postId'] ?>" target="_blank">Edit Copy</a>
					<div id="manage-<?= $post['postId'] ?>" class="newsroom-manage" style="display: none;">
						<h3>Manage Post:<br> <span class="text-default"><?= $post['title'] ?></span></h3>
						<ul class="post-manage-info">
							<li><strong>Primary Author:</strong> <a href="<?= SITE_URL ?>'/profile/user/<?= $post['user_slug'] ?>" target="_blank"><?= $post['username'] ?></a></li>
							<li><strong>Date Created:</strong> <?= formatDate($postTime) ?></li>
							<li>
								<?php
								$publishDate = formatDate($publishTime);
								
								if($post['published'] == 0 OR $publishTime > $time OR $post['status'] != 'published'){
									$publishTitle =  'Scheduled to Publish:';
									if($post['status'] != 'published' AND $publishTime < $time){
										$publishDate = '<span class="text-error">'.$publishDate.'</span>';
									}
									else{
										$publishDate = '<span class="text-pending">'.$publishDate.'</span>';
									}
								}
								else{
									$publishTitle = 'Published:';
									$publishDate = '<span class="text-success">'.$publishDate.'</span>';
								}
								
								echo '<strong>'.$publishTitle.'</strong> '.$publishDate;
								?>
								</li>
							<?php
							if($post['edit_user'] > 0){
								?>
								<li><strong>Last Updated:</strong> <?= formatDate($editTime) ?> <small>(<a href="<?= SITE_URL ?>'/profile/user/<?= $post['edit_user']['slug'] ?>" target="_blank"><?= $post['edit_user']['username'] ?></a>)</small></li>
								<?php
							}
							?>
							<?php
							if($post['views'] > 0){
							?>
							<li><Strong>Views:</Strong> <?= number_format($post['views']) ?></li>
							<?php
							}
							if($post['commentCount'] > 0){
							?>
							<li><Strong>Comments:</Strong> <?= number_format($post['commentCount']) ?></li>
							<?php
							}
							if($post['magic_words'] > 0){
								echo '<li><strong>Magic Words:</strong> '.number_format($post['magic_words']).'</li>';
							}
							?>
							<li><strong>Post Status:</strong>
								<?php
								switch($post['status']){
									case 'published':
										echo 'Finished';
										break;
									case 'ready':
										echo 'Ready for Review';
										break;
									case 'draft':
									default:
										echo 'Draft';
										break;
								}
								?>
							</li>
						</ul>
						<div class="clear"></div>
						<div class="pull-right"><a href="<?= SITE_URL ?>/<?= $app['url'] ?>/submissions/edit/<?= $post['postId'] ?>" target="_blank" class="btn btn-large">Edit/View Copy</a></div>
						<div class="pull-left">
							<?php
							if($post['published'] == 1 AND $publishTime < $time AND $post['status'] == 'published'){
								?>
								<a href="<?= SITE_URL ?>/blog/post/<?= $post['url'] ?>" target="_blank"  class="btn btn-large">View Post</a>
								<?php
							}
							else{
								?>
								<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/submissions/preview/<?= $post['postId'] ?>" target="_blank"  class="btn btn-large">View Draft</a>
								<?php
							}
							?>
						</div>
						<div class="clear"></div>
						<br>
						<h4>Categories</h4>
						<form action="?blog=<?= $blog['slug'] ?>" method="post">
							<input type="hidden" name="update-categories" value="<?= $post['postId'] ?>" />
						<?php
						if(count($post['categories']) == 0){
							echo '<p>No categories selected.</p>';
						}
						else{
							foreach($post['categories'] as $catBlog =>  $postCats){
								echo '<p>
										<strong>'.$blogs[$catBlog]['name'].'</strong>
									  </p>
									  <ul class="disc newsroom-manage-categories">';	
											
								foreach($postCats as $postCat){
									$parents = array_reverse(getCategoryParent($postCat['parentId'], $blogs));
										
									$parentList = array();
									foreach($parents as $parent){
										$parentList[] = '<em>'.$parent['name'].'</em>';
									}
									$parentList[] = '<strong>'.$postCat['name'].'</strong>';
									
									$pendChecked = 'checked';
									$approveChecked = '';
									if($postCat['approved'] == 1){
										$pendChecked = '';
										$approveChecked = 'checked';
									}
									?>
									<li>
										<?= join(' / ', $parentList) ?>
										<div class="manage-category-form">
										<input type="radio" name="category_<?= $post['postId'] ?>_<?= $postCat['categoryId'] ?>" value="pending" id="pending_<?= $post['postId'] ?>_<?= $postCat['categoryId'] ?>" <?= $pendChecked ?> />
										<label for="pending_<?= $post['postId'] ?>_<?= $postCat['categoryId'] ?>">Pending</label>
										<input type="radio" name="category_<?= $post['postId'] ?>_<?= $postCat['categoryId'] ?>" value="approve" id="approve_<?= $post['postId'] ?>_<?= $postCat['categoryId'] ?>" <?= $approveChecked ?> />
										<label for="approve_<?= $post['postId'] ?>_<?= $postCat['categoryId'] ?>">Approve</label>
										<input type="radio" name="category_<?= $post['postId'] ?>_<?= $postCat['categoryId'] ?>" value="reject" id="reject_<?= $post['postId'] ?>_<?= $postCat['categoryId'] ?>"  />
										<label for="reject_<?= $post['postId'] ?>_<?= $postCat['categoryId'] ?>">Reject</label>
										</div>
									</li>
									<?php
								}
								echo '</ul>';
							}
							echo '<input type="submit" value="Update Categories" />';
						}
						?>
						</form>
						<br>
						<h4>Contributors</h4>
						<?php
		$contribList = array();
		$contribList[] = array('username' => $post['username'], 'slug' => $post['user_slug'],
							   'role' => 'Author', 'share' => '*');
							   
		$contribList = array_merge($contribList, $post['contributors']);

	
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
				}
			}
			echo '<tr class="'.$rowClass.'">
					<td>'.$contribUser.'</td>
					<td>'.$contribRole.'</td>
					<td>'.$contribShare.'</td>
				  </tr>';
		}
		
		echo '</tbody></table>';
		echo '<p><strong>Author Reward Share:</strong> '.convertFloat($authorTotal).'%</p>';
		echo '<p><small>* original author receives whatever reward share percentage is left over.</small></p>';
						?>
					</div>
				</td>
			</tr>
<?php
		}//endforeach
		echo '</tbody></table></div>';
	}//endif
?>
</div><!-- newsroom-cont -->
<link href="//cdn.datatables.net/1.10.3/css/jquery.dataTables.css" rel="stylesheet" />
<script type="text/javascript" src="//cdn.datatables.net/1.10.3/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
	$.fn.dataTableExt.afnFiltering.push(
		function( oSettings, aData, iDataIndex ) {
			var useFilter = $('.status_filter').val();
			var get = aData[2];
			
			if(useFilter == 'all'){
				return true;
			}
			if(useFilter == 'pending'){
				if(get == 'Ready for Review' || get == 'Pending Approval'){
					return true;
				}
			}
			
			if(useFilter == 'draft'){
				if(get == 'Draft'){
					return true;
				}
			}
			
			if(useFilter == 'published'){
				if(get == 'Published'){
					return true;
				}
			}
			return false;
		}
	);		
	$(document).ready(function(){
	
		var tables = $('.data-table').DataTable({
			searching: true,
			lengthChange: true,
			paging: true,
			iDisplayLength: 10,
			"order": [[ 3, "desc" ]]
		});		
		
		$('.status_filter').change(function(e){
			tables.draw();
		});
		
		$('#load_posts').change(function(e){
			var num = $(this).val();
			if(num != 'all'){
				num = parseInt(num);
			}
			var url = window.location.href;
			window.location.replace(window.location.origin + window.location.pathname + '?load=' + num);
		});
	
		$('.expand-posts').click(function(e){
			e.preventDefault();
			var blog = $(this).data('blog');
			if($(this).hasClass('collapse')){
				$('.' + blog + '_posts').slideUp();
				$(this).html('<i class="fa fa-plus-circle"></i>');
				$(this).removeClass('collapse');
			}
			else{
				$('.' + blog + '_posts').slideDown();
				$(this).html('<i class="fa fa-minus-circle"></i>');
				$(this).addClass('collapse');
			}
		});
		
		setInterval(function(){
			var url = '<?= SITE_URL ?>/api/v1/info';
			$.get(url, function(data){
				var new_time = data['system-time'];
				var t = new_time.split(/[- :]/);
				var d = new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
				var month = d.getMonth()+1;
				if(month < 10){
					month = '0' + month;
				}
				var day = d.getDay()+1;
				if(day < 10){
					day = '0' + day;
				}
				var hours = d.getHours();
				if(hours < 10){
					hours = '0' + hours;
				}
				var minutes = d.getMinutes();
				if(minutes < 10){
					minutes = '0' + minutes;
				}
				var formatted = d.getFullYear() + '/' + month + '/' + day + ' ' + hours + ':' + minutes;
				$('.server-time').html(formatted);
			});
			
		}, 30000);
		
	});
</script>
