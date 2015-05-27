<div class="home-sidebar">
	<div>
		<?= $this->displayBlock('home-sidebar') ?>
	</div>
</div>
<div class="home-posts-cont">
	<?php
		$tca = new \App\Tokenly\TCA_Model;
		$profileModule = $tca->get('modules', 'user-profile', array(), 'slug');
		$postModule = $tca->get('modules', 'blog-post', array(), 'slug');
		$catModule = $tca->get('modules', 'blog-category', array(), 'slug');
		$title = 'Recent Posts';
		$catModel = new \App\Blog\Category_Model;
		$settings = new \App\CMS\Settings_Model;
		$meta = new \App\Meta_Model;
		$blogApp = $meta->get('apps', 'blog', array(), 'slug');
		$blogMeta = $meta->appMeta($blogApp['appId']);
		$postLimit = $blogMeta['postsPerPage'];
		$commentsEnabled = $settings->getSetting('blog-commentsEnabled');
		if(!$postLimit){
			$postLimit = 12;
		}

		$posts = $catModel->getHomePosts($site['siteId'], $postLimit);
		$numPages = $catModel->getHomePages($site['siteId'], $postLimit);
		$app = $catModel->get('apps', 'blog', array(), 'slug');

	echo '<ul class="home-posts">';
	if(count($posts) > 0){
		foreach($posts as $post){
			if((trim($post['coverImage']) == '')){
				continue;
			}
			
			$postTCA = $tca->checkItemAccess($user, $postModule['moduleId'], $post['postId'], 'blog-post');
			if(!$postTCA){
				continue;
			}
			
			foreach($post['categories'] as $cat){
				$catTCA = $tca->checkItemAccess($user, $catModule['moduleId'], $cat['categoryId'], 'blog-category');
				if(!$catTCA){
					continue 2;
				}
			}

			$class = '';
			if(isset($post['soundcloud-url'])){
				$class .= ' show';
				
			}
			/*if($post['featured'] == 1){
				$class .= ' featured';
			}*/
			$avatar = '';
			$author = $post['author'];
			
			$authorTCA = $tca->checkItemAccess($user, $profileModule['moduleId'], $author['userId'], 'user-profile');
			
			$avImage = $author['avatar'];
			if(!isExternalLink($author['avatar'])){
				$avImage = SITE_URL.'/files/avatars/'.$author['avatar'];
			}
			$avImage = '<img src="'.$avImage.'" alt="" />';
			if($authorTCA){
				$avImage = '<a href="'.$data['site']['url'].'/profile/user/'.$author['slug'].'">'.$avImage.'</a>';
			}
			$avatar = '<span class="circle-avatar">'.$avImage.'</span>';
			
			?>
			<li class="<?= $class ?>">
				<div class="post-pic">
					<a href="<?= SITE_URL ?>/blog/post/<?= $post['url'] ?>">
					<?php

					if(trim($post['coverImage']) != ''){
						echo '<img src="'.SITE_URL.'/files/blogs/'.$post['coverImage'].'" alt="" />';
					}

            
					?>
					</a>
				</div>
				<div class="post-author">
					<div class="post-inner">
						<?php
						if(!isset($post['author']['profile']['real-name']) OR trim($post['author']['profile']['real-name']['value']) == ''){
							$post['author']['profile']['real-name'] = array('value' => $post['author']['username']);
						}
						$authorLink = $post['author']['profile']['real-name']['value'];
						if($authorTCA){
							$authorLink = '<a href="'.SITE_URL.'/profile/user/'.$post['author']['slug'].'">'.$authorLink.'</a>';
						}
						?>
							<?= $avatar ?><?= $authorLink ?>
					</div>
				</div>
				<div class="post-title">
					<div class="post-inner">
						<span class="post-date"><?= date('F d, Y', strtotime($post['publishDate'])) ?></span>
						<h3><a href="<?= SITE_URL ?>/blog/post/<?= $post['url'] ?>"><?= $post['title'] ?></a></h3>
					</div>
				</div>

			</li>
			<?php
			
		}
	}
	echo '</ul>';

	//include(SITE_PATH.'/themes/views/Blog/list.php');
	?>
	<div class="clear"></div>
	<div class="home-more">
		<h3><a href="<?= SITE_URL ?>/blog?page=2">VIEW MORE</a></h3>
	</div>
	<div class="clear"></div>
</div>
