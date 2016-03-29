<h1 class="large"><?= $app['meta']['forum-title'] ?></h1>
<hr>
<div class="forum-thread">
<?php
$thisURL = SITE_URL.'/'.$app['url'].'/'.$module['url'].'/'.$topic['url'];


function checkUserTCA($userId, $profUserId)
{
	$tca = new \App\Tokenly\TCA_Model;
	$module = get_app('profile.user-profile');
	if(!$userId OR ($userId AND $userId != $profUserId)){
		$checkTCA = $tca->checkItemAccess($userId, $module['moduleId'], $profUserId, 'user-profile');
		if(!$checkTCA){
			return false;
		}
	}
	return true;
}

?>
<?php
if($user AND $topic['locked'] == 0){
?>
<p class="thread-top-controls">
	<?php if($perms['canPostReply']){ ?><a class="board-control-link" href="#post-reply">Post Reply</a><?php }//endif ?>
	<?php
	$subscribeText = 'Subscribe';
	$subscribeClass = 'subscribe';
	$model = new \Core\Model;
	$getSubs = $model->getAll('forum_subscriptions',
				array('userId' => $user['userId'], 'topicId' => $topic['topicId']));
	if(count($getSubs) > 0){
		$subscribeClass = 'unsubscribe';
		$subscribeText = 'Unsubscribe';
	}
	echo '<a href="#" class="board-control-link '.$subscribeClass.'">'.$subscribeText.'</a>';	
	?>
</p>
<?php
    }//endif
?>
<h2><?= $topic['title'] ?></h2>
<?php
if(isset($replyMessage) AND $replyMessage != ''){
	echo '<p class="error">'.$replyMessage.'</p>';
}
?>
<a name="top"></a>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/board/<?= $board['slug'] ?>" class="board-back-link"><i class="fa fa-mail-reply"></i> Back to <?= $board['name'] ?></a>
</p>
<div class="clear"></div>
<span class="pull-right">
	<a href="#bottom"><strong>Jump to bottom <i class="fa fa-chevron-down"></i></strong></a>
</span>
<div class="topic-paging paging">
	<?php
	if($numPages > 1){
		echo '<strong>Pages:</strong> ';
		for($i = 1; $i <= $numPages; $i++){
			$active = '';
			if((isset($_GET['page']) AND $_GET['page'] == $i) OR (!isset($_GET['page']) AND $i == 1)){
				$active = 'active';
			}
			echo '<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/'.$topic['url'].'?page='.$i.'" class="'.$active.'">'.$i.'</a>';
		}
	}
	?>
