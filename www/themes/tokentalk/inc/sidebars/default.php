<?php
$model = new \App\Forum\Post_Model;
$meta = new \App\Meta_Model;
$tca = new \App\Tokenly\TCA_Model;
$board_model = new \App\Forum\Board_Model;

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
	$forum_threads = $forum_api->getThreadList(array('user' => $user, 'site' => currentSite()), true);
	@file_put_contents($forum_recent_file, json_encode(array('last_update' => $time, 'threads' => $forum_threads)));
}



$sidebar_threads = array();

if(is_array($forum_threads) AND isset($forum_threads['threads'])){
	foreach($forum_threads['threads'] as $f_thread){
        $checkTCA = $board_module->checkTopicTCA($user, $f_thread);
        if(!$checkTCA){
            continue;
        }                
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


aasort($sidebar_threads, 'time');
$sidebar_threads = array_reverse($sidebar_threads);

$show_threads = false;
if(count($sidebar_threads) > 0){
	$show_threads = true;
}


?>
						<div class="sidebar-inner-content">
							<div class="search-cont pull-right">
								<a href="#" class="search-icon" title="Search website"><i class="fa fa-search"></i></a>
							</div><!-- search-cont -->							
							<h2>TokenTalk Community <span>Recent Posts</span></h2>
							<div class="clear"></div>
							<?php
							if(!$show_threads){
								echo '<p>No posts found</p>';
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
