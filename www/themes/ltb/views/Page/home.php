<div class="home-sidebar">
	<div>
		<?= $this->displayBlock('home-sidebar') ?>
	</div>

</div>
<div class="home-posts-cont">
	<?php

		$title = 'Recent Posts';
		$catModel = new Slick_App_Blog_Category_Model;
		$settings = new Slick_App_Dashboard_Settings_Model;
		$meta = new Slick_App_Meta_Model;
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
			if((trim($post['image']) == '' AND $post['featured'] == 1)
                OR (trim($post['coverImage']) == '' AND $post['featured'] != 1)){
				continue;
			}

			$class = '';
			if(isset($post['soundcloud-url'])){
				$class .= ' show';
				
			}
			if($post['featured'] == 1){
				$class .= ' featured';
			}
			$avatar = '';
			$author = $post['author'];
			$avImage = $author['avatar'];
			if(!isExternalLink($author['avatar'])){
				$avImage = SITE_URL.'/files/avatars/'.$author['avatar'];
			}
			$avatar = '<span class="circle-avatar"><a href="'.$data['site']['url'].'/profile/user/'.$author['slug'].'"><img src="'.$avImage.'" alt="" /></a></span>';
			
			?>
			<li class="<?= $class ?>">
				<div class="post-pic">
					<a href="<?= SITE_URL ?>/blog/post/<?= $post['url'] ?>">
					<?php
                    if($post['featured'] == 1){
                        if(trim($post['image']) != ''){
                            echo '<img src="'.SITE_URL.'/files/blogs/'.$post['image'].'" alt="" />';
                        }
                    }
                    else{
                        if(trim($post['coverImage']) != ''){
                            echo '<img src="'.SITE_URL.'/files/blogs/'.$post['coverImage'].'" alt="" />';
                        }
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
						?>
							<?= $avatar ?><a href="<?= SITE_URL ?>/profile/user/<?= $post['author']['slug'] ?>"><?= $post['author']['profile']['real-name']['value'] ?></a>
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