</div>
<h2 class="topic-heading">Comments</h2>
<?php
$time = time();
if($page == 1){
	
?>
<div class="thread-op">
	<?php
	$userId = 0;
	if($user){
		$userId = $user['userId'];
	}
	$checkUserTCA = checkUserTCA($userId, $topic['userId']);
	
	$avImage = $topic['author']['avatar'];
	if(trim($topic['author']['real_avatar']) == ''){
		$avImage = SITE_URL.'/files/avatars/default.jpg';
	}
	else{				
		if(!isExternalLink($topic['author']['avatar'])){
			$avImage = SITE_URL.'/files/avatars/'.$topic['author']['avatar'];
		}
	}
	$avImage = '<img src="'.$avImage.'" alt="" />';
	if($checkUserTCA){
		$avImage = '<a href="'.SITE_URL.'/profile/user/'.$topic['author']['slug'].'">'.$avImage.'</a>';
	}	
	
	$topicUsername = $topic['author']['username'];
	if($checkUserTCA){
		$topicUsername = '<a href="'.SITE_URL.'/profile/user/'.$topic['author']['slug'].'" target="_blank">'.$topicUsername.'</a>';
	}	
	?>
	<div class="op-author">
		<span class="post-username"><?= $topicUsername ?></span>
		<div class="profile-pic">
			<?= $avImage ?>
		</div>
		<div class="post-author-info">
			<?php
			$this->includeView('inc/group-title', array('profile' => $topic['author'], 'primary_only' => true));
			$use_status = '';
			$online_icon = 'fa-circle';
			$online_title = 'Offline';
			$activeTime = strtotime($topic['author']['lastActive']);
			$diff = $time - $activeTime;
			if($diff < 7200){
				if(isset($topic['author']['custom_status'])){
					switch($topic['author']['custom_status']){
						case 'away':
							$online_icon .= ' text-pending';
							$online_title = 'Away';							
							break;	
						case 'busy':
							$online_icon .= ' text-progress';
							$online_title = 'Busy';							
							break;												
						case 'offline':
							$online_icon .= ' text-error';
							$online_title = 'Offline';							
							break;					
						default:
							$online_icon .= ' text-success';
							$online_title = 'Online';							
							break;
					}
				}
				else{
					$online_icon .= ' text-success';
					$online_title = 'Online';				
				}
			}
			else{
				$online_icon .= ' text-error';
			}			
			
			$use_active = $topic['author']['lastActive'];
			if($online_title == 'Offline'){
				$use_active = $topic['author']['lastAuth'];
			}
			
			$use_status = '<span title="Last active: '.formatDate($use_active).'"><i class="fa fa-circle '.$online_icon.'"></i> <strong>'.$online_title.'</strong></span>';;
			?>
			<?= $use_status ?><br>
			<strong>Posts:</strong> <?= \App\Account\Home_Model::getUserPostCount($topic['userId']) ?>
			<?php
			if(isset($topic['author']['profile']['location'])){
				echo '<br><strong>Location:</strong> '.$topic['author']['profile']['location']['value'];
			}
			
			
			if($user AND $user['userId'] != $topic['userId']){
				if($checkUserTCA){
					echo '<br><a href="'.SITE_URL.'/dashboard/account/messages/send?user='.$topic['author']['slug'].'" target="_blank" class="send-msg-btn" title="Send private message"><i class="fa fa-envelope"></i> Message</a>';
				}
			}
			
			?>
			
		</div>
	</div>
	<div class="op-content">
		<div class="post-content" data-user-slug="<?= $topic['author']['slug'] ?>" data-message="<?= base64_encode($topic['content']) ?>">
			<?= markdown($topic['content']) ?>
		</div>
			<?php
			if(isset($topic['author']['profile']['forum-signature']['value'])){
				echo "		<div class=\"forum-sig\">\n";
				echo markdown($topic['author']['profile']['forum-signature']['value']);
				echo "		</div>\n";
			}
			?>
	</div>
	<div class="clear"></div>
	<span class="post-date">Posted on <?= formatDate($topic['postTime']) ?>
	<?php
	if($topic['editTime'] != null){
		echo '<br>Last Edited: '.formatDate($topic['editTime']);
	}
	?>
	</span>
	<div class="post-extras">
	<?php
	if($user AND $perms['canReportPost'] AND $topic['userId'] != $user['userId']){
		echo '<span class="report-link">';
		if(isset($topic['isReported']) AND $topic['isReported']){
			echo '<em>Reported</em>'; 
		}
		else{
			echo '<a class="report-post" data-id="'.$topic['topicId'].'" data-type="topic" href="#">Flag/Report</a>';
		}
	
		echo '</span> ';
	}
	if($user AND $perms['canRequestBan']){
		echo ' <span class="report-link"><a class="request-ban" data-id="'.$topic['userId'].'" href="#">Request Ban</a></span>';
	}	
	echo '</div>'; //post-extras
	$likeList = array();
	foreach($topic['likeUsers'] as $likeUser){
		$likeList[] = str_replace('"', '', $likeUser['username']);
	}
	$likeList = join(', ', $likeList);
	
	if($user){
		$model = new \Core\Model;
		$likeLink = '';
		if($perms['canUpvoteDownvote']){
			$hasLiked = $model->getAll('user_likes', array('userId' => $user['userId'], 'itemId' => $topic['topicId'], 'type' => 'topic'));
			$unlike = '';
			$likeText = 'Like';
			if(isset($hasLiked[0])){
				$unlike = 'unlike';
				$likeText = 'Unlike';
			}
			$likeLink = '<a href="#" class="like-post '.$unlike.'" data-id="'.$topic['topicId'].'" title="'.$likeList.'" data-type="topic">'.$likeText.' <span>(<em>'.$topic['likes'].'</em> '.pluralize('like', $topic['likes'], true).')</span></a>';
			
		}
		else{
			$likeLink = '<em title="'.$likeList.'">'.$topic['likes'].' '.pluralize('like', $topic['likes'], true).'</em>';
		}
		echo '	<div class="clear"></div><div class="post-controls">
					<span class="post-action" style="float: right;">
					'.$likeLink;
					
		if($perms['canPostReply'] AND $topic['locked'] == 0){
			echo ' <a href="#post-reply" class="quote-post">Quote</a>';
		}
		echo '</span>';
		if(($user['userId'] == $topic['userId'] AND $perms['canEditSelf']) OR ($user['userId'] != $topic['userId'] AND $perms['canEditOther'])){
			echo '<a href="'.$thisURL.'/edit">Edit</a>';
		}
		if(($user['userId'] == $topic['userId'] AND $perms['canLockSelf']) OR ($user['userId'] != $topic['userId'] AND $perms['canLockOther'])){
			$lockUrl = 'lock';
			$lockLabel = 'Lock';
			if($topic['locked'] != 0){
				$lockUrl = 'unlock';
				$lockLabel = 'Unlock';
			}
			echo '<a href="'.$thisURL.'/'.$lockUrl.'">'.$lockLabel.'</a>';
		}
		if(($user['userId'] == $topic['userId'] AND $perms['canStickySelf']) OR ($user['userId'] != $topic['userId'] AND $perms['canStickyOther'])){
			$stickyUrl = 'sticky';
			$stickyLabel = 'Sticky';
			if($topic['sticky'] != 0){
				$stickyUrl = 'unsticky';
				$stickyLabel = 'Un-sticky';
			}
			echo '<a href="'.$thisURL.'/'.$stickyUrl.'">'.$stickyLabel.'</a>';
		}
		if(($user['userId'] == $topic['userId'] AND $perms['canMoveSelf']) OR ($user['userId'] != $topic['userId'] AND $perms['canMoveOther'])){
			echo '<a href="'.$thisURL.'/move">Move</a>';
		}
		if($perms['canPermaDeleteTopic']){
			echo '<a href="'.$thisURL.'/permadelete" class="delete">Permadelete</a>';
		}
		if(($user['userId'] == $topic['userId'] AND $perms['canDeleteSelfTopic']) OR ($user['userId'] != $topic['userId'] AND $perms['canDeleteOtherTopic'])){
			echo '<a href="'.$thisURL.'/delete" class="delete" style="float: right; margin-right: 60px;">Bury Thread</a>';
		}
		
		if($user){
			//old subscribe spot
		}

		echo '<div class="clear"></div></div>';
		
	}//endif
	else{
		echo '<div class="clear"></div><div class="post-controls">
					<span class="post-action" style="float: right;"><em title="'.$likeList.'">'.$topic['likes'].' '.pluralize('like', $topic['likes'], true).'</em></span>
				<div class="clear"></div>
			  </div>';
	}
	?>
</div>

<?php
}//endif
?>

