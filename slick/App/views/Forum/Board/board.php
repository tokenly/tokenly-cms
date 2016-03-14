<h1 class="large"><?= $app['meta']['forum-title'] ?></h1>
<hr>
<?php
$model = new \Core\Model;
$boardToken = '';
if($isAll){
	$board = array('name' => $title, 'description' => '', 'slug' => $slug);
}
else{
	$access_token = $model->getAll('forum_boardMeta', array('boardId' => $board['boardId'], 'metaKey' => 'access_token'));
	if(count($access_token) > 0){
		$access_token = $access_token[0];
		$getAsset = $model->get('xcp_assetCache', $access_token['value'], array(), 'asset');
		if($getAsset){
			$boardImage = '';
			if(trim($getAsset['image']) != ''){
				$boardImage = '<img class="board-img" src="'.$site['url'].'/files/tokens/'.$getAsset['image'].'" alt="" title="'.$getAsset['asset'].'" /><br>';
			}
			$boardToken = '<div class="board-image-cont"><a href="#asset-info" class="fancy">'.$boardImage.$getAsset['asset'].'</a></div>';
			$boardToken .= '<div id="asset-info" style="display: none;">'.markdown($getAsset['description']);
			if(trim($getAsset['link']) != ''){
				$boardToken .= '<p><strong>More Info:</strong> <a href="'.$getAsset['link'].'" target="_blank">'.$getAsset['link'].'</a></p>';
			}
			$boardToken .= '</div>';
		}
	}
}
echo $boardToken;
?>

<?php

// subscribe to board
if(!$isAll AND $user){
	$subscribeText = 'Subscribe to Board';
	$subscribeClass = 'subscribe';

	$model = new \Core\Model;
	$getSubs = $model->getAll('board_subscriptions',
				array('userId' => $user['userId'], 'boardId' => $board['boardId']));
	if(count($getSubs) > 0){
		$subscribeClass = 'unsubscribe';
		$subscribeText = 'Unsubscribe from Board';
	}

	echo '<p style="float: right; vertical-align: top; width: 190px; text-align: center; clear: right;">';
	echo '<a href="#" class="board-control-link '.$subscribeClass.'">'.$subscribeText.'</a>';		
	echo '</p>';

	if(!$isAll AND $user AND $perms['canPostTopic']){
		echo '<div class="board-controls">
				<ul>
					<li style="width: 190px; text-align: center;"><a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/'.$board['slug'].'/post">Post New Topic</a></li>
				</ul>
			  </div>';
		
	}
}

