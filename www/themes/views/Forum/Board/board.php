<?php
$model = new Slick_Core_Model;
$boardToken = '';
if($isAll){
	$board = array('name' => 'Recent Posts', 'description' => '', 'slug' => 'all');
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

	$model = new Slick_Core_Model;
	$getSubs = $model->getAll('board_subscriptions',
				array('userId' => $user['userId'], 'boardId' => $board['boardId']));
	if(count($getSubs) > 0){
		$subscribeClass = 'unsubscribe';
		$subscribeText = 'Unsubscribe from Board';
	}

	echo '<p style="float: right; vertical-align: top; width: 190px; text-align: center;">';
	echo '<a href="#" class="board-control-link '.$subscribeClass.'">'.$subscribeText.'</a>';	
	echo '</p>';
}

?>

<h1><?= $board['name'] ?></h1>
<?php
if(trim($board['description']) != ''){
	echo '<div class="board-description">'.Slick_App_Page_View_Model::parsePageTags(markdown($board['description'])).'</div>';
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

<?php
if(!$isAll AND $user AND $perms['canPostTopic']){
	echo '<div class="board-controls">
			<ul>
				<li style="width: 190px; text-align: center;"><a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/'.$board['slug'].'/post">Post New Topic</a></li>
			</ul>
		  </div>';
	
}



if($isAll){
?>
<p>
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
	
			$model = new Slick_Core_Model;
			$getfCats = $model->getAll('forum_categories', array('siteId' => $site['siteId']), array(), 'rank', 'asc');
			$tca = new Slick_App_LTBcoin_TCA_Model;
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
<div class="board-topics">
	<?php
	
	if(count($stickies) > 0 AND (!isset($_GET['page']) OR $_GET['page'] == 1)){
		$stickyText = 'Hide';
		$stickyClass = 'collapse';
		$stickyDivStyle = '';
		if($isAll){
			$stickyText = 'Show';
			$stickyClass = '';
			$stickyDivStyle = 'display: none;';
		}
	?>
	<div class="clear"></div>
	<a href="#" class="sticky-trigger <?= $stickyClass ?>"><?= $stickyText ?> Sticky Posts</a>
	<div id="sticky-posts" style="<?= $stickyDivStyle ?>">
		<h4>Sticky Posts</h4>
	<?php
		$table = $this->generateTable($stickies, array('fields' => array('link' => 'Discussion', 'started' => 'Created',
																	   'numReplies' => 'Replies', 'views' => 'Readers', 'lastPost' => 'Most Recent'),
													'class' => 'topics-table mobile-table'));
		
		echo $table->display();
	?>
	</div>
	<?php
	}
	
	if(count($topics) == 0){
		echo '<p>No discussions found</p>';
	}
	else{

		
		$table = $this->generateTable($topics, array('fields' => array('link' => 'Discussion', 'started' => 'Created',
																	   'numReplies' => 'Replies', 'views' => 'Readers', 'lastPost' => 'Most Recent'),
													'class' => 'topics-table mobile-table'));
		
		echo $table->display();
		
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
if($isAll){
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
				$('#sticky-posts').slideUp();
				$(this).removeClass('collapse');
				$(this).html('Show Sticky Posts');
			}
			else{
				$('#sticky-posts').slideDown();
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

