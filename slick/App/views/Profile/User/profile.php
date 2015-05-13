<h1><?= $profile['username'] ?></h1>
<div class="pull-right">
	<p>
		<a href="#activity" class="btn">Activity</a>
	</p>
</div>
<p>
	<a href="<?= route('profile.member-list') ?>">Back to Community Directory</a>
</p>
<div class="clear"></div>
<div class="profile-cont">
	<?php
	$avImage = $profile['avatar'];
	if(!isExternalLink($profile['avatar'])){
		$avImage = SITE_URL.'/files/avatars/'.$profile['avatar'];
	}


	$avatar = '<img src="'.$avImage.'" alt="'.$profile['username'].'" />';	
	//if(isset($profile['avatar']) AND trim($profile['avatar']) != ''){
		?>
		<div class="profile-pic">
			<?= $avatar ?>
			<?php
			if($user AND $user['userId'] != $profile['userId']){
				echo '<br><a href="'.SITE_URL.'/dashboard/account/messages/send?user='.$profile['slug'].'" target="_blank" class="btn send-msg-btn" title="Send private message" ><i class="fa fa-envelope"></i> Message</a>';
			}							
			?>
			<?php
			if($activity['forums'] AND $activity['forums']['posts']){
				echo '<strong>Posts:</strong> '.number_format($activity['forums']['count']).'<br>';
			}
			?>
			<?php
			if($activity['blog'] AND $activity['blog']['posts']){
				echo '<strong>Articles:</strong> '.number_format($activity['blog']['count']).'<br>';
			}
			?>
			<strong>Profile Views:</strong> <?= number_format($profile_views) ?><br>
		</div>
		<?php
	//}
	?>
	<div class="profile-content">
		<?php
		if(intval($profile['pubProf']) == 0 AND (!$user OR $user['userId'] != $profile['userId'])){
			echo '<p><strong>This users\' profile is private.</strong></p>';
		}
		else{
		?>
		<ul class="profile-info">
			<?php
			$online_icon = 'fa-circle-o text-error';
			$online_title = 'Offline';
			$activeTime = strtotime($profile['lastActive']);
			$time = time();
			$diff = $time - $activeTime;
			if($diff < 7200){
				$online_icon = 'fa-circle text-success';
				$online_title = 'Recently Online';
			}
			?>
			<li><strong>Date Registered:</strong> <?= formatDate($profile['regDate']) ?></li>
			<?php
			if($profile['lastActive'] != '0000-00-00 00:00:00' AND $profile['lastActive'] != null){
			?>
			<li><strong>Last Active:</strong> <?= formatDate($profile['lastActive']) ?> <i class="fa <?= $online_icon ?>" title="<?= $online_title ?>"></i></li>
			<?php
			}
			if(trim($profile['email']) != '' AND intval($profile['showEmail']) === 1){
			?>
				<li><strong>Email:</strong> <a href="mailto:<?= $profile['email'] ?>"><?= $profile['email'] ?></a></li>
			<?php
			}
			
			if($activity['tokenly']){
				if($activity['tokenly']['pop'] != 'N/A'){
					echo '<li><strong>LTB Participation Rank:</strong> #'.$activity['tokenly']['pop'].'</li>';
				}
				if($activity['tokenly']['content'] != 'N/A'){
					echo '<li><strong>LTB Content Creation Rank:</strong> #'.$activity['tokenly']['content'].'</li>';
				}
			}
			
			$model = new \Core\Model;
			foreach($profile['profile'] as $field){
				if($field['fieldId'] == PRIMARY_TOKEN_FIELD){
					$getAddress = $model->getAll('coin_addresses', array('userId' => $profile['userId'], 'address' => $field['value']));
					if($getAddress AND count($getAddress) > 0){
						$getAddress = $getAddress[0];
						if($getAddress['public'] == 0){
							continue;
						}
					}
					$field['value'] = '<span class="companion-tip-button" data-address="'.$field['value'].'" data-label="'.$profile['username'].'" data-tokens="all">'.$field['value'].'</span>';
				}
				if($field['type'] == 'textarea'){
					echo '<li class="profile-area"><strong>'.$field['label'].':</strong><br/>
													'.markdown($field['value']).'</li>';
					
				}
				else{
					echo '<li><strong>'.$field['label'].':</strong> '.autolink($field['value']).'</li>';
				}
			}
			if($activity['tokenly'] AND $activity['tokenly']['addresses']){
				echo '<li>
						<strong>Public Bitcoin Addresses:</strong>
						<ul>';
				
				foreach($activity['tokenly']['addresses'] as $address){
					echo '<li><a href="https://blockchain.info/address/'.$address['address'].'" target="_blank">'.$address['address'].'</a></li>';
				}
						
				echo '</ul></li>';
			}
			?>
		</ul>
		<a name="activity"></a>
		<div class="clear"></div>
	</div>
	<hr>
	<h2 class="text-default">Account Activity</h2>	
	<ul class="ltb-stat-tabs" data-tab-type="profile-activity">
		<?php
		if($activity['forums'] AND $activity['forums']['posts']){
			$forumActive = '';
			$forumDisplay = 'display: none;';
			if(!isset($_GET['t']) OR (isset($_GET['t']) AND $_GET['t'] == 'forums')){
				$forumActive = 'active';
				$forumDisplay = 'display: block;';
			}
		?>
			<li><a href="#" class="tab <?= $forumActive ?>" data-tab="forum-activity">Forums</a></li>
		<?php
		}
		if($activity['blog'] AND $activity['blog']['posts']){
			$blogActive = '';
			$blogDisplay = 'display: none;';
			if((isset($_GET['t']) AND $_GET['t'] == 'blog') OR (!$activity['forums'] OR !$activity['forums']['posts'])){
				$blogActive = 'active';
				$blogDisplay = 'display: block;';
			}			
		?>
			<li><a href="#" class="tab <?= $blogActive ?>" data-tab="blog-activity">Blog Posts</a></li>
		<?php
		}
		?>
	</ul>
	<div class="clear"></div>
	<div class="profile-activity">
		<?php
		$foundActivity = false;
		if($activity['forums'] AND $activity['forums']['posts']){
			$foundActivity = true;
		?>
		<div class="ltb-data-tab" id="forum-activity" style="<?= $forumDisplay ?>">
			<h3>Forum Posts</h3>
			<ul>
				<li><strong>Replies:</strong> <?= number_format($activity['forums']['replies']) ?></li>
				<li><strong>Topics:</strong> <?= number_format($activity['forums']['topics']) ?></li>
				<li><strong>Likes Received:</strong> <?= number_format($activity['forums']['likes']) ?></li>
			</ul>
			<ul class="forum-activity-list">
			<?php
			foreach($activity['forums']['posts'] as $item){
				?>
				<li>
					<h4><a href="<?= route('forum.forum-post', '/'.$item['ref_url']) ?>" target="_blank"><?= $item['ref_title'] ?></a></h4>
					<?= markdown($item['content']) ?>
					<div class="post-date">
						<em>Posted on <?= formatDate($item['date']) ?></em><br>
						<strong><a href="<?= route('forum.forum-board', '/'.$item['board_url']) ?>" target="_blank"><?= $item['board_title'] ?></a></strong>
					</div>
				</li>
				<?php
			}
			?>
			</ul>
			<?php
			$pager = new \UI\Pager;
			$pager->addClass('pager');
			$curForumPage = 1;
			if(isset($_GET['page']) AND isset($_GET['t']) AND $_GET['t'] == 'forums'){
				$curForumPage = intval($_GET['page']);
			}
			echo $pager->display($activity['forums']['num_pages'], '?t=forums&page=', $curForumPage, '', '#activity');
			?>

		</div>
		<?php
		}
		if($activity['blog'] AND $activity['blog']['posts']){
			$foundActivity = true;
		?>
		<div class="ltb-data-tab" id="blog-activity" style="<?= $blogDisplay ?>">
			<h3>Blog Posts</h3>
			<ul>
				<li><strong>Posts Authored:</strong> <?= number_format($activity['blog']['written']) ?></li>
				<li><strong>Posts Contributed To:</strong> <?= number_format($activity['blog']['contribs']) ?></li>
			</ul>
			<?php
			if($activity['blog'] AND $activity['blog']['posts']){
				?>
				<ul class="blog-list">
				<?php

				$settings = new \App\CMS\Settings_Model;
				$maxChars = $settings->getSetting('blog-excerptChars');
				if(!$maxChars){
					$maxChars = 250;
				}
				$blogApp = get_app('blog');
				$blogPostModule = get_app('blog.blog-post');

				$imagePath = SITE_PATH.'/files/blogs';
				foreach($activity['blog']['posts'] as $post){
					$getIndex = $settings->getAll('page_index', array('itemId' => $post['postId'], 'moduleId' => $blogPostModule['moduleId'], 'siteId' => $site['siteId']));
					if($getIndex AND count($getIndex) > 0){
						$post['url'] = SITE_URL.'/'.$getIndex[count($getIndex) - 1]['url'];
					}
					else{
						$post['url'] = SITE_URL.'/'.$blogApp['url'].'/post/'.$post['url'];
					}
					
					$displayName = $post['author']['username'];
					if(isset($post['author']['profile']['real-name']) AND trim($post['author']['profile']['real-name']['value']) != ''){
						$displayName =  $post['author']['profile']['real-name']['value'];
					}

					$displayName = '<a href="'.SITE_URL.'/profile/user/'.$post['author']['slug'].'">'.$displayName.'</a>';
					
					
					if($post['formatType'] == 'markdown'){
						$post['excerpt'] = markdown($post['excerpt']);
					}
				?>
					<li>
						<?php
						if(trim($post['coverImage']) != '' AND file_exists($imagePath.'/'.$post['coverImage'])){
							echo '<div class="blog-image"><img src="'.SITE_URL.'/files/blogs/'.$post['coverImage'].'" alt="" /></div>';
						}
						?>
						<h2><a href="<?= $post['url'] ?>"><?= $post['title'] ?></a></h2>
						<div class="blog-date">
							Published on <?= date('F jS, Y', strtotime($post['publishDate'])) ?> by 
							<?= $displayName ?>
							<?php
							if($post['role'] != 'Author'){
								echo '<br><strong>'.$profile['username'].'\'s role:</strong> '.$post['role'];
							}
							?>
						</div>
						<div class="blog-excerpt">
							<?php
							if(isset($post['soundcloud-id'])){
								echo '<iframe src="https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F'.$post['soundcloud-id'].'&auto_play=false&show_artwork=true&color=ff7700" width="400" height="100"></iframe>
								<br><br>';
							}
							?>							
							<?= $post['excerpt'] ?>
							<a href="<?= $post['url'] ?>" class="blog-more">Read More</a>
						</div>
						<div class="blog-extra">
							<div class="blog-social">
								<?php
								$shareURL = SITE_URL.'/'.$app['url'].'/post/'.$post['url'];
								?>
								<a href="http://www.reddit.com/submit?url=<?= $shareURL ?>" target="_blank"><img src="<?= THEME_URL ?>/images/reddit.png" alt="Post on Reddit" /></a>
								<a href="http://www.facebook.com/sharer.php?u=<?= $shareURL ?>" target="_blank"><img src="<?= THEME_URL ?>/images/facebook.png" alt="Share this Post" /></a>
								<a href="http://twitter.com/share?text=<?= urlencode($post['title']) ?>&url=<?= $shareURL ?>" target="_blank"><img src="<?= THEME_URL ?>/images/twitter.png" alt="Tweet this Post" /></a>
								<a href="https://plus.google.com/share?url=<?= $shareURL ?>" target="_blank"><img src="<?= THEME_URL ?>/images/gplus.png" alt="+1 this Post" /></a>
							</div>
							<?php
							if(isset($post['author']['profile']['bitcoin-address']) AND trim($post['author']['profile']['bitcoin-address']['value']) != ''){
								$btcAddress = $post['author']['profile']['bitcoin-address']['value'];
							?>
							Tip this Post: <a href="bitcoin:<?= $btcAddress ?>"><?= $btcAddress ?></a>

							<?php
							}//endif
							?>
							<div class="blog-cats">
								<?php
								if(count($post['categories']) > 0){
									echo 'Categories: ';
									$catList = array();
									foreach($post['categories'] as $cat){
										$catList[] =  '<a href="'.SITE_URL.'/'.$app['url'].'/category/'.$cat['slug'].'">'.$cat['name'].'</a>';
									}
									echo join(', ', $catList);
								}
								?>
							</div>
						</div>
						<div class="clear"></div>
					</li>
				<?php
				}//endforeach
				?>
				</ul>				
				<?php
				$blogPager = new \UI\Pager;
				$blogPager->addClass('pager');
				$curBlogPage = 1;
				if(isset($_GET['page']) AND isset($_GET['t']) AND $_GET['t'] == 'blog'){
					$curBlogPage = intval($_GET['page']);
				}
				echo $blogPager->display($activity['blog']['num_pages'], '?t=blog&page=', $curBlogPage, '', '#activity');
				
			}
			?>
		</div>
		<?php
		}//endif
		if(!$foundActivity){
			echo '<p>No account activity found.</p>';
		}
		?>
	</div>
	<?php
	}//endif
	?>
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