<?php
if(count($replies) == 0){
	echo '<p>No replies yet</p>';
}
?>
<ul class="reply-list">
	<?php
	foreach($replies as $k => $reply){
		$postClass = '';
		if($reply['buried'] != 0){
			$postClass = 'buried';
		}
		echo '<li class="'.$postClass.'"><a name="post-'.$reply['postId'].'" class="anchor"></a>';
		?>
		<div class="reply-author">
			<?php
			if($reply['buried'] != 0){
				echo '<div class="post-buried post-username">[deleted]</div>';
			}
			else{
				$userId = 0;
				if($user){
					$userId = $user['userId'];
				}
				$checkUserTCA = checkUserTCA($userId, $reply['userId']);
				

				$avImage = $reply['author']['avatar'];
				if(trim($reply['author']['real_avatar']) == ''){
					$avImage = SITE_URL.'/files/avatars/default.jpg';
				}
				else{				
					if(!isExternalLink($reply['author']['avatar'])){
						$avImage = SITE_URL.'/files/avatars/'.$reply['author']['avatar'];
					}
				}
				$avImage = '<img src="'.$avImage.'" alt="" />';
				if($checkUserTCA){
					$avImage = '<a href="'.SITE_URL.'/profile/user/'.$reply['author']['slug'].'">'.$avImage.'</a>';
				}					
				
				$replyUsername = $reply['author']['username'];
				if($checkUserTCA){
					$replyUsername = '<a href="'.SITE_URL.'/profile/user/'.$reply['author']['slug'].'" target="_blank">'.$replyUsername.'</a>';
				}
			?>
			<span class="post-username"><?= $replyUsername ?></span>
			<div class="profile-pic">
				<?php

				echo $avImage;
				
				?>
			</div>
			
			<div class="post-author-info">
				<?php
				$this->includeView('inc/group-title', array('profile' => $reply['author'], 'primary_only' => true));
			$use_status = '';
			$online_icon = 'fa-circle';
			$online_title = 'Offline';
			$activeTime = strtotime($reply['author']['lastActive']);
			$diff = $time - $activeTime;
			if($diff < 7200){
				if(isset($reply['author']['custom_status'])){
					switch($reply['author']['custom_status']){
						case 'away':
							$online_icon .= ' text-pending';
							$online_title = 'Away';							
							break;	
						case 'busy':
							$online_icon .= ' text-progress';
							$online_title = 'Busy';							
							break;												
						case 'offline':
							$online_icon .= ' text-error';
							$online_title = 'Offline';							
							break;					
						default:
							$online_icon .= ' text-success';
							$online_title = 'Online';							
							break;
					}
				}
				else{
					$online_icon .= ' text-success';
					$online_title = 'Online';				
				}
			}
			else{
				$online_icon .= ' text-error';
			}		
			
			$use_active = $reply['author']['lastActive'];
			if($online_title == 'Offline'){
				$use_active = $reply['author']['lastAuth'];
			}
			
			$use_status = '<span title="Last active: '.formatDate($use_active).'"><i class="fa fa-circle '.$online_icon.'"></i> <strong>'.$online_title.'</strong></span>';
			?>
			<?= $use_status ?><br>			
				<strong>Posts:</strong> <?= \App\Account\Home_Model::getUserPostCount($reply['userId']) ?>
				<?php
				if(isset($reply['author']['profile']['location'])){
					echo '<br><strong>Location:</strong> '.$reply['author']['profile']['location']['value'];
				}
				if($user AND $user['userId'] != $reply['userId']){
					if($checkUserTCA){					
						echo '<br><a href="'.SITE_URL.'/dashboard/account/messages/send?user='.$reply['author']['slug'].'" target="_blank" class="send-msg-btn" title="Send private message"><i class="fa fa-envelope"></i> Message</a>';
					}
				}				
				?>
			</div>
			<?php
			}//endif
			
			?>
		</div>
		<div class="reply-content">
			<div class="post-content" <?php if($reply['buried'] == 0){ ?>data-user-slug="<?= $reply['author']['slug'] ?>" data-message="<?= base64_encode($reply['content']) ?>" <?php }//endif ?>>
				<?= markdown($reply['content']) ?>
			</div>
				<?php
				if($reply['buried'] != 1 AND isset($reply['author']['profile']['forum-signature']['value'])){
					echo "		<div class=\"forum-sig\">\n";
					echo markdown($reply['author']['profile']['forum-signature']['value']);
					echo "		</div>\n";
				}
				?>
		</div>
		<div class="clear"></div>
		<span class="post-date">Posted on <?= formatDate($reply['postTime']) ?>
		<?php
		if($reply['editTime'] != null){
			echo '<br>Last Edited: '.formatDate($reply['editTime']);
		}
		?>
		</span>
		<div class="post-extras">
		<?php
		if($user AND $perms['canReportPost'] AND $reply['userId'] != $user['userId']){
			echo '<span class="report-link">';
			if(isset($reply['isReported']) AND $reply['isReported']){
				echo '<em>Reported</em>'; 
			}
			else{
				echo '<a class="report-post" data-id="'.$reply['postId'].'" data-type="post" href="#">Flag/Report</a>';
			}
			echo '</span> ';
		}	
		if($user AND $perms['canRequestBan']){
			echo ' <span class="report-link"><a class="request-ban" data-id="'.$reply['userId'].'" href="#">Request Ban</a></span>';
		}			
		$permaPage = '';
		$returnPage = '';
		if(isset($_GET['page']) AND intval($_GET['page']) > 1){
			$permaPage = '?page='.intval($_GET['page']);
			$returnPage = '?retpage='.intval($_GET['page']);
		}
		?>
		<span class="post-permalink"><a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/<?= $topic['url'] ?><?= $permaPage ?>#post-<?= $reply['postId'] ?>">Permalink</a></span>
		</div>
		<?php
			$likeList = array();
			foreach($reply['likeUsers'] as $likeUser){
				$likeList[] = str_replace('"', '', $likeUser['username']);
			}
			$likeList = join(', ', $likeList);
			
			if($user AND $reply['buried'] != 1){
				$likeLink = '';
				if($perms['canUpvoteDownvote']){
					$hasLiked = $model->getAll('user_likes', array('userId' => $user['userId'], 'itemId' => $reply['postId'], 'type' => 'post'));
					$unlike = '';
					$likeText = 'Like';
					if(isset($hasLiked[0])){
						$unlike = 'unlike';
						$likeText = 'Unlike';
					}
					$likeLink = '<a href="#" class="like-post '.$unlike.'" data-id="'.$reply['postId'].'" title="'.$likeList.'" data-type="reply">'.$likeText.' <span>(<em>'.$reply['likes'].'</em> '.pluralize('like', $reply['likes'], true).')</span></a>';
				}
				else{
					$likeLink = '<em title="'.$likeList.'">'.$reply['likes'].' '.pluralize('like', $reply['likes'], true).'</em>';
				}
				echo '	<div class="clear"></div><div class="post-controls">
					<span class="post-action" style="float: right;">
					'.$likeLink;
				
							
				if($perms['canPostReply'] AND $topic['locked'] == 0){
					echo ' <a href="#post-reply" class="quote-post">Quote</a>';
				}
				echo '</span>';
				if(($user['userId'] == $reply['userId'] AND $perms['canEditSelf']) OR ($user['userId'] != $reply['userId'] AND $perms['canEditOther'])){
					echo '<a href="'.$thisURL.'/edit/'.$reply['postId'].$returnPage.'">Edit Post</a>';
				}
				if(($user['userId'] == $reply['userId'] AND $perms['canBurySelf']) OR ($user['userId'] != $reply['userId'] AND $perms['canBuryOther'])){
					echo '<a href="'.$thisURL.'/delete/'.$reply['postId'].$returnPage.'" class="delete">Bury Post</a>';
				}
				if($perms['canPermaDeletePost']){
					echo '<a href="'.$thisURL.'/permadelete/'.$reply['postId'].$returnPage.'" class="delete">Permadelete</a>';
				}
				echo '<div class="clear"></div></div>';
			
		}
		elseif($reply['buried'] != 1){
			echo '<div class="clear"></div><div class="post-controls">
						<span class="post-action" style="float: right;"><em title="'.$likeList.'">'.$reply['likes'].' '.pluralize('like', $reply['likes'], true).'</em></span>
					<div class="clear"></div>
				  </div>';
		}
				


		?>
		<?php
		echo '</li>';
	}
	?>
