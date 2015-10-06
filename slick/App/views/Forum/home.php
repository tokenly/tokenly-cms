<h1 class="large"><?= $app['meta']['forum-title'] ?></h1>
<hr>
<?php
	echo '<div class="forum-description">'.$this->displayBlock('main-forum-description').'</div>';
?>

<div class="forum-categories">
	<?php

	foreach($categories as $cat){
		echo '<div class="forum-category">
				<h2>'.$cat['name'].'</h2>';
		if(trim($cat['description']) != ''){
			echo '<div class="forum-category-desc">'.$cat['description'].'</div>';
		}
		
		if(count($cat['boards']) > 0){
			echo '<ul class="category-boards">';
			foreach($cat['boards'] as $board){
				echo '<li>
						<h3><a href="'.SITE_URL.'/'.$app['url'].'/board/'.$board['slug'].'">'.$board['name'].'</a></h3>
						';
				if(trim($board['description']) != ''){
					echo '<div class="board-description">'.\App\Page\View_Model::parsePageTags(markdown($board['description']), true).'</div>';
				}
				
				$mostRecent = '';
				if($board['mostRecent'] != ''){
					$mostRecent = '<strong>Most recent:</strong> '.$board['mostRecent'];
				}
				
				echo '<ul class="board-info">';
					echo '<li><strong>'.$board['numTopics'].' '.pluralize('discussion', $board['numTopics'], true).'</strong/li>
						  <li><strong>'.$board['numReplies'].' '.pluralize('comment', $board['numReplies'], true).'</strong></li>
						  <li>'.$mostRecent.'</li>';
				echo '</ul>
				<div class="clear"></div>';
				
				echo '</li>';
			}
			echo '</ul>';
		}
		
		echo '</div>';
	}

	?>
</div>
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
