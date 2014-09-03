<?php
class Slick_App_Forum_Board_Controller extends Slick_App_ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Slick_App_Forum_Board_Model;
		$this->tca = new Slick_App_LTBcoin_TCA_Model;
		
	}
	
	public function init()
	{
		$output = parent::init();
		
		if(!isset($this->args[2])){
			$output['view'] = '404';
			return $output;
		}
		
		if($this->args[2] == 'all'){
			return $this->showAllTopics($output);
		}
		
		$getBoard = $this->model->get('forum_boards', $this->args[2], array(), 'slug');
		if(!$getBoard OR $getBoard['siteId'] != $this->data['site']['siteId']){
			$output['view'] = '404';
			return $output;
		}
		$checkTCA = $this->tca->checkItemAccess($this->data['user'], $this->data['module']['moduleId'], $getBoard['boardId'], 'board');
		if(!$checkTCA){
			$output['view'] = '403';
			return $output;
		}
		
		$this->board = $getBoard;
		$newOutput = false;
		if(isset($this->args[3])){
			switch($this->args[3]){
				case 'post':
					$newOutput = $this->postTopic();
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
		
		$output['board'] = $getBoard;
		$output['title'] = $getBoard['name'];
		$output['view'] = 'board';
		$output['totalTopics'] = $this->model->count('forum_topics', 'boardId', $getBoard['boardId']);
		$output['numPages'] = ceil($output['totalTopics'] / $this->data['app']['meta']['topicsPerPage']);
		$output['page'] = 1;
		$output['isAll'] = false;
		if(isset($_GET['page'])){
			$page = intval($_GET['page']);
			if($page > 1 AND $page <= $output['numPages']){
				$output['page'] = $page;
			}
		}
		$output['topics'] = $this->model->getBoardTopics($getBoard['boardId'], $this->data, $output['page']);
        $output['stickies'] = $this->model->getBoardStickyPosts($this->data, $getBoard['boardId']);


		if($this->data['user']){
			Slick_App_LTBcoin_POP_Model::recordFirstView($this->data['user']['userId'], $this->data['module']['moduleId'], $getBoard['boardId']);
		}

		return $output;
	}
	
	private function postTopic()
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
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['userId'] = $this->data['user']['userId'];
			$data['boardId'] = $output['board']['boardId'];
			
			try{
				$post = $this->model->postTopic($data, $this->data);
			}
			catch(Exception $e){
				$output['message'] = $e->getMessage();
				$output['form']->setValues($data);
				$post = false;
			}
			
			if($post){
				$this->redirect($this->data['site']['url'].'/'.$this->data['app']['url'].'/post/'.$post['url']);
				return $output;
			}
			
		}
		
		return $output;
	}
	
	private function showAllTopics($output)
	{
		$output['board'] = false;
		$output['isAll'] = true;
		$output['title'] = 'Recent Posts';
		$output['view'] = 'board';
		
		if(posted() AND isset($_POST['boardFilters'])){
			$update = $this->model->updateBoardFilters($this->data['user'], $_POST['boardFilters']);
			$this->redirect($this->data['site']['url'].'/'.$this->data['app']['url'].'/'.$this->data['module']['url'].'/all');
			die();
		}
		
		$output['boardFilters'] = $this->model->getBoardFilters($this->data['user']);
		if(count($output['boardFilters']) > 0){
			$output['totalTopics'] = $this->model->countFilteredTopics($output['boardFilters']);
		}
		else{
			$output['totalTopics'] = $this->model->count('forum_topics');
		}
		
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
		$output['numOnline'] = Slick_App_Account_Home_Model::getUsersOnline();
		$output['mostOnline'] = Slick_App_Account_Home_Model::getMostOnline();
		$output['onlineUsers'] = Slick_App_Account_Home_Model::getOnlineUsers();		
		
		return $output;
	}
}