</ul>
<a name="bottom"></a>
<span class="pull-right">
	<a href="#top"><strong>Jump to top <i class="fa fa-chevron-up"></i></strong></a>
</span>
<div class="topic-paging paging">
	<?php
	if($numPages > 1){
		echo '<strong>Pages:</strong> ';
		for($i = 1; $i <= $numPages; $i++){
			$active = '';
			if((isset($_GET['page']) AND $_GET['page'] == $i) OR (!isset($_GET['page']) AND $i == 1)){
				$active = 'active';
			}
			echo '<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/'.$topic['url'].'?page='.$i.'" class="'.$active.'">'.$i.'</a>';
		}
	}
	?>
</div>
<?php
if(!$user OR $perms['canPostReply']){
?>
	<?php
	if(isset($replyMessage) AND $replyMessage != ''){
		echo '<p class="error">'.$replyMessage.'</p>';
	}
	?>
	<a name="post-reply"></a>
	<?php
	if($user){
		if(isset($subscribeClass)){
			echo '<p style="float: right; vertical-align: top; margin-top: 10px; width: 120px; text-align: center;">';
			echo '<a href="#" class="board-control-link '.$subscribeClass.'">'.$subscribeText.'</a>';	
			echo '</p>';	
		}
	}
	?>
	<h2>Post Reply</h2>
	<div class="reply-form">
	<?php
	if($user){

		if($topic['locked'] != 0){
			$model = new \Core\Model;
			$getLockedUser = $model->get('users', $topic['lockedBy'], array('username', 'slug'));
			$lockedUser = '';
			if($getLockedUser){
				$lockedUser = 'by <a href="'.SITE_URL.'/profile/user/'.$getLockedUser['slug'].'">'.$getLockedUser['username'].'</a>';
				
			}
			echo '<p><em>This thread was locked on '.formatDate($topic['lockTime']).' '.$lockedUser.' </em></p>';
		}
		elseif(isset($form)){
			echo $form->display();
			
			echo '<p><em>Use <strong>markdown</strong> formatting for post. See <a href="#" class="markdown-trigger" target="_blank">formatting guide</a>
						for more information.</em></p>
					<div style="display: none;" id="markdown-guide">
					'.$this->displayBlock('markdown-guide').'
					</div>
					
					';
		}
	}
	else{
	?>
		<p>
		Please <a href="<?= SITE_URL ?>/account?r=/<?= $app['url'] ?>/<?= $module['url'] ?>/<?= $topic['url'] ?>">Login</a>
		to post a reply to this thread.
		</p>
	<?php
	}
	echo '</div>';
}
?>
</div><!-- forum-thread -->
<script type="text/javascript">
	$(document).ready(function(){
		$('.quote-post').click(function(e){
			var message = $(this).parent().parent().parent().find('.post-content').data('message');
			message = Base64.decode(message);
			var user = $(this).parent().parent().parent().find('.post-content').data('user-slug');
			var newMessage = '> @' + user + "\n";
			var splitMessage = message.split("\n");
			$.each(splitMessage, function(k, v){
				newMessage = newMessage + '> ' + v + "\n";
			});
			
			var curVal = $('.reply-form').find('#markdown').val();
			if(curVal.trim() != ''){
				newMessage = curVal + "\n\n" +  newMessage;
			}
			
			$('.reply-form').find('#markdown').val(newMessage);
		
			
		});
		
		
		$('.like-post').click(function(e){
			e.preventDefault();
			var id = $(this).data('id');
			var type = $(this).data('type');
			var numLikes = parseInt($(this).find('span').find('em').html());
			
			var action = 'like';
			if($(this).hasClass('unlike')){
				action = 'unlike';
			}
			
			if(type == 'topic'){
				var url = '<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/<?= $topic['url'] ?>/' + action;
			}
			if(type == 'reply'){
				var url = '<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/<?= $topic['url'] ?>/' + action + '/' + id;
			}
			
			var thisLink = $(this);
			console.log(url);
			
			$.get(url, function(data){
				console.log(data);
				if(typeof data.error != 'undefined'){
					console.log(data.error);
					return false;
				}
				else{
					if(action == 'like'){
						thisLink.addClass('unlike');
						numLikes++;
						var likeText = 'like';
						if(numLikes == 0 || numLikes > 1){
							likeText = 'likes';
						}
						thisLink.html('Unlike <span>(<em>' + numLikes + '</em> ' + likeText + ')</span>');
					}
					else{
						thisLink.removeClass('unlike');
						numLikes--;
						var likeText = 'like';
						if(numLikes == 0 || numLikes > 1){
							likeText = 'likes';
						}
						thisLink.html('Like <span>(<em>' + numLikes + '</em> ' + likeText + ')</span>');						
					}
				}
			});
		});
		
		$('.content').delegate('.subscribe', 'click', function(e){
			e.preventDefault();
			var thisLink = $('.subscribe');
			var url = '<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/<?= $topic['url'] ?>/subscribe';
			$.post(url, function(data){
				if(typeof data.error != 'undefined'){
					alert(data.error);
					return false;
				}
				else{
					thisLink.html('Unsubscribe');
					thisLink.addClass('unsubscribe');
					thisLink.removeClass('subscribe');
					
				}
			});
			
		});
		
		$('.content').delegate('.unsubscribe', 'click', function(e){
			e.preventDefault();
			var thisLink = $('.unsubscribe');
			var url = '<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/<?= $topic['url'] ?>/unsubscribe';
			$.post(url, function(data){
				if(typeof data.error != 'undefined'){
					alert(data.error);
					return false;
				}
				else{
					thisLink.html('Subscribe');
					thisLink.addClass('subscribe');
					thisLink.removeClass('unsubscribe');
				}
			});
			
		});
		
		$('.report-post').click(function(e){
			e.preventDefault();
			var check = confirm('Are you sure you want to report this post as spam/inappropriate? (a moderator will be notified)');
			if(!check || check == null){
				return false;
			}
			
			var url = '<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/<?= $topic['url'] ?>/report';
			var thisId = $(this).data('id');
			var thisType = $(this).data('type');
			var thisLink = $(this);
			$.post(url, {type: thisType, itemId: thisId}, function(data){
				//console.log(data);
				if(typeof data.error != 'undefined'){
					console.log(data.error);
					return false;
				}
				else{
					$(thisLink).parent().html('<em>Reported</em>');
				}
			});
		});
		
		<?php
		if($user AND $perms['canRequestBan']){
		?>
		$('.request-ban').click(function(e){
			e.preventDefault();
			var reason = prompt('Please enter a reason for the ban request. An Admin will investigate');
			if(!prompt || prompt == null){
				return false;
			}
			
			var thisId = $(this).data('id');
			var thisLink = $(this);
			var url = '<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/<?= $topic['url'] ?>/request-ban/' + thisId;
			$.post(url, {message: reason}, function(data){
				console.log(data);
				if(typeof data.error != 'undefined'){
					console.log(data.error);
					return false;
				}
				else{
					$(thisLink).parent().html('<em>Request Sent</em>');
				}
			});
			
		});
		
		<?php
		}//endif
		?>
		
	});
</script>
