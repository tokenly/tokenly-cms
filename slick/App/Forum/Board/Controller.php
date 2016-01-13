<?php
namespace App\Forum;
use App\Tokenly, App\Account, UI;
class Board_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Board_Model;
		$this->tca = new Tokenly\TCA_Model;
	}
	
	protected function init()
	{
		$output = parent::init();
		
		if(!isset($this->args[2])){
			$output['view'] = '404';
			return $output;
		}
		
		if($this->args[2] == 'all'){
			return $this->container->showAllTopics($output);
		}
		if($this->args[2] == 'subscriptions'){
			return $this->container->showSubscribedTopics($output);
		}
		if($this->args[2] == 'tca-posts'){
			return $this->container->showTCATopics($output);
		}
		
		$getBoard = $this->model->get('forum_boards', $this->args[2], array(), 'slug');
		if(!$getBoard OR $getBoard['siteId'] != $this->data['site']['siteId'] OR $getBoard['active'] == 0){
			$output['view'] = '404';
			return $output;
		}			
		
		$checkTCA = $this->tca->checkItemAccess($this->data['user'], $this->data['module']['moduleId'], $getBoard['boardId'], 'board');
		if(!$checkTCA){
			$output['view'] = '403';
			return $output;
		}
		
		if($this->data['user']){
			$postControl = new Post_Controller;
			$output['perms'] = $postControl->checkModPerms($getBoard['boardId'], $this->data);
			$output['perms'] = $this->tca->checkPerms($this->data['user'], $output['perms'], $this->data['module']['moduleId'], $getBoard['boardId'], 'board');
			$this->data['perms'] = $output['perms'];
		}			
		
		$this->board = $getBoard;
		$newOutput = false;
		if(isset($this->args[3])){
			switch($this->args[3]){
				case 'subscribe':
					$newOutput = $this->container->subscribeBoard();
					break;
				case 'unsubscribe':
					$newOutput = $this->container->unsubscribeBoard();
					break;
				case 'post':
					$newOutput = $this->container->postTopic();
					break;
				default:
					$output['view'] = '404';
					return $output;
				
			}
		}
		if($newOutput != false){
			$output = array_merge($output, $newOutput);
			return $output;
		}
		
		$dashModel = new Boards_Model;
		
		$output['board'] = $getBoard;
		$output['title'] = $getBoard['name'];
		$output['view'] = 'board';
		$output['totalTopics'] = $this->model->count('forum_topics', 'boardId', $getBoard['boardId']);
		$output['numPages'] = ceil($output['totalTopics'] / $this->data['app']['meta']['topicsPerPage']);
		$output['page'] = 1;
		$output['isAll'] = false;
		$output['moderators'] = $dashModel->getBoardMods($getBoard['boardId']);
		if(isset($_GET['page'])){
			$page = intval($_GET['page']);
			if($page > 1 AND $page <= $output['numPages']){
				$output['page'] = $page;
			}
		}
		$output['topics'] = $this->model->getBoardTopics($getBoard['boardId'], $this->data, $output['page']);
        $output['stickies'] = $this->model->getBoardStickyPosts($this->data, $getBoard['boardId']);


		if($this->data['user']){
			Tokenly\POP_Model::recordFirstView($this->data['user']['userId'], $this->data['module']['moduleId'], $getBoard['boardId']);
		}

		return $output;
	}
	
	protected function postTopic()
	{
		$output = array();
		
		if(!$this->data['user'] OR !$this->data['perms']['canPostTopic']){
			$output['view'] = '403';
			return $output;
		}
		
		$output['form'] = $this->model->getTopicForm();
		$output['view'] = 'topic-form';
		$output['board'] = $this->board;
		$output['title'] = 'New Topic - '.$this->board['name'];
		$output['message'] = '';
		
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
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['userId'] = $this->data['user']['userId'];
			$data['boardId'] = $output['board']['boardId'];
			$data['check_captcha'] = $checkCaptcha;
			
			try{
				$post = $this->model->postTopic($data, $this->data);
			}
			catch(\Exception $e){
				$output['message'] = $e->getMessage();
				$output['form']->setValues($data);
				$post = false;
			}
			
			if($post){
				redirect($this->data['site']['url'].'/'.$this->data['app']['url'].'/post/'.$post['url']);
			}
			
		}
		
		return $output;
	}
	
	protected function showAllTopics($output)
	{
		$output['board'] = false;
		$output['isAll'] = true;
		$output['title'] = 'Recent Posts';
		$output['slug'] = 'all';
		$output['view'] = 'board';
		
		if(posted() AND isset($_POST['boardFilters'])){
			$update = $this->model->updateBoardFilters($this->data['user'], $_POST['boardFilters']);
			redirect($this->data['site']['url'].'/'.$this->data['app']['url'].'/'.$this->data['module']['url'].'/all');
		}
		
		$output['boardFilters'] = $this->model->getBoardFilters($this->data['user']);
		$output['totalTopics'] = $this->model->countFilteredTopics($output['boardFilters']);

		
		$output['numPages'] = ceil($output['totalTopics'] / $this->data['app']['meta']['topicsPerPage']);
		$output['page'] = 1;
		if(isset($_GET['page'])){
			$page = intval($_GET['page']);
			if($page > 1 AND $page <= $output['numPages']){
				$output['page'] = $page;
			}
		}
		$output['topics'] = $this->model->getBoardTopics(0, $this->data, $output['page'], true);
		$output['stickies'] = array();
		$numTopics = $this->model->fetchSingle('SELECT count(*) as total
												FROM forum_topics t
												LEFT JOIN forum_boards b ON b.boardId = t.boardId
												WHERE b.siteId = :siteId', array(':siteId' => $this->data['site']['siteId']));
		$output['numTopics'] = $numTopics['total'];

		$numReplies = $this->model->fetchSingle('SELECT count(*) as total
												FROM forum_posts p
												LEFT JOIN forum_topics t ON t.topicId = p.topicId
												LEFT JOIN forum_boards b ON b.boardId = t.boardId
												WHERE b.siteId = :siteId', array(':siteId' => $this->data['site']['siteId']));
		$output['numReplies'] = $numReplies['total'];
		$output['numUsers'] = $this->model->count('users');
		$output['numOnline'] = Account\Home_Model::getUsersOnline();
		$output['mostOnline'] = Account\Home_Model::getMostOnline();
		$output['onlineUsers'] = Account\Home_Model::getOnlineUsers();		
		
		return $output;
	}
	
	protected function showSubscribedTopics($output)
	{
		$output['board'] = false;
		$output['isAll'] = true;
		$output['title'] = 'Subscribed Topics';
		$output['slug'] = 'subscriptions';
		$output['view'] = 'board';
		$per_page = $this->data['app']['meta']['topicsPerPage'];
		$output['totalTopics'] = $this->model->countUserSubscribedTopics();

		$output['numPages'] = ceil($output['totalTopics'] / $per_page);
		$output['page'] = 1;
		$page_start = 0;
		if(isset($_GET['page'])){
			$page = intval($_GET['page']);
			if($page > 1 AND $page <= $output['numPages']){
				$output['page'] = $page;
				$page_start = floor(($per_page * $page) - $per_page);
			}
		}
		$output['stickies'] = array();
		$output['topics'] = $this->model->getUserSubscribedThreads(false, $per_page, $page_start);
		$output['topics'] = $this->model->checkTopicsTCA($output['topics'], $this->data);
								
		$output['topics'] = $this->model->parseTopics($output['topics'], $this->data, true);
		

		return $output;
	}	
	
	protected function showTCATopics($output)
	{
		$output['board'] = false;
		$output['isAll'] = true;
		$output['title'] = 'Token Controlled Access Posts';
		$output['slug'] = 'tca-posts';
		$output['view'] = 'board';
		$per_page = $this->data['app']['meta']['topicsPerPage'];
		$output['totalTopics'] = $this->model->countUserTCATopics();

		$output['numPages'] = ceil($output['totalTopics'] / $per_page);
		$output['page'] = 1;
		$page_start = 0;
		if(isset($_GET['page'])){
			$page = intval($_GET['page']);
			if($page > 1 AND $page <= $output['numPages']){
				$output['page'] = $page;
				$page_start = floor(($per_page * $page) - $per_page);
			}
		}
		$output['stickies'] = array();
		$output['topics'] = $this->model->getUserTCAThreads(false, $per_page, $page_start);
		$output['topics'] = $this->model->checkTopicsTCA($output['topics'], $this->data);
								
		$output['topics'] = $this->model->parseTopics($output['topics'], $this->data, true);
		

		return $output;
	}		


	protected function subscribeBoard()
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
		
		$getSubs = $this->model->getAll('board_subscriptions', array('userId' => $this->data['user']['userId'], 'boardId' => $this->board['boardId']));
		
		if(count($getSubs) > 0){
			$output['error'] = 'Already subscribed to this topic!';
		}
		else{
			$insert = $this->model->insert('board_subscriptions', array('userId' => $this->data['user']['userId'], 'boardId' => $this->board['boardId']));
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
	
	protected function unsubscribeBoard()
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
		$getSubs = $this->model->getAll('board_subscriptions', array('userId' => $this->data['user']['userId'], 'boardId' => $this->board['boardId']));
		
		if(count($getSubs) == 0){
			$output['error'] = 'Not yet subscribed to this topic!';
		}
		else{
			$delete = $this->model->sendQuery('DELETE FROM board_subscriptions WHERE userId = :userId AND boardId = :boardId',
							array(':userId' => $this->data['user']['userId'], ':boardId' => $this->board['boardId']));
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
}
