<h1 class="large">Community Directory</h1>
<hr>
<?php
if($page_mod == ''){
?>
<div class="pull-right">
	<p>
		<a href="#activity" class="btn">Activity</a>
	</p>
</div>
<?php
}
?>
<p>
	<a href="<?= route('profile.member-list') ?>" title="Back to user directory"><i class="fa fa-mail-reply"></i> Go back</a>
</p>
<div class="clear"></div>
<div class="profile-cont">
	<div class="profile-hud">
		<?php
		
$profileModel = new \App\Profile\User_Model;
$meta = new \App\Meta_Model;
//$user['profile'] = $profileModel->getUserProfile($user['userId']);
//if(isset($user['profile']['profile'])){
	//$user['profile'] = $user['profile']['profile'];
//}
$display_name = $profile['username'];
$show_username = false;
if(isset($profile['profile']['real-name']) AND trim($profile['profile']['real-name']['value']) != ''){
	$display_name = $profile['profile']['real-name']['value'];
	$show_username = $profile['username'];
}

$statuses = array('online' => 'text-success',
				  'away' => 'text-pending',
				  'busy' => 'text-progress',
				  'offline' => 'text-error');

$forumDisplay = '';
$blogDisplay = '';


if(!isset($profile['meta']['avatar']) OR trim($profile['meta']['avatar']) == ''){
	$profile['meta']['avatar'] = 'default.jpg';
}		
		?>
			<div class="dashboard-hud" >
				<div class="dash-user-data">
					<div class="user-avatar mini-avatar">
						<a href="<?= SITE_URL ?>/profile/user/<?= $profile['slug'] ?>"><img src="<?= SITE_URL ?>/files/avatars/<?= $profile['meta']['avatar'] ?>" alt="" /></a>
					</div><!-- user-avatar -->
					<div class="user-info">
						<h3 class="user-name"><?= $display_name ?>
						<?php
						if($show_username){
							echo '<span class="hud-alt-username">('.$show_username.')</span>';
						}
						?>
						</h3>
						<div class="user-info-col user-info-left">
							<?php
							$this->includeView('inc/group-title', array('profile' => $profile));
							if(isset($profile['meta']['pop_score_cache'])){
								echo '<span class="user-rating" title="Total Proof of Participation Earned"><i class="fa fa-comment"></i> '.number_format(round($profile['meta']['pop_score_cache'])).' PoP</span>';
							}
							else{
								echo '<span class="user-rating" title="Total Proof of Participation Earned"><i class="fa fa-comment"></i> 0 PoP</span>';
							}
							if(isset($profile['meta']['poq_score_cache'])){
								echo '<span class="user-rating" title="Total Proof of Quality/Publication Earned"><i class="fa fa-thumbs-o-up"></i> '.number_format(round($profile['meta']['poq_score_cache'])).' PoQ</span>';
							}
							else{
								echo '<span class="user-rating" title="Total Proof of Quality/Publication Earned"><i class="fa fa-thumbs-o-up"></i> 0 PoQ</span>';
							}
							if(isset($profile['meta']['pov_score_cache'])){
								echo '<span class="user-rating" title="Total Proof of Value Earned"><i class="fa fa-star"></i> '.number_format(round($profile['meta']['pov_score_cache'])).' PoV</span>';
							}	
							else{
								echo '<span class="user-rating" title="Total Proof of Value Earned"><i class="fa fa-star"></i> 0 PoV</span>';
							}															
							?>
							
						</div><!-- user-info-left -->
						<div class="user-info-col user-info-right">
							<?php
							if($user AND $profile['userId'] != $user['userId']){
							?>
							<span class="user-profile-link"><strong><a href="<?= SITE_URL ?>/dashboard/account/messages/send?user=<?= urlencode($profile['username']) ?>" target="_blank"><i class="fa fa-envelope"></i> Send Message</a></strong></span>
							<?php
							}//endif
							?>
							<span class="user-status">
								Status: 
									<?php
									$custom_status_class = 'text-error';
									$status_title = 'Offline';
									$activeTime = strtotime($profile['lastActive']);
									$diff = time() - $activeTime;
									if($diff < 7200){
										if(isset($profile['meta']['custom_status'])){
											$status_title = ucfirst($profile['meta']['custom_status']);
											$custom_status_class = $statuses[$profile['meta']['custom_status']];;
										}
										else{
											$status_title = 'Online';
											$custom_status_class = 'text-success';
										}
									}
									$use_active = $profile['lastActive'];
									if($status_title == 'Offline'){
										$use_active = $profile['lastAuth'];
									}
									echo $status_title;
									?>	
								<i id="hud-status-circle" class="fa fa-circle <?= $custom_status_class ?>" title="Last active: <?= formatDate($use_active) ?>"></i>						
							</span>
							<?php
							if(isset($profile['affiliate']['userId'])){
								echo '<span class="user-sponsor">Referred by: <a href="'.SITE_URL.'/profile/user/'.$profile['affiliate']['slug'].'" target="_blank">'.$profile['affiliate']['username'].'</a></span>';
							}

							echo '<span class="user-rewards-address">
									Date registered: '.date('F jS, Y', strtotime($profile['regDate'])).'
								</span>';
							?>
						</div><!-- user-info-right -->
						<div class="clear"></div>
					</div>
				</div><!-- dash-user-data -->
				<div class="clear"></div>
			</div><!-- dashboard-hud -->		
	</div><!-- profile-hud -->
	<div class="profile-stats">
		<?php
		//get LTBC rank
		$ltb_rank = 0;
		if(isset($profile['meta']['pop_rank_cache'])){
			$ltb_rank = $profile['meta']['pop_rank_cache'];
		}
		
		$ltb_content_rank = 0;
		if(isset($profile['meta']['content_rank_cache'])){
			$ltb_content_rank = $profile['meta']['content_rank_cache'];
		}		

		//count published articles
		$num_published = $activity['blog']['count'];

		//get forum postcount
		$num_posts = $activity['forums']['count'];

		?>
		<div class="dash-home-stats">
			<ul class="stats-list">
				<li>
					<?php
					if($ltb_rank <= 0){
					?>
						<span class="stat-total null-stat">N/A</span>
					<?php
					}
					else{
					?>
						<span class="stat-total">#<?= number_format($ltb_rank) ?></span>
					<?php
					}//endif
					?>
					<span class="stat-name">LTBcoin Rank <span class="stat-extra">(Participation)</span></span>
				</li>
				<li>
					<?php
					if($ltb_rank <= 0){
					?>
						<span class="stat-total null-stat">N/A</span>
					<?php
					}
					else{
					?>
						<span class="stat-total">#<?= number_format($ltb_content_rank) ?></span>
					<?php
					}//endif
					?>
					<span class="stat-name">LTBcoin Rank <span class="stat-extra">(Content)</span></span>
				</li>				
				<li>
					<span class="stat-total" ><?= number_format($profile_views) ?></span>
					<span class="stat-name">Profile views</span>
				</li>
				<li>
					<a href="#activity">
						<span class="stat-total <?php if($num_published == 0){ echo 'null-stat'; } ?>"><?= number_format($num_published) ?></span>
						<span class="stat-name"><?= pluralize('Article', $num_published) ?> Published</span>
					</a>
				</li>
				<li>
					<a href="#activity">
						<span class="stat-total <?php if($num_posts == 0){ echo 'null-stat'; } ?>"><?= number_format($num_posts) ?></span>
						<span class="stat-name">Forum <?= pluralize('Post', $num_posts) ?></span>
					</a>
				</li>									
			</ul><!-- stats-list -->
			<div class="clear"></div>
		</div><!-- dash-home-stats -->
	</div><!-- profile-stats -->
	<?php

	$foundActivity = false;
	if($activity['forums'] AND $activity['forums']['posts']){
		$foundActivity = true;
	}
	
	if($activity['blog'] AND $activity['blog']['posts']){
		$foundActivity = true;
		
	}	
		
	if($page_mod == ''){
	?>
	<div class="profile-content"
	<?php
	if((trim($profile['email']) == '' OR intval($profile['showEmail']) === 0)
		AND
	  (count($profile['profile']) == 0)
	    AND 
	  (!$activity['tokenly'] OR !$activity['tokenly']['addresses'] OR count($activity['tokenly']['addresses']) == 0)
	 ){
		 echo 'style="display: none;"';
	 }
	?>
	>
		<?php
	}
		if(intval($profile['pubProf']) == 0 AND (!$user OR $user['userId'] != $profile['userId'])){
			echo '<p><strong>This users\' profile is private.</strong></p>';
		}
		else{
			if($page_mod == ''){
		?>
		<ul class="profile-info">
			<?php
			if(trim($profile['email']) != '' AND intval($profile['showEmail']) === 1){
			?>
				<li><strong>Email:</strong> <a href="mailto:<?= $profile['email'] ?>"><?= $profile['email'] ?></a></li>
			<?php
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
					$field['value'] = '<span class="companion-tip-button" data-address="'.$field['value'].'" data-label="'.$profile['username'].'" data-tokens="all"></span>'.$field['value'];
				}
				if($field['type'] == 'textarea'){
					echo '<li class="profile-area"><h4>'.$field['label'].':</h4>
													'.markdown($field['value']).'</li>';
					
				}
				else{
					echo '<li><strong>'.$field['label'].':</strong> '.autolink($field['value']).'</li>';
				}
			}
			if($activity['tokenly'] AND $activity['tokenly']['addresses']){
				echo '<li>
						<strong>Public Bitcoin Addresses:</strong>
						<ul class="public-address-list">';
				
				foreach($activity['tokenly']['addresses'] as $address){
					echo '<li>
							'.$address['address'].'
							<a href="https://blockscan.com/address/'.$address['address'].'" target="_blank" title="View on block explorer">
								<i class="fa fa-info-circle"></i>
							</a>
							<a href="#qr-'.$address['address'].'" class="fancy" target="_blank">
								<i class="fa fa-qrcode"  title="Show QR code"></i>
							</a>	
							<span class="companion-tip-button pockets-tip-button" data-address="'.$address['address'].'" data-label="'.$profile['username'].'" data-tokens="btc" data-color="icon"></span>							
								<div id="qr-'.$address['address'].'" style="display: none;">
								<p class="text-center">
									<img src="'.SITE_URL.'/qr.php?q='.$address['address'].'" alt="" style="width: 200px;" /><br>
									<strong><a href="bitcoin:'.$address['address'].'">'.$address['address'].'</a></strong><br>
									<a href="https://blockchain.info/address/'.$address['address'].'" target="_blank">Blockchain.info</a><br>
									<a href="https://chain.so/address/'.$address['address'].'" target="_blank">Chain.so</a><br>
									<a href="https://blockscan.com/address/'.$address['address'].'" target="_blank">Blockscan</a>
								</p>
								</div>					
							</li>';
				}
						
				echo '</ul></li>';
			}
			?>
		</ul>
		<a name="activity"></a>
		<div class="clear"></div>
	</div>
	<?php
	}//endif $page_mod == ''
		
	if($page_mod == ''){
	?>
	<h2 class="text-default">User Activity</h2>	
	<div class="profile-activity-cont">
	<?php
	}
	
	if(!$foundActivity){
		echo '<p>No account activity found.</p>';
	}	
	else{
		if($page_mod == ''){
	?>		
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
			<li class="<?= $forumActive ?>"><a href="#" class="tab" data-tab="forum-activity"><span class="pull-right"><i class="fa fa-comments"></i></span>Forums</a></li>
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
			<li class="<?= $blogActive ?>"><a href="#" class="tab" data-tab="blog-activity"><span class="pull-right"><i class="fa fa-pencil"></i></span> Blog Posts</a></li>
		<?php
		}
		?>
	</ul>
	<div class="clear"></div>
	<div class="profile-activity">
<?php
	}//endif $page_mod == ''

		if($activity['forums'] AND $activity['forums']['posts'] AND ($page_mod == '' OR $page_mod == 'forum-only')){
?>
		<div class="ltb-data-tab" id="forum-activity" style="<?= $forumDisplay ?>">
			<h3><a href="<?= SITE_URL ?>/profile/user/<?= $profile['slug'] ?>/forum-posts"  class="text-success">Forum Posts</a></h3>
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
			$pager->addClass('paging');
			$curForumPage = 1;
			if(isset($_GET['page']) AND isset($_GET['t']) AND $_GET['t'] == 'forums'){
				$curForumPage = intval($_GET['page']);
			}
			echo $pager->display($activity['forums']['num_pages'], '?t=forums&page=', $curForumPage, '', '#activity');
			?>

		</div>
		<?php
		}

		if($activity['blog'] AND $activity['blog']['posts'] AND ($page_mod == '' OR $page_mod == 'blog-only')){
		?>
		<div class="ltb-data-tab" id="blog-activity" style="<?= $blogDisplay ?>">
			<h3><a href="<?= SITE_URL ?>/profile/user/<?= $profile['slug'] ?>/blog-posts"  class="text-success">Blog Posts</a></h3>
			<ul>
				<li><strong>Posts Authored:</strong> <?= number_format($activity['blog']['written']) ?></li>
				<li><strong>Posts Contributed To:</strong> <?= number_format($activity['blog']['contribs']) ?></li>
			</ul>
			<?php
			if($activity['blog'] AND $activity['blog']['posts']){
				?>
				<ul class="blog-list list">
				<?php

				$settings = new \App\CMS\Settings_Model;
				$maxChars = $settings->getSetting('blog-excerptChars');
				$tca = new \App\Tokenly\TCA_Model;
				if(!$maxChars){
					$maxChars = 250;
				}
				$blogApp = get_app('blog');
				$postModule = get_app('blog.blog-post');
				$catModule = get_app('blog.blog-category');
				$profileModule = get_app('profile.user-profile');

				$imagePath = SITE_PATH.'/files/blogs';
				foreach($activity['blog']['posts'] as $post){
					$postTCA = $tca->checkItemAccess($user, $postModule['moduleId'], $post['postId'], 'blog-post');
					foreach($post['categories'] as $cat){
						$catTCA = $tca->checkItemAccess($user, $catModule['moduleId'], $cat['categoryId'], 'blog-category');
						if(!$catTCA){
							continue 2;
						}
					}
					if(!$postTCA){
						
						continue;
					}

					$getIndex = $settings->getAll('page_index', array('itemId' => $post['postId'], 'moduleId' => 28, 'siteId' => $site['siteId']));

					if($getIndex AND count($getIndex) > 0){
						$post['url'] = SITE_URL.'/'.$getIndex[count($getIndex) - 1]['url'];

					}
					else{
						$post['url'] = SITE_URL.'/'.$blogApp['url'].'/post/'.$post['url'];
					}
					
					
					$authorTCA = $tca->checkItemAccess($user, $profileModule['moduleId'], $post['author']['userId'], 'user-profile');
					
					$displayName = $post['author']['username'];
					if(isset($post['author']['profile']['real-name']) AND trim($post['author']['profile']['real-name']['value']) != ''){
						$displayName =  $post['author']['profile']['real-name']['value'];
					}
					if($authorTCA){
						$avImage = $post['author']['avatar'];
						if(!isExternalLink($post['author']['avatar'])){
							$avImage = SITE_URL.'/files/avatars/'.$post['author']['avatar'];
						}
						$avImage = '<img src="'.$avImage.'" alt="" />';
						$avImage = '<span class="mini-avatar inline">'.$avImage.'</span>';			
						$displayName = '<a href="'.SITE_URL.'/profile/user/'.$post['author']['slug'].'">'.$avImage.' '.$displayName.'</a>';
					}
					
					if($post['formatType'] == 'markdown'){
						$post['excerpt'] = markdown($post['excerpt']);
						$post['content'] = markdown($post['content']);
					}
				?>
					<li>

			<div class="blog-extra">
				<?php
				if(trim($post['coverImage']) != '' AND file_exists($imagePath.'/'.$post['coverImage'])){
					echo '<div class="blog-image"><a href="'.$post['url'].'"><img src="'.SITE_URL.'/files/blogs/'.$post['coverImage'].'" alt="" /></a></div>';
				}
				?>
				<?php
				if(isset($post['tip-address']) OR (isset($post['author']['profile']['bitcoin-address']) AND trim($post['author']['profile']['bitcoin-address']['value']) != '')){
					if(isset($post['tip-address'])){
						$btcAddress = $post['tip-address'];
					}
					else{
						$btcAddress = $post['author']['profile']['bitcoin-address']['value'];
					}
				?>
				<div class="blog-tipping">
					<a href="#<?= $post['postId'] ?>-btc-tip" class="fancy" target="_blank"><i class="fa fa-btc" title="Tip this post"></i></a>	
					<span class="companion-tip-button pockets-payment-button" data-color="icon" data-address="<?= $btcAddress ?>" data-label="<?= $post['author']['username'] ?> <br> (<?= $post['title'] ?>)" data-tokens="all"></span>				
					<div id="<?= $post['postId'] ?>-btc-tip" style="display: none;">
						<div class="text-center">
							<h3><a href="bitcoin:<?= $btcAddress ?>">Tip <?= $post['author']['username'] ?></a></h3>
							<h4><?= $btcAddress ?></h4>
							<h5><a href="https://blockscan.com/address/<?= $btcAddress ?>" target="_blank">(blockscan)</a></h5>
							<p>
								<img src="<?= SITE_URL ?>/qr.php?q=<?= $btcAddress ?>" alt="" />
							</p>
						</div>
					</div>
				</div>
				<?php
				}//endif
				?>		
				<div class="blog-social">
					<?php
					$shareURL = SITE_URL.'/'.$app['url'].'/post/'.$post['url'];
					?>
					<a href="http://www.reddit.com/submit?url=<?= $shareURL ?>" target="_blank"><img src="<?= THEME_URL ?>/images/reddit-black.png" title="Post on Reddit" /></a>
					<a href="http://www.facebook.com/sharer.php?u=<?= $shareURL ?>" target="_blank"><img src="<?= THEME_URL ?>/images/facebook-black.png" title="Share this Post" /></a>
					<a href="http://twitter.com/share?text=<?= urlencode($post['title']) ?>&url=<?= $shareURL ?>" target="_blank"><img src="<?= THEME_URL ?>/images/twitter-black.png" title="Tweet this Post" /></a>
					<a href="https://plus.google.com/share?url=<?= $shareURL ?>" target="_blank"><img src="<?= THEME_URL ?>/images/gplus-black.png" title="+1 this Post" /></a>
				</div>
				<?php
					$post['commentCount'] = intval($post['commentCount']);
				?>
				<div class="clear"></div>
				<div class="blog-commentCount">
					<a href="<?= $post['url'] ?>#disqus_thread"><i class="fa fa-comments"></i> <?= $post['commentCount'] ?> <?= pluralize('Comment', $post['commentCount'], true) ?></a><br>
					<span><i class="fa fa-eye"></i> <?= number_format($post['views']) ?> views</span>
				</div>		
				<div class="blog-cats">
					<?php
					if(count($post['categories']) > 0){
						echo 'Categories: ';
						echo '<span>';
						$catList = array();
						foreach($post['categories'] as $cat){
							$catList[] =  '<a href="'.SITE_URL.'/'.$app['url'].'/category/'.$cat['slug'].'">'.$cat['name'].'</a>';
						}
						echo join(', ', $catList);
						echo '</span>';
					}
					?>
				</div>
			</div>		
			<div class="blog-list-info">
				<h2 class="post-title"><a href="<?= $post['url'] ?>"><?= $post['title'] ?></a></h2>
				<div class="blog-date">
					<?= date('F jS, Y', strtotime($post['publishDate'])) ?> by 
					<?= $displayName ?>
				</div>
				<div class="blog-excerpt">
					<?php
					if(isset($category) AND $category['slug'] == 'uncoinventional-living'){
						echo $post['content'];
					}
					else{
						if(trim($post['excerpt']) == ''){
							$post['excerpt'] = shortenMsg($post['content'], 750);
						}
						echo $post['excerpt'];
					}
					?>
					<a href="<?= $post['url'] ?>" class="blog-more">Read More <i class="fa fa-chevron-right"></i></a>
				</div>
			</div><!-- blog-list-info -->
			<div class="clear"></div>

					</li>
				<?php
				}//endforeach
				?>
				</ul>				
				<?php
				$blogPager = new \UI\Pager;
				$blogPager->addClass('paging');
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
		
				if($page_mod == ''){
				?>
				</div><!-- profile-activity -->
				<?php
				}//endif $page_mod == ''
			}//endif
				if($page_mod == ''){
				?>
				</div><!-- profile-activity-cont -->
				<?php
				}
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
			$(this).parent().parent().find('.tab').parent().removeClass('active');
			$(this).parent().addClass('active');
		});		
		
	});
</script>
