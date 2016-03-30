<?php
include(THEME_PATH.'/inc/header.php');
?>
<div class="content">
	<div class="home-content">
		<?php
		$model = new \App\Forum\Model;
		$output = array();
		$forum_app = get_app('forum');
		$output['app'] = $forum_app;
		$output['categories'] = $model->getForumCategories($site, $forum_app, $user);
		$numTopics = $model->fetchSingle('SELECT count(*) as total
												FROM forum_topics t
												LEFT JOIN forum_boards b ON b.boardId = t.boardId
												WHERE b.siteId = :siteId', array(':siteId' => $site['siteId']));
		$output['numTopics'] = $numTopics['total'];

		$numReplies = $model->fetchSingle('SELECT count(*) as total
												FROM forum_posts p
												LEFT JOIN forum_topics t ON t.topicId = p.topicId
												LEFT JOIN forum_boards b ON b.boardId = t.boardId
												WHERE b.siteId = :siteId', array(':siteId' => $site['siteId']));
		$output['numReplies'] = $numReplies['total'];
		$output['numUsers'] = $model->count('users');
		$output['numOnline'] = \App\Account\Home_Model::getUsersOnline();
		$output['mostOnline'] = \App\Account\Home_Model::getMostOnline();
		$output['onlineUsers'] = \App\Account\Home_Model::getOnlineUsers();
		$output['forum_home'] = true;
		
		$this->includeView('Forum/home', $output);
		?>
	</div><!-- home-content -->
</div><!-- content -->
<?php
include(THEME_PATH.'/inc/footer.php');
?>