?>
<?php
if($isAll AND $board['slug'] == 'all'){
?>
<p class="pull-right">
	<a href="#" class="filter-change">Filter Boards [+]</a>
</p>
<div id="board-filter" style="display: none;">
	<p>
		Choose what boards to show by default.
	</p>
	<label>Boards:</label>
	<form action="" method="post">
	<div class="Slick_UI_CheckboxList checkboxList">
		<?php
	
			$model = new \Core\Model;
			$getfCats = $model->getAll('forum_categories', array('siteId' => $site['siteId']), array(), 'rank', 'asc');
			$tca = new \App\Tokenly\TCA_Model;
			foreach($getfCats as $fcat){
				$checkTCA = $tca->checkItemAccess($user, $module['moduleId'], $fcat['categoryId'], 'category');
				if(!$checkTCA){
					continue;
				}
				$getfBoards = $model->getAll('forum_boards', array('categoryId' => $fcat['categoryId'], 'active' => 1), array(), 'rank', 'asc');
				if(count($getfBoards) > 0){
					echo '<div class="clear"></div><h4>'.$fcat['name'].'</h4>';
					foreach($getfBoards as $fboard){
						$checkTCA = $tca->checkItemAccess($user, $module['moduleId'], $fboard['boardId'], 'board');
						if(!$checkTCA){
							continue;
						}						
						$checked = 'checked';
						if(isset($boardFilters) AND count($boardFilters['antifilters']) > 0 AND in_array($fboard['boardId'], $boardFilters['antifilters'])){
							$checked = '';
						}
						echo '<input type="checkbox" id="b-'.$fboard['boardId'].'" name="boardFilters[]" '.$checked.' value="'.$fboard['boardId'].'" />';
						echo '<label for="b-'.$fboard['boardId'].'">'.$fboard['name'].'</label>';
					}
				}
			}
		?>
		</div>
		<input type="submit" value="Save" />
	</form>	
</div>

<?php
}//endif
?>
<h2><?= $board['name'] ?></h2>
<?php
if(trim($board['description']) != ''){
	echo '<div class="board-description">'.\App\Page\View_Model::parsePageTags(markdown($board['description'])).'</div>';
}
if(!$isAll){
	if(count($moderators) > 0){
		echo '<p><strong>Moderators:</strong> ';
		$modList = array();
		foreach($moderators as $mod){
			$modList[] = '<a href="'.SITE_URL.'/profile/user/'.$mod['slug'].'">'.$mod['username'].'</a>';
		}
		echo join(', ', $modList);
		echo '</p>';
	}
}
?>
<div class="board-topics">
	<?php
	$topic_list = array('sticky' => array(), 'topic' => $topics);
	if(count($stickies) > 0 AND (!isset($_GET['page']) OR $_GET['page'] == 1)){
		$stickyText = 'Hide';
		$stickyClass = 'collapse';
		if($isAll){
			$stickyText = 'Show';
			$stickyClass = '';
		}
		$topic_list['sticky'] = $stickies;
	?>
	<div class="clear"></div>
	<a href="#" class="sticky-trigger <?= $stickyClass ?>"><?= $stickyText ?> Sticky Posts</a>
	<?php
	}
	if(count($topics) == 0){
		echo '<p>No discussions found</p>';
	}
	else{
		$userId = 0;
		$viewed_topics = array();
		if($user){
			$userId = $user['userId'];
			if(isset($user['meta']['viewed_forum_replies'])){
				$viewed_topics = json_decode($user['meta']['viewed_forum_replies'], true);
				if(!is_array($viewed_topics)){
					$viewed_topics = array();
				}
			}
		}		
		$tokenly = get_app('tokenly');
		echo '<ul class="forum-thread-list">';
		foreach($topic_list as $t_type => $topics){
			foreach($topics as $k => $topic){
				$avImage = $topic['author']['avatar'];
				if(trim($topic['author']['real_avatar']) == ''){
					$avImage = SITE_URL.'/files/avatars/default.jpg';
				}
				else{				
					if(!isExternalLink($topic['author']['avatar'])){
						$avImage = SITE_URL.'/files/avatars/'.$topic['author']['avatar'];
					}
				}
				$avImage = '<span class="mini-avatar"><img src="'.$avImage.'" alt="" /></span>';
				
				$boardClass = '';
				if($topic['board']['categoryId'] == $tokenly['meta']['tca-forum-category']){
					$boardClass = 'tca-board';
				}
				
				$topicIcon = '';
				$item_class = 'topic_item';
				if($t_type == 'sticky' OR $topic['sticky'] == 1){
					$topicIcon = '<i class="fa fa-thumb-tack"></i> ';
					$item_class = 'sticky_item';
				}
				
				?>
				
				<li class="<?= $item_class ?>">
					<strong class="thread-title"><a href="<?= SITE_URL.'/forum/post/'.$topic['url'] ?>" title="<?= str_replace('"', '\'', shortenMsg($topic['content'], 250)) ?>"><span class="pull-right"><i class="fa fa-chevron-right"></i></span><?= $topicIcon ?><?= $topic['title'] ?></a></strong>
					<div class="thread-origin">
						Posted <span title="<?= formatDate($topic['postTime']) ?>"><?= human_time_since($topic['postTime'], false, false, 'round') ?> ago</span>
						by <a href="<?= SITE_URL ?>/profile/user/<?= $topic['author']['slug'] ?>"><?= $avImage.' '.$topic['author']['username'] ?></a>
						<?php
						if(!isset($board['boardId'])){
						?>
						to <a href="<?= SITE_URL ?>/forum/board/<?= $topic['board']['slug'] ?>" class="<?= $boardClass ?>"><?= $topic['board']['name'] ?></a>
						<?php
						}//endif
						?>
					</div>
					<div class="thread-info">
						<span class="thread-comment-count">
							<?php
							$new_replies = '';
							if(isset($viewed_topics[$topic['topicId']])){
								$prev_replies = intval($viewed_topics[$topic['topicId']]);
								$cur_replies = intval($topic['numReplies']);
								$reply_diff = $cur_replies - $prev_replies;
								if($reply_diff > 0){
									$new_replies = ' <span class="new-replies">('.number_format($reply_diff).' new)</span>';
								}
								
							}
							?>
							<a href="<?= SITE_URL ?>/forum/post/<?= $topic['url'] ?>"><i class="fa fa-comments"></i> <?= number_format($topic['numReplies']) ?> <?= pluralize('comment', $topic['numReplies'], true) ?><?= $new_replies ?></a>
						</span>
						<span class="thread-reader-count">
							<strong><i class="fa fa-eye"></i> <?= number_format($topic['views']) ?> <?= pluralize('reader', $topic['views'], true) ?></strong>
						</span>
						<?php
						if($topic['mostRecent'] AND isset($topic['mostRecent']['author'])){
							if(!isset($topic['numPages'])){
								$topic['numPages'] = 1;
							}
						?>
						<span class="thread-most-recent">
							<a href="<?= SITE_URL ?>/forum/post/<?= $topic['url'] ?>?page=<?= $topic['numPages'] ?>#post-<?= $topic['mostRecent']['postId'] ?>" title="<?= $topic['mostRecent']['author']['username'] ?> says: <?= str_replace('"', '\'', shortenMsg($topic['mostRecent']['content'], 250)) ?>"><i class="fa fa-mail-forward"></i> View most recent</a>
						</span>
						<span class="thread-recent-user">
							<?php
							$recentAvImage = $topic['mostRecent']['author']['avatar'];
							if(trim($topic['mostRecent']['author']['real_avatar']) == ''){
								$recentAvImage = SITE_URL.'/files/avatars/default.jpg';
							}
							else{				
								if(!isExternalLink($topic['mostRecent']['author']['avatar'])){
									$recentAvImage = SITE_URL.'/files/avatars/'.$topic['mostRecent']['author']['avatar'];
								}
							}
							$recentAvImage = '<span class="mini-avatar"><img src="'.$recentAvImage.'" alt="" /></span>';							
							?>
							<a href="<?= SITE_URL ?>/profile/user/<?= $topic['mostRecent']['author']['slug'] ?>"><?= $recentAvImage.' '.$topic['mostRecent']['author']['username'] ?></a>
						</span>
						<span class="thread-recent-time" title="Last post: <?= formatDate($topic['mostRecent']['postTime']) ?>">
							<i class="fa fa-clock-o"></i> <?= human_time_since($topic['mostRecent']['postTime'], false, false, 'round') ?> ago
						</span>
						<?php
						}//endif
						?>
						<?= $topic['paging'] ?>
					</div>
				</li>
				<?php
			}
		}
		echo '</ul>';

		if($numPages > 1){
			echo '<div class="board-paging paging">
					<strong>Pages:</strong>';
			for($i = 1; $i <= $numPages; $i++){
				$active = '';
				if((isset($_GET['page']) AND $_GET['page'] == $i) OR (!isset($_GET['page']) AND $i == 1)){
					$active = 'active';
				}
				echo '<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/'.$board['slug'].'?page='.$i.'" class="'.$active.'">'.$i.'</a> ';
			}
			echo '</div>';
		}
	}
	?>
	
	
