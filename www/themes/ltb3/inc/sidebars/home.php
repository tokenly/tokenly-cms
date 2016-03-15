<?php
$model = new \App\Forum\Post_Model;
$meta = new \App\Meta_Model;
$tca = new \App\Tokenly\TCA_Model;

$forum_app = get_app('forum');
$forum_meta = $meta->appMeta($forum_app['appId']);

$forum_recent_file = SITE_BASE.'/data/forum-recent.json';
$forum_threads = false;
$time = time();
if(file_exists($forum_recent_file)){
	$forum_recent = json_decode(@file_get_contents($forum_recent_file), true);
	if(is_array($forum_recent) AND isset($forum_recent['last_update'])){
		$diff = $time - $forum_recent['last_update'];
		if($diff < 600){
			$forum_threads = $forum_recent['threads'];
		}
	}
}
if(!$forum_threads){
	$forum_api = new \App\API\V1\Forum_Model;
	$forum_threads = $forum_api->getThreadList(array('user' => $user, 'site' => currentSite()));
	@file_put_contents($forum_recent_file, json_encode(array('last_update' => $time, 'threads' => $forum_threads)));
}

$disqus = new \API\Disqus;
$disqusPosts = $disqus->getRecentPosts();

$sidebar_threads = array();

if(is_array($forum_threads) AND isset($forum_threads['threads'])){
	foreach($forum_threads['threads'] as $f_thread){
		$item = array();
		$item['title'] = $f_thread['title'];
		$post_page = 0;
		$andPage = '';
		if(isset($f_thread['mostRecent']['postId'])){
			$post_page = $model->getPostPage($f_thread['mostRecent']['postId'], $forum_meta['postsPerPage']);
		}
		if($post_page > 1){
			$andPage = '?page='.$post_page;
		}
		if(!isset($f_thread['postId'])){
			$f_thread['postId'] = 0;
		}		
		$item['link'] = SITE_URL.'/forum/post/'.$f_thread['url'].$andPage.'#post-'.$f_thread['postId'];
		$item['author'] = $f_thread['author'];
		$item['content'] = $f_thread['content'];
		$item['time'] = strtotime($f_thread['postTime']);
		if(isset($f_thread['mostRecent']['postId'])){
				$item['author'] = $f_thread['mostRecent']['author'];
				$item['content'] = $f_thread['mostRecent']['content'];
				$item['time'] = strtotime($f_thread['mostRecent']['postTime']);
		}
		$item['author']['link'] = SITE_URL.'/profile/user/'.$item['author']['slug'];
		$item['type'] = 'thread';
		$sidebar_threads[] = $item;
	}
}

if(is_array($disqusPosts)){
	date_default_timezone_set('UTC');
	$postModule = get_app('blog.blog-post');
	$catModule = get_app('blog.blog-category');
	foreach($disqusPosts as $d_thread){
		$item = array();
		
		$raw_url = explode('/', explode('#', $d_thread['url'])[0]);
		$raw_url = $raw_url[count($raw_url) - 1];
		$get_post = $model->get('blog_posts', $raw_url, array('postId'), 'url');
		if($get_post){
			$checkTCA = $tca->checkItemAccess($this->data['user'], $postModule['moduleId'], $get_post['postId'], 'blog-post');	
			if(!$checkTCA){
				continue;
			}			
			
			$getCats = $model->getAll('blog_postCategories', array('postId' => $get_post['postId']));
			$cats = array();
			foreach($getCats as $cat){
				$getCat = $model->get('blog_categories', $cat['categoryId']);
				$cats[] = $getCat;
			}
			
			foreach($cats as $cat){
				$catTCA = $tca->checkItemAccess($this->data['user'], $catModule['moduleId'], $cat['categoryId'], 'blog-category');
				$blogTCA = $tca->checkItemAccess($this->data['user'], $catModule['moduleId'], $cat['blogId'], 'multiblog');
				if(!$catTCA OR !$blogTCA){
					continue 2;
				}
			}			
		}
		
		$item['title'] = $d_thread['thread']['clean_title'];
		$item['link'] = $d_thread['url'];
		$item['author'] = array();
		$item['author']['username'] = $d_thread['author']['name'];
		$item['author']['avatar'] = $d_thread['author']['avatar']['permalink'];
		$item['author']['link'] = $d_thread['author']['profileUrl'];
		if(isset($d_thread['author']['url']) AND trim($d_thread['author']['url']) != ''){
			$item['author']['link'] = $d_thread['author']['url'];
		}
		$item['content'] = $d_thread['raw_message'];
		$item['time'] = strtotime($d_thread['createdAt']);
		$item['type'] = 'comment';
		$sidebar_threads[] = $item;
	}
	date_default_timezone_set('America/Los_Angeles');
	
}

aasort($sidebar_threads, 'time');
$sidebar_threads = array_reverse($sidebar_threads);

$show_threads = false;
if(count($sidebar_threads) > 0){
	$show_threads = true;
}


?>
						<div class="sidebar-inner-content">
							<div style="margin-bottom: 20px;">
								<?php
								$ad2 = $this->displayTag('DISPLAY_ADSPACE', array('slug' => 'homepage-sidebar-ad-2'));
								if($ad2){
									echo '<div style="margin-bottom: 20px;">'.$ad2.'</div>';
								}
								?>								
								<div style="margin-bottom: 20px;">
									<?= $this->displayTag('DISPLAY_ADSPACE', array('slug' => 'homepage-sidebar')) ?>
								</div>
								<?= $this->displayTag('DISPLAY_ADSPACE', array('slug' => 'homepagesingle125x125')) ?>
							</div>
							<div class="search-cont pull-right">
								<a href="#" class="search-icon" title="Search website"><i class="fa fa-search"></i></a>
							</div><!-- search-cont -->							
							<h2>LTB Community <span>Recent Posts</span></h2>
							<div class="clear"></div>
							<?php
							if(!$show_threads){
								
								
							}
							else{
							?>
							<ul class="recent-posts">
								<?php
								$limit = 6;
								$num = 0;
								foreach($sidebar_threads as $thread){
									if($num >= $limit){
										break;
									}
									$num++;
									?>
								<li>
									<div class="post-title">
										<span class="post-date" title="<?= date('F jS, Y \a\t H:i a', $thread['time']) ?>"><?= human_time_since($thread['time'], false, true, 'floor') ?></span>
										<a href="<?= $thread['author']['link'] ?>" class="user-link"><span class="mini-avatar"><img src="<?= $thread['author']['avatar'] ?>" alt="" /></span><span class="user-name"><?= $thread['author']['username'] ?></span></a>
									</div>
									<div class="post-content">
										<p>
											<?= shortenMsg(strip_tags(remove_tags(markdown($thread['content']), array('blockquote'))), 250) ?>
										</p>
										<div class="post-thread-title">
											<a href="<?= $thread['link'] ?>" title="<?= str_replace('"', '\'', $thread['title']) ?>" class="view-post">
												<span class="pull-right text-right">View Thread <i class="fa fa-mail-forward"></i></span>
											</a>
												<strong><?= $thread['title'] ?></strong>
											<div class="clear"></div>
										</div>										
									</div>

								</li>
									<?php
								}
								?>
							</ul><!-- recent-posts -->
							<div class="clear"></div>
							<div class="text-right">
								<a href="<?= SITE_URL ?>/forum/board/all" class="enter-forum">Enter <span>Forums</span></a>
							</div>
							<?php
							}//endif
							?>
						</div><!-- sidebar-inner-content -->
