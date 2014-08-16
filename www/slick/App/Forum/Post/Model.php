<?php
class Slick_App_Forum_Post_Model extends Slick_Core_Model
{
	public function getReplyForm()
	{
		$form = new Slick_UI_Form;
		
		$content = new Slick_UI_Textarea('content', 'markdown');
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
		
		if(trim($useData['content']) == ''){
			throw new exception('Message required');
		}
		
		$useData['content'] = strip_tags($useData['content']);
		
		$useData['postTime'] = timestamp();
		$post = $this->insert('forum_posts', $useData);
		if(!$useData){
			throw new Exception('Message required');
		}
		
		$this->edit('forum_topics', $useData['topicId'], array('lastPost' => timestamp()));
		
		$useData['postId'] = $post;
		

		$numReplies = $this->count('forum_posts', 'topicId', $appData['topic']['topicId']);
		$numPages = ceil($numReplies / $appData['app']['meta']['postsPerPage']);
		$page = '';
		if($numPages > 1){
			$page = '?page='.$numPages;
		}

		mention($useData['content'], '%username% has mentioned you in a 
				<a href="'.$appData['site']['url'].'/'.$appData['app']['url'].'/'.$appData['module']['url'].'/'.$appData['topic']['url'].$page.'#post-'.$useData['postId'].'">forum post.</a>',
				$useData['userId'], $useData['postId'], 'forum-reply');
				
		$getSubs = $this->getAll('forum_subscriptions', array('topicId' => $data['topicId']));
		foreach($getSubs as $sub){
			if($sub['userId'] != $useData['userId']){
				$notification = '<a href="'.$appData['site']['url'].'/profile/user/'.$appData['user']['slug'].'">'.$appData['user']['username'].'</a> posted a new 
								reply in a forum topic you are subscribed to: <a href="'.$appData['site']['url'].'/'.$appData['app']['url'].'/'.$appData['module']['url'].'/'.$appData['topic']['url'].$page.'#post-'.$useData['postId'].'">'.$appData['topic']['title'].'</a>';
				Slick_App_Meta_Model::notifyUser($sub['userId'], $notification, $useData['postId'], 'topic-subscription');
			}
			
		}
		
	
		return $useData;
		
	}
	
	public function editPost($id, $data, $appData)
	{
		$useData = array();
		$req = array('content' => true);
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
		
		$edit = $this->edit('forum_posts', $id, $useData);
		if(!$edit){
			throw new Exception('Error editing post');
		}

		mention($useData['content'], '%username% has mentioned you in a 
				<a href="'.$appData['site']['url'].'/'.$appData['app']['url'].'/'.$appData['module']['url'].'/'.$appData['topic']['url'].'">forum post.</a>',
				$appData['user']['userId'], $id, 'forum-reply');
		
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
		
		$get = $this->fetchAll('SELECT * FROM 
								forum_posts
								WHERE topicId = :topicId
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
		
		if(isset($_GET['regen-url'])){
			$useData['url'] = genURL($useData['title']);
			if(trim(str_replace('-', '', $useData['url'])) == ''){
				$useData['url'] = substr(md5($useData['title']), 0, 10);
			}
			$boardModel = new Slick_App_Forum_Board_Model;
			$checkURL = $boardModel->checkURLExists($useData['url']);
			if($checkURL){
				$useData['url'] .= '-'.$checkURL + 1;
			}
		}
		
		$edit = $this->edit('forum_topics', $topicId, $useData);
		if(!$edit){
			throw new Exception('Error editing thread');
		}
		
		$getTopic = $this->get('forum_topics', $topicId);
		mention($useData['content'], '%username% has mentioned you in a 
				<a href="'.$appData['site']['url'].'/'.$appData['app']['url'].'/'.$appData['module']['url'].'/'.$getTopic['url'].'">forum thread.</a>',
				$appData['user']['userId'], $topicId, 'forum-topic');
		
		return $this->get('forum_topics', $topicId);
	}
	
	public function getMoveTopicForm($site)
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
		foreach($getBoards as $board){
			$boardId->addOption($board['boardId'], $board['name']);
		}
		$form->add($boardId);
		
		return $form;
		
	}
	
	public function moveTopic($id, $data)
	{
		if(!isset($data['boardId'])){
			throw new Exception('Board Required');
		}
		$getBoard = $this->get('forum_boards', $data['boardId']);
		if(!$getBoard){
			throw new Exception('Board not found');
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
		$getAllReplies = $this->getAll('forum_posts', array('topicId' => $getPost['topicId']), array('postId'), 'postTime', 'asc');
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
	
	
}