</div>
<?php
if($isAll AND $board['slug'] == 'all'){
?>
<hr>
<a name="stats"></a>
<h3>Statistics</h3>
<?php
$onlineList = array();
foreach($onlineUsers as $oUser){
	$onlineList[] = $oUser['link'];
}
?>
<ul class="forum-stats">
	<li><strong>Total Posts:</strong> <?= $numTopics + $numReplies ?> <em>(<?= $numTopics ?> discussions, <?= $numReplies ?> replies)</em></li>
	<li><strong>Total Users:</strong> <?= $numUsers ?></li>
	<li><strong>Most Ever Online:</strong> <?= $mostOnline ?></li>
	<li><strong>Currently Online (<?= $numOnline ?>):</strong> <?= join(', ', $onlineList) ?></li>
	
</ul>
<?php
	
}
?>

<script type="text/javascript">
	$(document).ready(function(){
		$('.sticky-trigger').click(function(e){
			e.preventDefault();
			if($(this).hasClass('collapse')){
				$('.sticky_item').hide();
				$(this).removeClass('collapse');
				$(this).html('Show Sticky Posts');
			}
			else{
				$('.sticky_item').show();
				$(this).addClass('collapse');
				$(this).html('Hide Sticky Posts');
			}
		});
		
		$('.filter-change').click(function(e){
			e.preventDefault();
			if($(this).hasClass('collapse')){
				$('#board-filter').slideUp();
				$(this).removeClass('collapse');
				$(this).html('Filter Boards [+]');
			}
			else{
				$('#board-filter').slideDown();
				$(this).addClass('collapse');
				$(this).html('Filter Boards [-]');
			}
			
		});

		$('.content').delegate('.subscribe', 'click', function(e){
			e.preventDefault();
			var thisLink = $('.subscribe');
			var url = '<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/<?= $board['slug'] ?>/subscribe';
			$.post(url, function(data){
				if(typeof data.error != 'undefined'){
					alert(data.error);
					return false;
				}
				else{
					thisLink.html('Unsubscribe from Board');
					thisLink.addClass('unsubscribe');
					thisLink.removeClass('subscribe');
					
				}
			});
			
		});
		
		$('.content').delegate('.unsubscribe', 'click', function(e){
			e.preventDefault();
			var thisLink = $('.unsubscribe');
			var url = '<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/<?= $board['slug'] ?>/unsubscribe';
			$.post(url, function(data){
				if(typeof data.error != 'undefined'){
					alert(data.error);
					return false;
				}
				else{
					thisLink.html('Subscribe to Board');
					thisLink.addClass('subscribe');
					thisLink.removeClass('unsubscribe');
				}
			});
			
		});		
	});
</script>

