<?php
namespace App\Forum;
use App\Tokenly, App\Profile, App\Account, UI, Util, Util\Session;
class Post_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Post_Model;
		$this->tca = new Tokenly\TCA_Model;
	}
	
	protected function init()
	{
		$output = parent::init();
		
		if(!isset($this->args[2])){
			$output['view'] = '404';
			return $output;
		}
		
		$getTopic = $this->model->get('forum_topics', $this->args[2], array(), 'url');
		if(!$getTopic OR $getTopic['buried'] == 1){
			$output['view'] = '404';
			return $output;
		}
		
		$boardModule = get_app('forum.forum-board');
		
		if($this->data['user']){
			$output['perms'] = $this->container->checkModPerms($getTopic['boardId'], $this->data);
			$output['perms'] = $this->tca->checkPerms($this->data['user'], $output['perms'], $this->data['module']['moduleId'], $getTopic['topicId'], 'topic');
			$output['perms'] = $this->tca->checkPerms($this->data['user'], $output['perms'], $boardModule['moduleId'], $getTopic['boardId'], 'board');
			$this->data['perms'] = $output['perms'];
		}
		
		if(isset($this->data['app']['meta']['min-required-upvote-points'])){
			$getUpvoteScore = $this->model->getUserUpvoteScore($this->data['user']['userId']);
			if($getUpvoteScore < intval($this->data['app']['meta']['min-required-upvote-points'])){
				$this->data['perms']['canUpvoteDownvote'] = false;
				$output['perms']['canUpvoteDownvote'] = false;
			}
		}

		$getBoard = $this->model->get('forum_boards', $getTopic['boardId']);
		$checkCat = $this->tca->checkItemAccess($this->data['user'], $boardModule['moduleId'], $getBoard['categoryId'], 'category');
		$checkBoard = $this->tca->checkItemAccess($this->data['user'], $boardModule['moduleId'], $getTopic['boardId'], 'board');
		$checkTCA = $this->tca->checkItemAccess($this->data['user'], $this->data['module']['moduleId'], $getTopic['topicId'], 'topic');
		if(!$checkTCA OR !$checkBoard OR !$checkCat){
			$output['view'] = '403';
			return $output;
		}
		
		$likeUsers = $this->model->fetchAll('SELECT u.username, u.userId, u.slug
									  FROM user_likes l
									  LEFT JOIN users u ON u.userId = l.userId
									  WHERE type = "topic" AND itemId = :id', array(':id' => $getTopic['topicId']));
		$getTopic['likeUsers'] = $likeUsers;
		$getTopic['likes'] = count($likeUsers);
		
		if($getBoard['siteId'] != $this->data['site']['siteId']){
			$output['view'] = '404';
			return $output;
		}
		
		$this->topic = $getTopic;
		$this->board = $getBoard;
	
		if(isset($this->args[3])){
			$newOutput = array();
			switch($this->args[3]){
				case 'edit':
					if(isset($this->args[4])){
						$newOutput = $this->container->editPost();
					}
					else{
						$newOutput = $this->container->editTopic();
					}
					break;
				case 'delete':
					if(isset($this->args[4])){
						$newOutput = $this->container->deletePost();
					}
					else{
						$newOutput = $this->container->deleteTopic();
					}
					break;
				case 'lock':
					$newOutput = $this->container->lockTopic();
					break;
				case 'unlock':
					$newOutput = $this->container->unlockTopic();
					break;
				case 'sticky':
					$newOutput = $this->container->stickyTopic();
					break;
				case 'unsticky':
					$newOutput = $this->container->unstickyTopic();
					break;
				case 'move':
					$newOutput = $this->container->moveTopic();
					break;
				case 'like':
					if(isset($this->args[4])){
						$newOutput = $this->container->likePost();
					}
					else{
						$newOutput = $this->container->likeTopic();
					}
					break;
				case 'unlike':
					if(isset($this->args[4])){
						$newOutput = $this->container->unlikePost();
					}
					else{
						$newOutput = $this->container->unlikeTopic();
					}
					break;
				case 'subscribe':
					$newOutput = $this->container->subscribeTopic();
					break;
				case 'unsubscribe':
					$newOutput = $this->container->unsubscribeTopic();
					break;
				case 'report':
					$newOutput = $this->container->reportPost();
					break;
				case 'permadelete':
					$newOutput = $this->container->permaDelete();
					break;
				case 'request-ban':
					$newOutput = $this->container->requestBan();
					break;
				default:
					$output['view'] = '404';
					break;
			}
			
			$output = array_merge($newOutput , $output);
			return $output;
			
		}
		else{
			if($this->data['user']){
				Tokenly\POP_Model::recordFirstView($this->data['user']['userId'], $this->data['module']['moduleId'], $getTopic['topicId']);
			}	
			
		}

		$profModel = new Profile\User_Model;
		$getTopic['author'] = $profModel->getUserProfile($getTopic['userId'], $this->data['site']['siteId']);
		$output['board'] = $getBoard;
		$output['topic'] = $getTopic;
		$output['view'] = 'topic';
		$output['title'] = $getTopic['title'].' - '.$getBoard['name'];
		
		$output['page'] = 1;
		$output['totalReplies'] = Post_Model::getNumTopicReplies($this->topic['topicId']);
		$output['numPages'] = Post_Model::getNumTopicPages($this->topic['topicId']);
		if(isset($_GET['page'])){
			$page = intval($_GET['page']);
			if($page > 1 AND $page <= $output['numPages']){
				$output['page'] = $page;
			}
		}
		
		$output['replies'] = $this->model->getTopicReplies($getTopic['topicId'], $this->data, $output['page']);
		
		if($this->data['user'] AND posted()){
			$output = array_merge($output, $this->container->postReply());
			return $output;
		}

		$output['reportedPosts'] = false;
		if($this->data['user']){
			//reply form
			$output['form'] = $this->model->getReplyForm();
			$postCount = Account\Home_Model::getUserPostCount($this->data['user']['userId']);
			$checkCaptcha = false;
			if(isset($this->data['app']['meta']['min-posts-captcha'])){
				$minPosts = intval($this->data['app']['meta']['min-posts-captcha']);
				if($postCount <= $minPosts){
					$captcha = new UI\Captcha();
					$output['form']->add($captcha);
					$checkCaptcha = true;
				}
			}
			
			//post reporting
			$meta = new \App\Meta_Model;
			$output['reportedPosts'] = $meta->getUserMeta($this->data['user']['userId'], 'reportedPosts');
			if($output['reportedPosts']){
				$output['reportedPosts'] = json_decode($output['reportedPosts'], true);
				$topicReported = extract_row($output['reportedPosts'], array('type' => 'topic', 'itemId' => $getTopic['topicId']));
				if($topicReported){
					$output['topic']['isReported'] = true;
				}
				foreach($output['replies'] as &$reply){
					$replyReported = extract_row($output['reportedPosts'], array('type' => 'post', 'itemId' => $reply['postId']));
					if($replyReported){
						$reply['isReported'] = true;
					}
				}
			}
			
			//record new topic replices
			$viewed_topics = array();
			if(isset($this->data['user']['meta']['viewed_forum_replies'])){
				$viewed_topics = json_decode($this->data['user']['meta']['viewed_forum_replies'], true);
				if(!is_array($viewed_topics)){
					$viewed_topics = array();
				}
			}
			if(!isset($viewed_topics[$getTopic['topicId']]) OR $viewed_topics[$getTopic['topicId']] != $output['totalReplies']){
				$viewed_topics[$getTopic['topicId']] = $output['totalReplies'];
				$meta->updateUserMeta($this->data['user']['userId'], 'viewed_forum_replies', json_encode($viewed_topics));
			}
		}
		
		if(!$this->data['user'] OR ($this->data['user'] AND $this->data['user']['userId'] != $getTopic['userId'])){
			$viewed_topics = Session::get('viewed_topics', array());
			if(!in_array($getTopic['topicId'], $viewed_topics)){
				$this->model->edit('forum_topics', $getTopic['topicId'], array('views' => ($getTopic['views'] + 1)));
				Session::set('viewed_topics', $getTopic['topicId'], APPEND_ARRAY);
			}
		}
		
		return $output;
	}
	
	protected function postReply()
	{
		$output = array();
		if(!$this->data['user'] OR !$this->data['perms']['canPostReply']){
			$output['view'] = '404';
			return $output;
		}

		$form = $this->model->getReplyForm();
		$postCount = Account\Home_Model::getUserPostCount($this->data['user']['userId']);
		$checkCaptcha = false;
		if(isset($this->data['app']['meta']['min-posts-captcha'])){
			$minPosts = intval($this->data['app']['meta']['min-posts-captcha']);
			if($postCount < $minPosts){
				$captcha = new UI\Captcha();
				$form->add($captcha);
				$checkCaptcha = true;
			}
		}		
		
		$data = $form->grabData();
		
		if($this->topic['locked'] != 0){
			$output['replyMessage'] = 'This thread is locked';
			return $output;
		}
		
		$data['topicId'] = $this->topic['topicId'];
		$data['userId'] = $this->data['user']['userId'];
		try{
			$this->data['topic'] = $this->topic;
			$data['check_captcha'] = $checkCaptcha;
			$post = $this->model->postReply($data, $this->data);
		}
		catch(\Exception $e){
			http_response_code(400);
			$post = false;
			$output['replyMessage'] = $e->getMessage();
			$output['form'] = $form;
			return $output;
		}
		
		$numPages = Post_Model::getNumTopicPages($this->topic['topicId']);
		$page = '';
		if($numPages > 1){
			$page = '?page='.$numPages;
		}
		
		if($post){
			redirect($this->site.'/'.$this->data['app']['url'].'/'.$this->data['module']['url'].'/'.$this->topic['url'].$page.'#post-'.$post['postId']);
		}
		
		return $output;
	}
	
	protected function editPost()
	{
		$output = array();
		
		$getPost = $this->model->get('forum_posts', $this->args[4]);
		if(!$this->data['user'] OR !$getPost OR $getPost['buried'] == 1
			OR (($getPost['userId'] != $this->data['user']['userId'] AND !$this->data['perms']['canEditOther'])
			OR ($getPost['userId'] == $this->data['user']['userId'] AND !$this->data['perms']['canEditSelf']))){
			$output['view'] = '403';
			return $output;
		}
		
		$output['view'] = 'post-form';
		$output['form'] = $this->model->getReplyForm();
		$output['form']->setValues($getPost);
		$output['post'] = $getPost;
		$output['title'] = 'Edit Post - '.$this->topic['title'];
		$output['message'] = '';
		$output['topic'] = $this->topic;
		$output['board'] = $this->board;
		if(!isset($_GET['retpage'])){
			$_GET['retpage'] = 0;
		}
		$output['permaPage'] = (($returnPage = intval($_GET['retpage'])) > 1 ? '?page='.$returnPage : '').'#post-'.$getPost['postId'];
		
		if(posted()){
			$data = $output['form']->grabData();
			
			try{
				$this->data['topic'] = $this->topic;
				$edit = $this->model->editPost($getPost['postId'], $data, $this->data);
			}
			catch(\Exception $e){
				$edit = false;
				$output['message'] = $e->getMessage();
			}
			
			if($edit){
				redirect($this->data['site']['url'].$this->moduleUrl.'/'.$this->topic['url'].$output['permaPage']);
			}
		}
		
		return $output;
	}
	
	protected function editTopic()
	{
		$output = array();
		
		if(!$this->data['user']
			OR (($this->topic['userId'] != $this->data['user']['userId'] AND !$this->data['perms']['canEditOther'])
			OR ($this->topic['userId'] == $this->data['user']['userId'] AND !$this->data['perms']['canEditSelf']))){
			$output['view'] = '403';
			return $output;
		}
		
		$boardModel = new Board_Model;
		$output['view'] = '../Board/topic-form';
		$output['form'] = $boardModel->getTopicForm();
		$output['form']->setValues($this->topic);
		$output['board'] = $this->board;
		$output['topic'] = $this->topic;
		$output['message'] = '';
		$output['title'] = 'Edit Thread - '.$this->topic['title'];
		$output['mode'] = 'edit';
		
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$edit = $this->model->editTopic($this->topic['topicId'], $data, $this->data);
			}
			catch(\Exception $e){
				$output['message'] = $e->getMessage();
				$edit = false;
			}
			
			if($edit){
				redirect($this->data['site']['url'].$this->moduleUrl.'/'.$edit['url']);
			}
		}
		
		return $output;
	}
	
	protected function deletePost()
	{
		$output = array();
		
		$getPost = $this->model->get('forum_posts', $this->args[4]);
		if(!$this->data['user'] OR !$getPost OR $getPost['buried'] == 1
			OR (($getPost['userId'] != $this->data['user']['userId'] AND !$this->data['perms']['canBuryOther'])
			OR ($getPost['userId'] == $this->data['user']['userId'] AND !$this->data['perms']['canBurySelf']))){
			$output['view'] = '403';
			return $output;
		}
		
		$delete = $this->model->edit('forum_posts', $getPost['postId'], array('buried' => 1, 'buriedBy' => $this->data['user']['userId'], 'buryTime' => timestamp()));
		$permaPage = ($returnPage = intval($_GET['retpage'])) > 1 ? '?page='.$returnPage : '';
		redirect($this->data['site']['url'].$this->moduleUrl.'/'.$this->topic['url'].$permaPage);
		
		return $output;
	}
	
	protected function deleteTopic()
	{
		$output = array();
		
		if(!$this->data['user']
			OR (($this->topic['userId'] != $this->data['user']['userId'] AND !$this->data['perms']['canDeleteOtherTopic'])
			OR ($this->topic['userId'] == $this->data['user']['userId'] AND !$this->data['perms']['canDeleteSelfTopic']))){
			$output['view'] = '403';
			return $output;
		}
		
		$delete = $this->model->edit('forum_topics', $this->topic['topicId'], array('buried' => 1, 'buriedBy' => $this->data['user']['userId'], 'buryTime' => timestamp()));
		redirect($this->data['site']['url'].'/'.$this->data['app']['url'].'/board/'.$this->board['slug']);

		return $output;
	}
	
	protected function lockTopic()
	{
		$output = array();

		if(!$this->data['user']
			OR (($this->topic['userId'] != $this->data['user']['userId'] AND !$this->data['perms']['canLockOther'])
			OR ($this->topic['userId'] == $this->data['user']['userId'] AND !$this->data['perms']['canLockSelf']))){
			$output['view'] = '403';
			return $output;
		}
		
		$lock = $this->model->edit('forum_topics', $this->topic['topicId'], array('locked' => 1, 'lockTime' => timestamp(), 'lockedBy' => $this->data['user']['userId']));
		redirect($this->data['site']['url'].$this->moduleUrl.'/'.$this->topic['url']);
		
		return $output;
	}

	protected function unlockTopic()
	{
		$output = array();

		if(!$this->data['user']
			OR (($this->topic['userId'] != $this->data['user']['userId'] AND !$this->data['perms']['canLockOther'])
			OR ($this->topic['userId'] == $this->data['user']['userId'] AND !$this->data['perms']['canLockSelf']))){
			$output['view'] = '403';
			return $output;
		}
		
		$lock = $this->model->edit('forum_topics', $this->topic['topicId'], array('locked' => 0));
		redirect($this->data['site']['url'].$this->moduleUrl.'/'.$this->topic['url']);
		
		return $output;
	}
	
	protected function stickyTopic()
	{
		$output = array();

		if(!$this->data['user']
			OR (($this->topic['userId'] != $this->data['user']['userId'] AND !$this->data['perms']['canStickyOther'])
			OR ($this->topic['userId'] == $this->data['user']['userId'] AND !$this->data['perms']['canStickySelf']))){
			$output['view'] = '403';
			return $output;
		}
		
		$sticky = $this->model->edit('forum_topics', $this->topic['topicId'], array('sticky' => 1));
		redirect($this->data['site']['url'].$this->moduleUrl.'/'.$this->topic['url']);
		
		return $output;
	}

	protected function unstickyTopic()
	{
		$output = array();

		if(!$this->data['user']
			OR (($this->topic['userId'] != $this->data['user']['userId'] AND !$this->data['perms']['canStickyOther'])
			OR ($this->topic['userId'] == $this->data['user']['userId'] AND !$this->data['perms']['canStickySelf']))){
			$output['view'] = '403';
			return $output;
		}
		
		$sticky = $this->model->edit('forum_topics', $this->topic['topicId'], array('sticky' => 0));
		redirect($this->data['site']['url'].$this->moduleUrl.'/'.$this->topic['url']);
		
		return $output;
	}
	
	protected function moveTopic()
	{
		$output = array();
		if(!$this->data['user']
			OR (($this->topic['userId'] != $this->data['user']['userId'] AND !$this->data['perms']['canMoveOther'])
			OR ($this->topic['userId'] == $this->data['user']['userId'] AND !$this->data['perms']['canMoveSelf']))){
			$output['view'] = '403';
			return $output;
		}
		
		$output['view'] = 'move-topic';
		$output['form'] = $this->model->getMoveTopicForm($this->data['site'], $this->data['user']);
		$output['topic'] = $this->topic;
		$output['board'] = $this->board;
		$output['message'] = '';
		$output['title'] = 'Move Thread - '.$this->topic['title'];
		$output['form']->setValues($this->topic);
		
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$move = $this->model->moveTopic($this->topic['topicId'], $data, $this->data['user']);
			}
			catch(\Exception $e){
				$output['message'] = $e->getMessage();
				$move = false;
			}
			
			$boardModule = get_app('forum.forum-board');
			if($move){
				redirect($this->data['site']['url'].'/'.$this->data['app']['url'].'/'.$boardModule['url'].'/'.$move['slug']);
			}
		}
		return $output;
	}
	
	protected function likeTopic()
	{
		ob_end_clean();
		header('Content-Type: text/json');
		$output = array();
		if(!$this->data['user']){
			http_response_code(400);
			$output['error'] = 'Not logged in';
			echo json_encode($output);
			die();
		}
		
		if(!$this->data['perms']['canUpvoteDownvote']){
			http_response_code(403);
			$output['error'] = 'You do not have permission for this';
			echo json_encode($output);
			die();
		}		
		
		$getLike = $this->model->fetchSingle('SELECT *
											  FROM user_likes
											  WHERE userId = :userId AND itemId = :id AND type = "topic"',
											 array(':userId' => $this->data['user']['userId'], ':id' => $this->topic['topicId']));
		if($getLike){
			http_response_code(400);
			$output['error'] = 'Already liked';
			echo json_encode($output);
			die();
		}
		
		$inventory = new Tokenly\Inventory_Model;
		$getScore = $inventory->getWeightedUserTokenScore($this->data['user']['userId'], $this->topic['userId'], 
															$this->data['app']['meta']['weighted-votes-token'], 
															$this->data['app']['meta']['min-upvote-points'], 
															$this->data['app']['meta']['max-upvote-points'], 1000,
															$this->data['app']['meta']['weighted-vote-token-cap']);
															
		if($this->data['user']['userId'] == $this->topic['userId']){
			$getScore['score'] = 0;
		}
		
		$like = $this->model->insert('user_likes', array('userId' => $this->data['user']['userId'],
														'itemId' => $this->topic['topicId'], 'type' => 'topic', 'likeTime' => timestamp(),
														'score' => $getScore['score'],
														'opUser' => $this->topic['userId']));
		if(!$like){
			http_response_code(400);
			$output['error'] = 'Error adding like';
			echo json_encode($output);
			die();
		}
		
		$notifyData = $this->data;
		$notifyData['topic'] = $this->topic;
		\App\Meta_Model::notifyUser($this->topic['userId'], 'emails.likeThreadNotice', $this->topic['topicId'], 
										 'like-topic-'.$this->data['user']['userId'], false, $notifyData);
		
		$output['result'] = 'success';
		echo json_encode($output);
		die();
	}
	
	protected function unlikeTopic()
	{
		ob_end_clean();
		header('Content-Type: text/json');
		$output = array();
		if(!$this->data['user']){
			http_response_code(400);
			$output['error'] = 'Not logged in';
			echo json_encode($output);
			die();
		}
		
		$getLike = $this->model->fetchSingle('SELECT *
											  FROM user_likes
											  WHERE userId = :userId AND itemId = :id AND type = "topic"',
											 array(':userId' => $this->data['user']['userId'], ':id' => $this->topic['topicId']));
		if(!$getLike){
			http_response_code(400);
			$output['error'] = 'Not yet liked..';
			echo json_encode($output);
			die();
		}
		
		$like = $this->model->delete('user_likes', $getLike['likeId']);
		if(!$like){
			http_response_code(400);
			$output['error'] = 'Error un-like-ing';
			echo json_encode($output);
			die();
		}
		
		$output['result'] = 'success';
		echo json_encode($output);
		die();
	}
	
	protected function likePost()
	{
		ob_end_clean();
		header('Content-Type: text/json');
		$output = array();
		if(!$this->data['user']){
			http_response_code(400);
			$output['error'] = 'Not logged in';
			echo json_encode($output);
			die();
		}
		
		$getPost = $this->model->get('forum_posts', $this->args[4]);
		if(!$getPost OR $getPost['topicId'] != $this->topic['topicId']){
			http_response_code(400);
			$output['error'] = 'Invalid post';
			echo json_encode($output);
			die();
		}
		
		if(!$this->data['perms']['canUpvoteDownvote']){
			http_response_code(403);
			$output['error'] = 'You do not have permission for this';
			echo json_encode($output);
			die();
		}
		
		$getLike = $this->model->fetchSingle('SELECT *
											  FROM user_likes
											  WHERE userId = :userId AND itemId = :id AND type = "post"',
											 array(':userId' => $this->data['user']['userId'], ':id' => $getPost['postId']));
		if($getLike){
			http_response_code(400);
			$output['error'] = 'Already liked';
			echo json_encode($output);
			die();
		}
		
		$inventory = new Tokenly\Inventory_Model;
		$getScore = $inventory->getWeightedUserTokenScore($this->data['user']['userId'], $getPost['userId'], 
															$this->data['app']['meta']['weighted-votes-token'], 
															$this->data['app']['meta']['min-upvote-points'], 
															$this->data['app']['meta']['max-upvote-points'], 1000,
															$this->data['app']['meta']['weighted-vote-token-cap']);
															
		if($this->data['user']['userId'] == $getPost['userId']){
			$getScore['score'] = 0;
		}															
		
		$like = $this->model->insert('user_likes', array('userId' => $this->data['user']['userId'],
														'itemId' => $getPost['postId'], 'type' => 'post', 'likeTime' => timestamp(),
														'score' => $getScore['score'],
														'opUser' => $getPost['userId']));
		if(!$like){
			http_response_code(400);
			$output['error'] = 'Error adding like';
			echo json_encode($output);
			die();
		}
		
		$postPage = $this->model->getPostPage($getPost['postId'], $this->data['app']['meta']['postsPerPage']);
		$andPage = '';
		if($postPage > 1){
			$andPage = '?page='.$postPage;
		}
		
		if($getPost['userId'] != $this->data['user']['userId']){
			$notifyData = $this->data;
			$notifyData['topic'] = $this->topic;
			$notifyData['page'] = $andPage;
			$notifyData['post'] = $getPost;
			\App\Meta_Model::notifyUser($getPost['userId'], 'emails.likePostNotice', $getPost['postId'], 'like-post-'.$this->data['user']['userId'], false, $notifyData);
		}
		
		$output['result'] = 'success';
		echo json_encode($output);
		die();
	}
	
	protected function unlikePost()
	{
		ob_end_clean();
		header('Content-Type: text/json');
		$output = array();
		if(!$this->data['user']){
			http_response_code(400);
			$output['error'] = 'Not logged in';
			echo json_encode($output);
			die();
		}
		
		$getPost = $this->model->get('forum_posts', $this->args[4]);
		if(!$getPost OR $getPost['topicId'] != $this->topic['topicId']){
			http_response_code(400);
			$output['error'] = 'Invalid post';
			echo json_encode($output);
			die();
		}
		
		$getLike = $this->model->fetchSingle('SELECT *
											  FROM user_likes
											  WHERE userId = :userId AND itemId = :id AND type = "post"',
											 array(':userId' => $this->data['user']['userId'], ':id' => $getPost['postId']));
		if(!$getLike){
			http_response_code(400);
			$output['error'] = 'Not yet liked..';
			echo json_encode($output);
			die();
		}
		
		$like = $this->model->delete('user_likes', $getLike['likeId']);
		if(!$like){
			http_response_code(400);
			$output['error'] = 'Error un-like-ing';
			echo json_encode($output);
			die();
		}
		
		$output['result'] = 'success';
		echo json_encode($output);
		die();
	}
	
	protected function subscribeTopic()
	{
		ob_end_clean();
		header('Content-Type: text/json');
		$output = array();
		if(!$this->data['user']){
			http_response_code(400);
			$output['error'] = 'Not logged in';
			echo json_encode($output);
			die();
		}
		
		$getSubs = $this->model->getAll('forum_subscriptions', array('userId' => $this->data['user']['userId'], 'topicId' => $this->topic['topicId']));
		
		if(count($getSubs) > 0){
			$output['error'] = 'Already subscribed to this topic!';
		}
		else{
			$insert = $this->model->insert('forum_subscriptions', array('userId' => $this->data['user']['userId'], 'topicId' => $this->topic['topicId']));
			if(!$insert){
				$output['error'] = 'Error subscribing, please try again';
			}
			else{
				$output['result'] = 'success';
			}
		}
		
		echo json_encode($output);
		die();
	}
	
	protected function unsubscribeTopic()
	{
		ob_end_clean();
		header('Content-Type: text/json');
		$output = array();
		if(!$this->data['user']){
			http_response_code(400);
			$output['error'] = 'Not logged in';
			echo json_encode($output);
			die();
		}
		$getSubs = $this->model->getAll('forum_subscriptions', array('userId' => $this->data['user']['userId'], 'topicId' => $this->topic['topicId']));
		
		if(count($getSubs) == 0){
			$output['error'] = 'Not yet subscribed to this topic!';
		}
		else{
			$delete = $this->model->sendQuery('DELETE FROM forum_subscriptions WHERE userId = :userId AND topicId = :topicId',
							array(':userId' => $this->data['user']['userId'], ':topicId' => $this->topic['topicId']));
			if(!$delete){
				$output['error'] = 'Error unsubscribing, please try again';
			}
			else{
				$output['result'] = 'success';
			}
		}
		
		
		echo json_encode($output);
		die();
	}
	
	protected function reportPost()
	{
		ob_end_clean();
		header('Content-Type: text/json');
		$output = array();
		if(!$this->data['user']){
			http_response_code(400);
			$output['error'] = 'Not logged in';
			echo json_encode($output);
			die();
		}

		$meta = new \App\Meta_Model;
		$reportedPosts = $meta->getUserMeta($this->data['user']['userId'], 'reportedPosts');
		if(!$reportedPosts){
			$reportedPosts = array();
		}
		else{
			$reportedPosts = json_decode($reportedPosts, true);
		}
		
		if(!isset($_POST['itemId']) OR !isset($_POST['type'])){
			http_response_code(400);
			$output['error'] = 'Invalid parameters';
			echo json_encode($output);
			die();
		}
		
		$getItem = false;
		$reportMessage = 'a post';
		switch($_POST['type']){
			case 'topic':
				$getItem = $this->model->get('forum_topics', $_POST['itemId']);
				break;
			case 'post':
				$getItem = $this->model->get('forum_posts', $_POST['itemId']);
				if($getItem){
					$getTopic = $this->model->get('forum_topics', $getItem['topicId']);
					$getPoster = $this->model->get('users', $getItem['userId'], array('userId', 'slug', 'username'));
					$postPage = $this->model->getPostPage($getItem['postId'], $this->data['app']['meta']['postsPerPage']);
					$getItem['topic'] = $getTopic;
					$getItem['poster'] = $getPoster;
					$getItem['postPage'] = $postPage;
					$getItem['boardId'] = $getTopic['boardId'];
				}

				break;
		}
		
		if(!$getItem){
			http_response_code(400);
			$output['error'] = 'Post not found';
			echo json_encode($output);
			die();
		}
		
		if($getItem['userId'] == $this->data['user']['userId']){
			http_response_code(400);
			$output['error'] = 'Cannot flag your own post';
			echo json_encode($output);
			die();
		}
		
		foreach($reportedPosts as $report){
			$hasReported = false;
			switch($report['type']){
				case 'topic':
					if(isset($getItem['topicId']) AND $getItem['topicId'] == $report['itemId']){
						$hasReported = true;
					}
					break;
				case 'post':
					if(isset($getItem['postId']) AND $getItem['postId'] == $report['itemId']){
						$hasReported = true;
					}
					break;
			}
			if($hasReported){
				http_response_code(400);
				$output['error'] = 'Post already reported';
				echo json_encode($output);
				die();
			}
		}
		
		//notify users
		$getPerms = $this->model->getAll('app_perms', array('appId' => $this->data['app']['appId']));
		$getPerm = extract_row($getPerms, array('permKey' => 'canReceiveReports'));
		if($getPerm){
			$getPerm = $getPerm[0];
			$notifyList = array();
			
			//check for forum mods
			$getMods = $this->model->getAll('forum_mods', array('boardId' => $getItem['boardId']));
			if(count($getMods) > 0){
				foreach($getMods as $mod){
					if(!in_array($mod['userId'], $notifyList)){
						$notifyList[] = $mod['userId'];
					}
				}
			}
			else{
				$permGroups = $this->model->getAll('group_perms', array('permId' => $getPerm['permId']));
				foreach($permGroups as $permGroup){
					$groupUsers = $this->model->getAll('group_users', array('groupId' => $permGroup['groupId']));
					foreach($groupUsers as $gUser){
						if(!in_array($gUser['userId'], $notifyList)){
							$notifyList[] = $gUser['userId'];
						}
					}
				}
			}
			
			foreach($notifyList as $notifyUser){
				if($notifyUser == $this->data['user']['userId']){
					continue;
				}
				$notifyData = $this->data;
				$notifyData['item'] = $getItem;
				$notifyData['notifyUser'] = $notifyUser;
				$notify = \App\Meta_Model::notifyUser($notifyUser, 'emails.flagPostNotice', $_POST['itemId'], 'report-'.$_POST['type'], true, $notifyData);
				
			}
		}
		
		$reportedPosts[] = array('type' => $_POST['type'], 'itemId' => $_POST['itemId']);
		$update = $meta->updateUserMeta($this->data['user']['userId'], 'reportedPosts', json_encode($reportedPosts));
		if(!$update){
			http_response_code(400);
			$output['error'] = 'Error reporting post';
			echo json_encode($output);
			die();
		}
		
		$output['result'] = 'success';
		
		echo json_encode($output);
		die();
	}
	
	protected function checkModPerms($boardId, $appData)
	{
		if(isset($appData['app']['meta']['mod-group'])){
			$modGroup = $this->model->get('groups', $appData['app']['meta']['mod-group']);
			if($modGroup){
				$forumMod = $this->model->getAll('forum_mods', array('userId' => $appData['user']['userId'], 'boardId' => $boardId));
				if($forumMod AND count($forumMod) > 0){
					$forumMod = $forumMod[0];
					$groupPerms = $this->model->getAll('group_perms', array('groupId' => $modGroup['groupId']));
					foreach($groupPerms as $perm){
						$getPerm = $this->model->get('app_perms', $perm['permId']);
						if(isset($appData['perms'][$getPerm['permKey']])){
							$appData['perms'][$getPerm['permKey']] = true;
						}
					}					
				}
			}
		}
		return $appData['perms'];
	}
	
	protected function permaDelete()
	{
		$output = array();
		if(!$this->data['user']){
			$output['view'] = '403';
			return $output;
		}
		
		if(isset($this->args[4])){
			if(!$this->data['perms']['canPermaDeletePost']){
				$output['view'] = '403';
				return $output;
			}
			$getPost = $this->model->get('forum_posts', $this->args[4]);
			if(!$getPost OR $getPost['topicId'] != $this->topic['topicId']){
				$output['view'] = '404';
				return $output;
			}
			
			$delete = $this->model->delete('forum_posts', $getPost['postId']);
			$permaPage = ($returnPage = intval($_GET['retpage'])) > 1 ? '?page='.$returnPage : '';
			redirect($this->data['site']['url'].$this->moduleUrl.'/'.$this->topic['url'].$permaPage);
		}
		else{
			if(!$this->data['perms']['canPermaDeleteTopic']){
				$output['view'] = '403';
				return $output;
			}
			$delete = $this->model->delete('forum_topics', $this->topic['topicId']);
			redirect($this->data['site']['url'].'/'.$this->data['app']['url'].'/board/'.$this->board['slug']);
		}
		
		return $output;
	}
	
	protected function requestBan()
	{
		ob_end_clean();
		header('Content-Type: text/json');		
		$output = array();
		
		if(!$this->data['user'] OR !$this->data['perms']['canRequestBan']){
			http_response_code(403);
			$output['error'] = 'You do not have permission';
			echo json_encode($output);
			die();
		}
		
		if(!posted() OR !isset($this->args[4])){
			http_response_code(400);
			$output['error'] = 'Invalid request';
			echo json_encode($output);
			die();
		}
		
		$getUser = $this->model->get('users', $this->args[4]);
		if(!$getUser){
			http_response_code(404);
			$output['error'] = 'User not found';
			echo json_encode($output);
			die();
		}
		
		$message = '';
		if(isset($_POST['message'])){
			$message = htmlentities(trim($_POST['message']));
		}
		
		$getPerms = $this->model->getAll('app_perms', array('appId' => $this->data['app']['appId']));
		$getPerm = extract_row($getPerms, array('permKey' => 'canReceiveBanRequest'));
		if($getPerm){
			$getPerm = $getPerm[0];
			$notifyList = array();
		
			$permGroups = $this->model->getAll('group_perms', array('permId' => $getPerm['permId']));
			foreach($permGroups as $permGroup){
				$groupUsers = $this->model->getAll('group_users', array('groupId' => $permGroup['groupId']));
				foreach($groupUsers as $gUser){
					if(!in_array($gUser['userId'], $notifyList)){
						$notifyList[] = $gUser['userId'];
					}
				}
			}
			
			foreach($notifyList as $notifyUser){
				if($notifyUser == $this->data['user']['userId']){
					continue;
				}
				$notifyData = $this->data;
				$notifyData['banUser'] = $getUser;
				$notifyData['banMessage'] = $message;
				$notifyData['notifyUser'] = $notifyUser;
				$notify = \App\Meta_Model::notifyUser($notifyUser, 'emails.banRequestNotice', $getUser['userId'], 'banrequest', true, $notifyData);
				
			}
		}		
		
		$output['result'] = 'success';
		echo json_encode($output);
		die();		
		return $output;
	}
}
