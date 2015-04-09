<?php
class Slick_App_Forum_Post_Model extends Slick_Core_Model
{
	public function getReplyForm()
	{
		$form = new Slick_UI_Form;
		
		$content = new Slick_UI_Markdown('content', 'markdown');
		$content->setLabel('Message');
		$content->addAttribute('required');
		$form->add($content);
		
		return $form;
		
	}
	
	public function postReply($data, $appData)
	{
		$useData = array();
		$req = array('topicId' => true, 'userId' => true, 'content' => false);
		foreach($req as $key => $required){
			if(!isset($data[$key])){
				if($required){
					throw new Exception(ucfirst($key).' required');
				}
				else{
					$useData[$key] = '';
				}
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		if(isset($data['check_captcha']) AND $data['check_captcha']){
			require_once(SITE_PATH.'/resources/recaptchalib2.php');
			$recaptcha = new Recaptcha(CAPTCHA_PRIV);
			$resp = $recaptcha->verifyResponse($_SERVER['REMOTE_ADDR'], @$_POST['g-recaptcha-response']);
			if($resp == null OR !$resp->success){
				throw new Exception('Captcha invalid!');
			}		
		}
		
		$regDate = strtotime($appData['user']['regDate']);
		$regThreshold = 60*60*1;
		$time = time();
		if(($time - $regDate) < $regThreshold){
			$numHours = round($regThreshold / 3600);
			throw new exception('Your account must be active for at least <strong>'.$numHours.' '.pluralize('hour', $numHours, true).'</strong> before you may post in the forums.');
		}
		
		if(trim($useData['content']) == ''){
			throw new exception('Message required');
		}
		
		$useData['content'] = strip_tags($useData['content']);
		$useData['postTime'] = timestamp();
		
		if($appData['perms']['isTroll']){
			$useData['trollPost'] = 1;
		}
		
		$post = $this->insert('forum_posts', $useData);
		if(!$useData){
			throw new Exception('Message required');
		}
		
		if(!$appData['perms']['isTroll']){
			$this->edit('forum_topics', $useData['topicId'], array('lastPost' => timestamp()));
		}
		
		$useData['postId'] = $post;
		

		$numReplies = Slick_App_Forum_Post_Model::getNumTopicReplies($appData['topic']['topicId']);
		$numPages = Slick_App_Forum_Post_Model::getNumTopicPages($appData['topic']['topicId']);
		$page = '';
		if($numPages > 1){
			$page = '?page='.$numPages;
		}
		
		if(!isset($useData['trollPost'])){
			$notifyData = $appData;
			$notifyData['postId'] = $useData['postId'];
			$notifyData['page'] = $page;
			$notifyData['postContent'] = $useData['content'];

			mention($useData['content'], 'emails.forumPostMention',
					$useData['userId'], $useData['postId'], 'forum-reply', $notifyData);
					
			$getSubs = $this->getAll('forum_subscriptions', array('topicId' => $data['topicId']));
			foreach($getSubs as $sub){
				$notifyData['sub'] = $sub;
				if($sub['userId'] != $useData['userId']){
					Slick_App_Meta_Model::notifyUser($sub['userId'], 'emails.forumSubscribeNotice', $useData['postId'], 'topic-subscription', false, $notifyData);
				}
			}


			// check board subscriptions
			$boardId = $appData['topic']['boardId'];
			$getBoardSubs = $this->getAll('board_subscriptions', array('boardId' => $boardId));
			foreach($getBoardSubs as $sub) {
				// don't notify self
				if($sub['userId'] == $useData['userId']) { continue; }

				// fetch the board name
				if (!isset($notifyData['board'])) {
					$notifyData['board'] = $this->get('forum_boards', $boardId);
				}

				// notify the user
				Slick_App_Meta_Model::notifyUser($sub['userId'], 'emails.boardSubscribeNotice', $useData['postId'], 'topic-subscription', false, $notifyData);
			}
			
		}

		$returnData = array();
		$returnData['postId'] = $useData['postId'];
		$returnData['topicId'] = $useData['topicId'];
		$returnData['userId'] = $useData['userId'];
		$returnData['content'] = $useData['content'];
		$returnData['postTime'] = $useData['postTime'];
		if(isset($useData['trollPost'])){
			$returnData['trollPost'] = $useData['trollPost'];
		}
		

		return $returnData;
	}
	
	public function editPost($id, $data, $appData)
	{
		$useData = array();
		$req = array('content' => true);
		foreach($req as $key => $required){
			if(!isset($data[$key])){
				if($required){
					throw new Exception($key.' required');
				}
				else{
					$useData[$key] = '';
				}
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		$useData['editTime'] = timestamp();
		$useData['editedBy'] = $appData['user']['userId'];
		
		$edit = $this->edit('forum_posts', $id, $useData);
		if(!$edit){
			throw new Exception('Error editing post');
		}
		
		$getPost = $this->get('forum_posts', $id);
		$numReplies = Slick_App_Forum_Post_Model::getNumTopicReplies($appData['topic']['topicId']);
		$numPages = Slick_App_Forum_Post_Model::getNumTopicPages($appData['topic']['topicId']);
		$page = '';
		if($numPages > 1){
			$page = '?page='.$numPages;
		}		
		
		if($getPost['trollPost'] != 1){
			$notifyData = $appData;
			$notifyData['postId'] = $id;
			$notifyData['page'] = $page;
			$notifyData['postContent'] = $useData['content'];

			mention($useData['content'], 'emails.forumPostMention',
					$getPost['userId'], $id, 'forum-reply', $notifyData);			
			
			
			mention($useData['content'], '%username% has mentioned you in a 
					<a href="'.$appData['site']['url'].'/'.$appData['app']['url'].'/'.$appData['module']['url'].'/'.$appData['topic']['url'].'">forum post.</a>',
					$appData['user']['userId'], $id, 'forum-reply');
		}
		
		Slick_Core_Model::$cacheMode = false;
		return $this->get('forum_posts', $id);
		
	}
	
	public function getTopicReplies($topicId, $data, $page = 1)
	{
		$start = 0;
		$max = intval($data['app']['meta']['postsPerPage']);
		$page = intval($page);
		if($page > 1){
			$start = ($page * $max) - $max;
		}
		$limit = 'LIMIT '.$start.', '.$max;
		
		$andTroll = ' AND trollPost = 0 ';
		if(isset($_GET['trollVision'])){
			$andTroll = '';
		}
		else{
			if($data['user'] AND $data['perms']['isTroll']){
				$andTroll = ' AND (trollPost = 0 OR (trollPost = 1 AND userId = '.$data['user']['userId'].')) ';
			}
		}
		
		$get = $this->fetchAll('SELECT * FROM 
								forum_posts
								WHERE topicId = :topicId AND buried = 0
								'.$andTroll.'
								ORDER BY postId ASC
								'.$limit,
								array(':topicId' => $topicId));
		$profModel = new Slick_App_Profile_User_Model;
		foreach($get as $key => $row){
			$get[$key]['author'] = $profModel->getUserProfile($row['userId'], $data['site']['siteId']);
			$likeUsers = $this->fetchAll('SELECT u.username, u.userId, u.slug
										  FROM user_likes l
										  LEFT JOIN users u ON u.userId = l.userId
										  WHERE type = "post" AND itemId = :id', array(':id' => $row['postId']));
			$get[$key]['likeUsers'] = $likeUsers;
			$get[$key]['likes'] = count($likeUsers);
			
					
		}
		
		return $get;
	}
	
	public function editTopic($topicId, $data, $appData)
	{
		$useData = array();
		$req = array('title' => true, 'content' => true);
		foreach($req as $key => $required){
			if(!isset($data[$key])){
				if($required){
					throw new Exception(ucfirst($key).' required');
				}
				else{
					$useData[$key] = '';
				}
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		$useData['editTime'] = timestamp();
		$useData['editedBy'] = $appData['user']['userId'];
		
		if(isset($_GET['regen-url'])){
			$useData['url'] = genURL($useData['title']);
			if(trim(str_replace('-', '', $useData['url'])) == ''){
				$useData['url'] = substr(md5($useData['title']), 0, 10);
			}
			$boardModel = new Slick_App_Forum_Board_Model;
			$useData['url'] = $boardModel->checkURLExists($useData['url'], $topicId);
		}
		
		$edit = $this->edit('forum_topics', $topicId, $useData);
		if(!$edit){
			throw new Exception('Error editing thread');
		}
		
		$getTopic = $this->get('forum_topics', $topicId);
		if($getTopic['trollPost'] != 1){
			mention($useData['content'], '%username% has mentioned you in a 
					<a href="'.$appData['site']['url'].'/'.$appData['app']['url'].'/'.$appData['module']['url'].'/'.$getTopic['url'].'">forum thread.</a>',
					$appData['user']['userId'], $topicId, 'forum-topic');
		}
		Slick_Core_Model::$cacheMode = false;
		return $this->get('forum_topics', $topicId);
	}
	
	public function getMoveTopicForm($site, $user)
	{
		$form = new Slick_UI_Form;
		
		$getBoards = $this->fetchAll('SELECT b.*
									  FROM forum_boards b
									  LEFT JOIN forum_categories c ON c.categoryId = b.categoryId
									  WHERE b.siteId = :siteId
									  ORDER BY c.rank ASC, b.rank ASC', 
									  array(':siteId' => $site['siteId']));
		$boardId = new Slick_UI_Select('boardId');
		$boardId->setLabel('New Board');
		$boardModule = $this->get('modules', 'forum-board', array(), 'slug');
		$tca = new Slick_App_Tokenly_TCA_Model;
		foreach($getBoards as $board){
			$checkCat = $tca->checkItemAccess($user, $boardModule['moduleId'], $board['categoryId'], 'category');
			$checkBoard = $tca->checkItemAccess($user, $boardModule['moduleId'], $board['boardId'], 'board');
			if(!$checkCat OR !$checkBoard){
				continue;
			}
			$boardId->addOption($board['boardId'], $board['name']);
		}
		$form->add($boardId);
		
		return $form;
		
	}
	
	public function moveTopic($id, $data, $user)
	{
		if(!isset($data['boardId'])){
			throw new Exception('Board Required');
		}
		$getBoard = $this->get('forum_boards', $data['boardId']);
		if(!$getBoard){
			throw new Exception('Board not found');
		}
		
		$boardModule = $this->get('modules', 'forum-board', array(), 'slug');
		$tca = new Slick_App_Tokenly_TCA_Model;
		$checkCat = $tca->checkItemAccess($user, $boardModule['moduleId'], $getBoard['categoryId'], 'category');
		$checkBoard = $tca->checkItemAccess($user, $boardModule['moduleId'], $getBoard['boardId'], 'board');
		
		if(!$checkCat OR !$checkBoard){
			throw new Exception('You do not have permission to move into that board');
		}

		$edit = $this->edit('forum_topics', $id, array('boardId' => $getBoard['boardId']));
		if(!$edit){
			throw new Exception('Error moving thread');
		}		
		return $getBoard;
	}
	
	public function getPostPage($postId, $perPage)
	{
		$page = 1;
		$perPage = intval($perPage);
		$getPost = $this->get('forum_posts', $postId);
		if(!$getPost){
			return false;
		}
		$getAllReplies = $this->getAll('forum_posts', array('topicId' => $getPost['topicId'], 'buried' => 0, 'trollPost' => 0), array('postId'), 'postTime', 'asc');
		$totalReplies = count($getAllReplies);
		$numPages = ceil($totalReplies / $perPage);
		
		$num = 0;
		foreach($getAllReplies as $reply){
			if($reply['postId'] == $postId){
				break;
			}
			$num++;
			if($num >= $perPage){
				$num = 0;
				$page++;
			}
		}
		
		return $page;
		
	}
	
	public static function getNumTopicReplies($topicId)
	{
		$model = new Slick_Core_Model;
		$count = $model->fetchSingle('SELECT count(*) as total
									 FROM forum_posts
									 WHERE topicId = :topicId
									 AND buried = 0 AND trollPost = 0', array(':topicId' => $topicId), 0, true);
									 
		return $count['total'];
	}
	
	public static function getNumTopicPages($topicId)
	{
		$model = new Slick_App_Meta_Model;
		$count = Slick_App_Forum_Post_Model::getNumTopicReplies($topicId);
		$forumApp = $model->get('apps', 'forum', array(), 'slug');
		$settings = $model->appMeta($forumApp['appId']);
		$perPage = 10;
		if(isset($settings['postsPerPage'])){
			$perPage = $settings['postsPerPage'];
		}
		$numPages = ceil($count / $perPage);
		return $numPages;
	}
	
	public function getUserUpvoteScore($userId)
	{
		$count = $this->fetchSingle('SELECT SUM(score) as total FROM user_likes WHERE opUser = :userId', array(':userId' => $userId));
		if(!$count){
			return 0;
		}
		return $count['total'];
	}
	
	public function getUserPosts($userId, $andTopics = true, $perPage = false, $page = 1)
	{
		$output = array('count' => 0, 'replies' => 0, 'topics' => 0, 'likes' => 0, 'posts' => array());
		
		$perPage = app_setting('forum', 'postsPerPage');
		$getPosts = $this->getAll('forum_posts', array('userId' => $userId, 'buried' => 0), array(), 'postId', 'desc');
		$getTopics = $this->getAll('forum_topics', array('userId' => $userId, 'buried' => 0), array(), 'topicId', 'desc');
										   
		$output['topics'] = count($getTopics);		
		$output['replies'] = count($getPosts);
		$output['count'] += $output['replies'];
		$output['count'] += $output['topics'];
		$postIds = array();
		$topicIds = array();
		
		foreach($getPosts as $post){
			$postIds[] = $post['postId'];
		}
		foreach($getTopics as $topic){
			$topicIds[] = $topic['topicId'];
		}
		
		$likes_list = $this->fetchAll('SELECT *
									   FROM user_likes
									   WHERE ((type = "post" AND itemId IN('.join(',',$postIds).'))
									   OR (type = "topic" AND itemId IN('.join(',',$topicIds).')))
									   AND userId != :userId',
									   array(':userId' => $userId));		
									   
		
		foreach($getPosts as $post){		
			$item = array();
			$item['userId'] = $post['userId'];
			$item['itemId'] = $post['postId'];
			$item['type'] = 'reply';
			$item['content'] = $post['content'];
			$item['date'] = $post['postTime'];
			$item['time'] = strtotime($post['postTime']);
			$item['topicId'] = $post['topicId'];
			
			$getLikes = extract_row($likes_list, array('itemId' => $item['itemId'], 'type' => 'post'), true);
			$output['likes'] += count($getLikes);
			$item['likes'] = $getLikes;			
						
			$output['posts'][] = $item;
		}
		
		foreach($getTopics as $post){
			$item = array();
			$item['userId'] = $post['userId'];
			$item['itemId'] = $post['topicId'];
			$item['topicId'] = $post['topicId'];
			$item['type'] = 'topic';
			$item['content'] = $post['content'];
			$item['date'] = $post['postTime'];
			$item['time'] = strtotime($post['postTime']);

			$getLikes = extract_row($likes_list, array('itemId' => $item['itemId'], 'type' => 'topic'), true);
			$output['likes'] += count($getLikes);
			$item['likes'] = $getLikes;						
			
			$output['posts'][] = $item;
		}
				
		aasort($output['posts'], 'time');
		$output['posts'] = array_reverse($output['posts']);
		
		$output['num_pages'] = false;
		if($perPage !== false){
			$pager = new Slick_Util_Paging;
			$pagePosts = $pager->pageArray($output['posts'], $perPage);
			$output['num_pages'] = count($pagePosts);
			
			if(!isset($pagePosts[$page])){
				$output['posts'] = array();
			}
			else{
				$output['posts'] = $pagePosts[$page];
			}
		}
		
		
		foreach($output['posts'] as &$item){
			$getTopic = $this->get('forum_topics', $item['topicId']);
			$getBoard = $this->get('forum_boards', $getTopic['boardId']);
			$getCategory = $this->get('forum_categories', $getBoard['categoryId']);
			if($item['type'] == 'reply'){
				$pageNum = $this->getPostPage($item['itemId'], $perPage);
				$item['ref_title'] = 'Re: '.$getTopic['title'];
				$item['ref_url'] = $getTopic['url'];
				if($pageNum > 1){
					$item['ref_url'] .= '?page='.$pageNum;
				}
				$item['ref_url'] .= '#post-'.$item['itemId'];
			}
			else{
				$item['ref_title'] = $getTopic['title'];
				$item['ref_url'] = $getTopic['url'];
			}
			
			$item['board_title'] = $getBoard['name'];
			$item['board_url'] = $getBoard['slug'];
			$item['boardId'] = $getBoard['boardId'];
			$item['categoryId'] = $getCategory['categoryId'];
			$item['category_title'] = $getCategory['name'];
			$item['category_slug'] = $getCategory['slug'];

		}
		
		return $output;
	}

	
}
