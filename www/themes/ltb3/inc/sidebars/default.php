<?php
$model = new \App\Forum\Post_Model;
$meta = new \App\Meta_Model;
$forum_app = get_app('forum');
$forum_meta = $meta->appMeta($forum_app['appId']);
$andAuth = '';
if(isset($_SESSION['accountAuth'])){
	$andAuth = '?x-auth='.$_SESSION['accountAuth'];
}
$forum_threads = json_decode(file_get_contents(SITE_URL.'/api/v1/forum/threads'.$andAuth), true);
$show_threads = false;
if(is_array($forum_threads) AND isset($forum_threads['threads'])){
	$show_threads = true;
}
?>
						<div class="sidebar-inner-content">
							<div style="margin-bottom: 20px;">
								<?php
								$ad2 = $this->displayTag('DISPLAY_ADSPACE', array('slug' => 'default-sidebar-2'));
								if($ad2){
									echo '<div style="margin-bottom: 20px;">'.$ad2.'</div>';
								}
								?>
								<?= $this->displayTag('DISPLAY_ADSPACE', array('slug' => 'default-sidebar')) ?>							
							</div>
							<div class="search-cont pull-right">
								<a href="#" class="search-icon" title="Search website"><i class="fa fa-search"></i></a>
							</div><!-- search-cont -->							
							<h2>LTB Forums <span>Recent Posts</span></h2>
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
								foreach($forum_threads['threads'] as $thread){
									if($num >= $limit){
										break;
									}
									$num++;
									$post_page = 0;
									$andPage = '';
									if(isset($thread['mostRecent']['postId'])){
										$post_page = $model->getPostPage($thread['mostRecent']['postId'], $forum_meta['postsPerPage']);
									}
									if($post_page > 1){
										$andPage = '?page='.$post_page;
									}
									$thread_preview = $thread;
									$thread_preview['postId'] = 0;
									if(isset($thread['mostRecent']['postId'])){
										$thread_preview = $thread['mostRecent'];
									}
									?>
								<li>
									<div class="post-title">
										<span class="post-date" title="<?= date('F jS, Y \a\t H:i a', strtotime($thread_preview['postTime'])) ?>"><?= human_time_since($thread_preview['postTime'], false, true, 'floor') ?></span>
										<a href="<?= SITE_URL.'/profile/user/'.$thread_preview['author']['slug'] ?>" class="user-link"><span class="mini-avatar"><img src="<?= $thread_preview['author']['avatar'] ?>" alt="" /></span><span class="user-name"><?= $thread_preview['author']['username'] ?></span></a>
									</div>
									<div class="post-content">
										<p>
											<?= shortenMsg(strip_tags(remove_tags(markdown($thread_preview['content']), array('blockquote'))), 250) ?>
										</p>
										<div class="post-thread-title">
											<a href="<?= SITE_URL.'/forum/post/'.$thread['url'].$andPage ?>#post-<?= $thread_preview['postId'] ?>" title="<?= str_replace('"', '\'', $thread['title']) ?>" class="view-post">
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
