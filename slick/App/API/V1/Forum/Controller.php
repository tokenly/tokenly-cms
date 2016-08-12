<?php
namespace App\API\V1;
/**
* Forum API Endpoint 
*
* Provides API capabilities for interacting with forum modules
* 
* @package [App][API][V1][Forum]
* @author Nick Rathman <nrathman@ironcladtech.ca>
* 
*/
class Forum_Controller extends \Core\Controller
{
	public $methods = array('GET', 'POST','PATCH','DELETE'); //set the valid request methods
	
	function __construct()
	{
		parent::__construct();
		$this->model = new Forum_Model;
		$this->tca = new \App\Tokenly\TCA_Model; //load token controlled access functions
		$this->meta = new \App\Meta_Model;
		$this->forumApp = get_app('forum');
		$this->boardModule = get_app('forum.forum-board');
		$this->postModule = get_app('forum.forum-post');
	}

	/**
	* Initializes endpoint and runs requested functions to build output data.
	*
	* @param $args Array data passed in from main API controller
	* @return Array
	*
	*/
	protected function init($args = array())
	{
		$this->args = $args;
		$output = array();
		//load user data if possible
		try{
			$this->user = Auth_Model::getUser($this->args['data']);
		}
		catch(\Exception $e){
			$this->user = false;
		}
		$userId = 0;
		if($this->user){
			$userId = $this->user['userId'];
		}
		$this->args['data']['perms'] = \App\Meta_Model::getUserAppPerms($userId, 'forum');
		$this->args['data']['perms'] = $this->tca->checkPerms($this->user, $this->args['data']['perms'], $this->postModule['moduleId'], 0, '');
		$this->perms = $this->args['data']['perms'];
		
		//check if HTML stripping is requested
		$this->striphtml = false;
		if(isset($args['data']['strip-html']) AND $args['data']['strip-html'] == 'true'){
			$this->striphtml = true;
		}
		if(isset($args[1])){
			switch($args[1]){
				case 'categories':
					$output = $this->container->categories();
					break;
				case 'boards':
					$output = $this->container->boards();
					break;
				case 'threads':
					$output = $this->container->threads();
					break;
				case 'opts':
					$output = $this->container->opts();
					break;
				default:
					http_response_code(400);
					$output['error'] = 'Invalid Request';
					break;		
			}
		}
		else{
			http_response_code(400);
			$output['error'] = 'Invalid Request';
		}		
		return $output;
	}
	
	/**
	* Categories endpoint control. If specified, grabs data for a specific category. Otherwise returns all categories
	*
	* @method GET
	* @return Array
	*
	*/
	protected function categories()
	{
		$output = array();
		$check = $this->checkMethod('GET');
		if(is_array($check)){
			return $check;
		}			
		if(isset($this->args[2])){
			return $this->container->getCategory();
		}
		$getCats = $this->model->getAll('forum_categories', array('siteId' => $this->args['data']['site']['siteId']),
															array('categoryId', 'name', 'rank', 'description', 'slug'), 'rank', 'ASC');
		foreach($getCats as $key => &$cat){
			$checkCatTCA = $this->tca->checkItemAccess($this->user, $this->boardModule['moduleId'], $cat['categoryId'], 'category');
			if(!$checkCatTCA){
				unset($getCats[$key]);
				continue;
			}
			if($this->striphtml){
				$cat['name'] = strip_tags($cat['name']);
				$cat['description'] = strip_tags($cat['description']);
			}
			if(isset($this->args['data']['parse-markdown']) AND (intval($this->args['data']['parse-markdown']) === 1 OR $this->args['data']['parse-markdown'] == 'true')){
				$cat['description'] = markdown($cat['description']);
			}					
			$getBoards = $this->model->getAll('forum_boards', array('categoryId' => $cat['categoryId'], 'active' => 1), 
											   array('boardId', 'name', 'slug', 'rank', 'description'), 'rank', 'ASC');
			foreach($getBoards as $bkey => &$board){
				$checkBoardTCA = $this->tca->checkItemAccess($this->user, $this->boardModule['moduleId'], $board['boardId'], 'board');
				if(!$checkBoardTCA){
					unset($getBoards[$bkey]);
					continue;
				}
				if($this->striphtml){
					$board['name'] = strip_tags($board['name']);
					$board['description'] = strip_tags($board['description']);
				}
				if(isset($this->args['data']['parse-markdown']) AND (intval($this->args['data']['parse-markdown']) === 1 OR $this->args['data']['parse-markdown'] == 'true')){
					$board['description'] = markdown($board['description']);
				}						
			}
			if(count($getBoards) == 0){
				unset($getCats[$key]);
				continue;
			}
			$getBoards = array_values($getBoards);
			$cat['boards'] = $getBoards;
		}				
		$getCats = array_values($getCats);								
		$output['categories'] = $getCats;
		return $output;
	}
	
	/**
	* Grab data for a specific category. If "threads" included in parameters, proxy to /threads endpoint with correct board filters.
	*
	* @method GET
	* @return Array
	*
	*/	
	protected function getCategory()
	{
		$output = array();
		$getCat = $this->model->get('forum_categories', $this->args[2], array('categoryId', 'name', 'rank', 'description', 'slug'), 'slug');
		if(!$getCat){
			$getCat = $this->model->get('forum_categories', $this->args[2], array('categoryId', 'name', 'rank', 'description', 'slug'));
			if(!$getCat){
				http_response_code(404);
				$output['error'] = 'Category not found';
				return $output;
			}
		}
		$checkCatTCA = $this->tca->checkItemAccess($this->user, $this->boardModule['moduleId'], $getCat['categoryId'], 'category');
		if(!$checkCatTCA){
			http_response_code(403);
			$output['error'] = 'You do not have permission to view this';
			return $output;
		}
		
		//check for threads option
		if(isset($this->args[3]) AND $this->args[3] == 'threads'){
			$this->args['data']['categories'] = $getCat['categoryId'];
			$this->args['data']['user'] = $this->user;
			$output['result'] = $this->model->getThreadList($this->args['data']);
			return $output;
		}
		
		if($this->striphtml){
			$getCat['name'] = strip_tags($getCat['name']);
			$getCat['description'] = strip_tags($getCat['description']);
		}	
		if(isset($this->args['data']['parse-markdown']) AND (intval($this->args['data']['parse-markdown']) === 1 OR $this->args['data']['parse-markdown'] == 'true')){
			$getCat['description'] = markdown($getCat['description']);
		}					
		$getCat['boards'] = $this->model->getAll('forum_boards', array('categoryId' => $getCat['categoryId'], 'active' => 1), 
											   array('boardId', 'name', 'slug', 'rank', 'description'), 'rank', 'ASC');
		foreach($getCat['boards'] as $bkey => &$board){
			$checkBoardTCA = $this->tca->checkItemAccess($this->user, $this->boardModule['moduleId'], $board['boardId'], 'board');
			if(!$checkBoardTCA){
				unset($getCat['boards'][$bkey]);
				continue;
			}
			if($this->striphtml){
				$board['name'] = strip_tags($board['name']);
				$board['description'] = strip_tags($board['description']);
			}
			if(isset($this->args['data']['parse-markdown']) AND (intval($this->args['data']['parse-markdown']) === 1 OR $this->args['data']['parse-markdown'] == 'true')){
				$board['description'] = markdown($board['description']);
			}								
		}
		if(count($getCat['boards']) == 0){
			//no boards.. category doesnt exist to outside world
			http_response_code(404);
			$output['error'] = 'Category not found';
			return $output;
		}
		$getCat['boards'] = array_values($getCat['boards']);
		$output['category'] = $getCat;
		return $output;
	}
	
	/**
	* Boards endpoint control. Gets data for a single board if specified, otherwise returns full board list, sorted by category/rank
	*
	* @method GET
	* @return Array
	*
	*/
	protected function boards()
	{
		$output = array();
		$check = $this->checkMethod('GET');
		if(is_array($check)){
			return $check;
		}		
		if(isset($this->args[2])){
			return $this->container->getBoard();
		}		
		$getBoards = $this->model->fetchAll('SELECT b.boardId, b.categoryId, b.name, b.slug, b.rank, b.description
											 FROM forum_boards b
											 LEFT JOIN forum_categories c ON c.categoryId = b.categoryId
											 WHERE b.siteId = :siteId AND b.active = 1
											 ORDER BY c.rank ASC, b.rank ASC', array(':siteId' => $this->args['data']['site']['siteId']));
		foreach($getBoards as $key => &$board){
			$checkCatTCA = $this->tca->checkItemAccess($this->user, $this->boardModule['moduleId'], $board['categoryId'], 'category');
			$checkBoardTCA = $this->tca->checkItemAccess($this->user, $this->boardModule['moduleId'], $board['boardId'], 'board');
			if(!$checkCatTCA OR !$checkBoardTCA){
				unset($getBoards[$key]);
				continue;
			}
			if($this->striphtml){
				$board['name'] = strip_tags($board['name']);
				$board['description'] = strip_tags($board['description']);
			}
			if(isset($this->args['data']['parse-markdown']) AND (intval($this->args['data']['parse-markdown']) === 1 OR $this->args['data']['parse-markdown'] == 'true')){
				$board['description'] = markdown($board['description']);
			}
		}
		$getBoards = array_values($getBoards);
		$output['boards'] = $getBoards;
		return $output;	
	}
	
	/**
	* Get data for a specific board. if "threads" included in parameter, proxy to /threads endpoint with correct board filters
	*
	* @method GET
	* @return Array
	* 
	*/
	protected function getBoard()
	{
		$output = array();
		$getBoard = $this->model->get('forum_boards', $this->args[2], array('boardId', 'categoryId', 'name', 'rank', 'description', 'slug', 'active'), 'slug');
		if(!$getBoard){
			$getBoard = $this->model->get('forum_boards', $this->args[2], array('boardId', 'categoryId', 'name', 'rank', 'description', 'slug', 'active'));
			if(!$getBoard){
				http_response_code(404);
				$output['error'] = 'Board not found';
				return $output;
			}
		}
		if($getBoard['active'] == 0){
			http_response_code(404);
			$output['error'] = 'Board not found';
			return $output;
		}
		unset($getBoard['active']);
		$checkCatTCA = $this->tca->checkItemAccess($this->user, $this->boardModule['moduleId'], $getBoard['categoryId'], 'category');
		$checkBoardTCA = $this->tca->checkItemAccess($this->user, $this->boardModule['moduleId'], $getBoard['boardId'], 'board');
		if(!$checkCatTCA OR !$checkBoardTCA){
			http_response_code(403);
			$output['error'] = 'You do not have permission to view this';
			return $output;
		}
		
		//check for /threads option
		if(isset($this->args[3]) AND $this->args[3] == 'threads'){
			$this->args['data']['user'] = $this->user;
			$this->args['data']['boards'] = $getBoard['boardId'];
			$output['result'] = $this->model->getThreadList($this->args['data']);
			return $output;
		}
		
		if($this->striphtml){
			$getBoard['name'] = strip_tags($getBoard['name']);
			$getBoard['description'] = strip_tags($getBoard['description']);
		}		
		if(isset($this->args['data']['parse-markdown']) AND (intval($this->args['data']['parse-markdown']) === 1 OR $this->args['data']['parse-markdown'] == 'true')){
			$getBoard['description'] = markdown($getBoard['description']);
		}		
		$output['board'] = $getBoard;
		return $output;
	}
	
	/**
	*
	* Controller function for thread listings and actions. If thread ID or URL included, routes to individual thread control.
	* If POST, attempt to post a new thread. Otherwise return a list of recent posts based on input parameters.
	*
	* @return Array
	*
	*/
	protected function threads()
	{
		$output = array();
		//route to individual thread
		if(isset($this->args[2])){
			return $this->container->getThread();
		}
		//check if posting a new thread
		if($this->useMethod == 'POST'){
			return $this->container->postThread();
		}
		//get thread list
		$this->args['data']['user'] = $this->user;
		$output = $this->model->getThreadList($this->args['data']);
		$output['threads'] = array_values($output['threads']);
		return $output;	
	}
	
	/**
	* Grab data for specific thread, route to basic thread actions or to specific post/reply based on arguments
	*
	* @return Array
	*
	*/
	protected function getThread()
	{
		$output = array();
		
		//check if board exists
		$getThread = $this->model->get('forum_topics', $this->args[2]);
		if(!$getThread){
			$getThread = $this->model->get('forum_topics', $this->args[2], array(), 'url');
		}
		if(!$getThread OR $getThread['buried'] == 1 OR $getThread['trollPost'] == 1){
			http_response_code(404);
			$output['error'] = 'Post not found';
			return $output;
		}
		
		//check TCA
		$checkTCA = $this->tca->checkItemAccess($this->user, $this->postModule['moduleId'], $getThread['topicId'], 'topic');
		if(!$checkTCA){
			http_response_code(403);
			$output['error'] = 'You do not have permission to view this thread';
			return $output;
		}
		
		$this->thread = $getThread;
		//if post ID set... route to specific post/reply
		if(isset($this->args[3])){
			return $this->container->getPost();
		}
		
		//general thread actions
		$newOutput = false;
		switch($this->useMethod){
			case 'POST':
				$newOutput = $this->container->postReply();
				break;
			case 'PATCH':
				$newOutput = $this->container->editThread();
				break;
			case 'DELETE':
				$newOutput = $this->container->buryThread();
				break;
		}
		if($newOutput !== false){
			return $newOutput;
		}
		
		$this->args['data']['user'] = $this->user;
		$output = $this->model->getThreadData($getThread, $this->args['data']);
		if(isset($output['replies'])){
			$output['replies'] = array_values($output['replies']);
		}
		
		return $output;	
	}
	
	/**
	* Creates a new forum thread in the desired board. Must be logged in and have permission.
	* For data params, accepts boardId, title and content. Can also pass parse-markdown=true to return content in HTML format
	* 
	* @return Array
	*
	*/
	protected function postThread()
	{
		$output = array();
		if(!$this->user){
			http_response_code('401');
			$output['error'] = 'Not Authorized';
			return $output;
		}
		
		//temp disable this method...
		$output['error'] = 'Method temporarily disabled';
		http_response_code(500);
		return $output;
		
		if(!$this->perms['canPostTopic']){
			http_response_cide('403');
			$output['error'] = 'You do not have permission for this';
			return $output;
		}
		$user = $this->user;
		$user['perms'] = $this->perms;
		$this->args['data']['user'] = $user;
		try{
			$post = $this->model->postThread($this->args['data']);
		}
		catch(\Exception $e){
			http_response_code(400);
			$output['error'] = $e->getMessage();
			return $output;
		}
		$output['thread'] = $post;
		return $output;	
	}
	
	/**
	* Edits an existing thread
	*
	* @return Array
	*
	*/	
	protected function editThread()
	{
		$output = array();
		if(!$this->user){
			http_response_code('401');
			$output['error'] = 'Not Authorized';
			return $output;
		}
		if(($this->thread['userId'] == $this->user['userId'] AND !$this->perms['canEditSelf']) OR
			($this->thread['userId'] != $this->user['userId'] AND !$this->perms['canEditOther'])){
			http_response_code(403);
			$output['error'] = 'You do not have permission to edit this';
			return $output;
		}
		$user = $this->user;
		$user['perms'] = $this->perms;
		$this->args['data']['user'] = $user;
		$this->args['data']['thread'] = $this->thread;
		try{
			$edit = $this->model->editThread($this->args['data']);
		}
		catch(\Exception $e){
			http_response_code(400);
			$output['error'] = $e->getMessage();
			return $output;
		}
		$output['thread'] = $edit;
		return $output;	
	}
	
	/**
	* Buries/archives a thread
	*
	* @return Array
	*
	*/
	protected function buryThread()
	{
		$output = array();
		if(!$this->user){
			http_response_code('401');
			$output['error'] = 'Not Authorized';
			return $output;
		}
		if(($this->thread['userId'] == $this->user['userId'] AND !$this->perms['canDeleteSelfTopic']) OR
			($this->thread['userId'] != $this->user['userId'] AND !$this->perms['canDeleteOtherTopic'])){
			http_response_code(403);
			$output['error'] = 'You do not have permission to delete this';
			return $output;
		}
		$delete = $this->model->edit('forum_topics', $this->thread['topicId'], array('buried' => 1, 'buriedBy' => $this->user['userId'], 'buryTime' => timestamp()));
		if(!$delete){
			http_response_code(400);
			$output['error'] = 'Error burying thread';
			return $output;
		}
		$output['result'] = 'success';
		return $output;	
	}
	
	/**
	* Grabs data for specific reply, or routes to specific post actions
	*
	* @return Array
	* 
	*/
	protected function getPost()
	{
		$output = array();
		//get post data
		$getPost = $this->model->get('forum_posts', $this->args[3], array('postId', 'userId', 'topicId', 'content', 'postTime', 'editTime', 'editedBy', 'buried', 'trollPost'));
		if(!$getPost OR $getPost['buried'] == 1 OR $getPost['trollPost'] == 1 OR $getPost['topicId'] != $this->thread['topicId']){
			http_response_code(404);
			$output['error'] = 'Post not found';
			return $output;
		}
		$this->post = $getPost;
		//check for post actions
		$newOutput = false;
		switch($this->useMethod){
			case 'GET':
				//continue on
				break;
			case 'PATCH':
				$newOutput = $this->container->editPost();
				break;
			case 'DELETE':
				$newOutput = $this->container->buryPost();
				break;
			default:
				$newOutput = array('error' => 'Invalid Request Method', 'methods' => array('GET','PATCH','DELETE'));
				http_response_code(400);
				break;
		}
		if($newOutput !== false){
			return $newOutput;
		}
		//unset some extra info
		unset($getPost['buried']);
		unset($getPost['trollPost']);
		$data = $this->args['data'];
		//get author profile
		if(!isset($data['no-profiles']) OR (intval($data['no-profiles']) === 0 AND $data['no-profiles'] != 'true')){
			$profile = new \App\Profile\User_Model;
			$getPost['author'] = $profile->getUserProfile($getPost['userId'], $data['site']['siteId']);
			unset($getPost['author']['email']);
			unset($getPost['author']['lastAuth']);
			unset($getPost['author']['pubProf']);
			unset($getPost['author']['showEmail']);
		}
		//strip HTML
		if(isset($data['strip-html']) AND (intval($data['strip-html']) === 1 OR $data['strip-html'] == 'true')){
			$getPost['content'] = strip_tags($getPost['content']);
			if(isset($getPost['author'])){
				foreach($getPost['author']['profile'] as &$profileItem){
					foreach($profileItem as &$profileValue){
						$profileValue = strip_tags($profileValue);
					}
				}
			}				
		}
		//parse markdown
		if(isset($data['parse-markdown']) AND (intval($data['parse-markdown']) === 1 OR $data['parse-markdown'] == 'true')){
			$getPost['content'] = markdown($getPost['content']);
			if(isset($getPost['author'])){
				foreach($getPost['author']['profile'] as &$profileItem){
					if($profileItem['type'] == 'textarea'){
						$profileItem['value'] = markdown($profileItem['value']);
					}
				}
			}					
		}
		//return output
		$output['post'] = $getPost;
		return $output;	
	}
	
	/**
	* Post reply to a thread
	*
	* @return Array
	* @method POST
	*
	*/
	protected function postReply()
	{
		$output = array();
		if(!$this->user){
			http_response_code('401');
			$output['error'] = 'Not Authorized';
			return $output;
		}
		
		//temp disable this method...
		$output['error'] = 'Method temporarily disabled';
		http_response_code(500);
		return $output;
		
		
		if(!$this->perms['canPostReply']){
			http_response_code('403');
			$output['error'] = 'You do not have permission for this';
			return $output;
		}
		$user = $this->user;
		$user['perms'] = $this->perms;
		$this->args['data']['user'] = $user;
		$this->args['data']['thread'] = $this->thread;
		try{
			$post = $this->model->postReply($this->args['data']);
		}
		catch(\Exception $e){
			http_response_code(400);
			$output['error'] = $e->getMessage();
			return $output;
		}
		$output['post'] = $post;
		return $output;	
	}
	
	/**
	* Edits an individual forum post/reply.
	*
	* @return Array
	* @method PATCH
	*
	*/
	protected function editPost()
	{
		$output = array();
		if(!$this->user){
			http_response_code('401');
			$output['error'] = 'Not Authorized';
			return $output;
		}
		if(($this->post['userId'] == $this->user['userId'] AND !$this->perms['canEditSelf']) OR
			($this->post['userId'] != $this->user['userId'] AND !$this->perms['canEditOther'])){
			http_response_code(403);
			$output['error'] = 'You do not have permission to edit this';
			return $output;
		}
		$user = $this->user;
		$user['perms'] = $this->perms;
		$this->args['data']['user'] = $user;
		$this->args['data']['thread'] = $this->thread;
		$this->args['data']['post'] = $this->post;
		try{
			$edit = $this->model->editReply($this->args['data']);
		}
		catch(\Exception $e){
			http_response_code(400);
			$output['error'] = $e->getMessage();
			return $output;
		}
		$output['post'] = $edit;
		return $output;	
	}
	
	/**
	* Sets individual post to archived/buried
	* 
	* @return Array
	* @method DELETE
	*
	*/
	protected function buryPost()
	{
		$output = array();
		if(!$this->user){
			http_response_code('401');
			$output['error'] = 'Not Authorized';
			return $output;
		}
		if(($this->post['userId'] == $this->user['userId'] AND !$this->perms['canBurySelf']) OR
			($this->post['userId'] != $this->user['userId'] AND !$this->perms['canBuryOther'])){
			http_response_code(403);
			$output['error'] = 'You do not have permission to delete this';
			return $output;
		}
		$delete = $this->model->edit('forum_posts', $this->post['postId'], array('buried' => 1, 'buriedBy' => $this->user['userId'], 'buryTime' => timestamp()));
		if(!$delete){
			http_response_code(400);
			$output['error'] = 'Error burying post';
			return $output;
		}
		$output['result'] = 'success';
		return $output;	
	}
	
	
	
	/**
	* "Opts" control function. Routes to various misc. forum functions such as flagging posts for spam, "likes", locking/unlock threads, etc.
	* 
	* @method GET|POST
	* @return Array
	*
	*/ 
	protected function opts()
	{
		$output = array();
		if(isset($this->args[2])){
			switch($this->args[2]){
				case 'flag':
					$output = $this->container->flagPost();
					break;
				case 'like':
					$output = $this->container->like();
					break;
				case 'unlike':
					$output = $this->container->unlike();
					break;
				case 'move':
					$output = $this->container->moveThread();
					break;
				case 'lock':
					$output = $this->container->lockThread();
					break;
				case 'unlock':
					$output = $this->container->unlockThread();
					break;
				case 'sticky':
					$output = $this->container->stickyThread();
					break;
				case 'unsticky':
					$output = $this->container->unstickyThread();
					break;
				case 'perms':
					$output = $this->container->getPerms();
					break;
				default:
					http_response_code(400);
					$output['error'] = 'Invalid Request';
					break;		
			}
		}
		else{
			http_response_code(400);
			$output['error'] = 'Invalid Request';
		}
		return $output;
	}
	
	/**
	* Flags a specific forum post or thread to the monitors, must be logged in.
	*
	* @method POST
	* @return Array
	*/
	protected function flagPost()
	{
		$output = array();
		$checkMethod = $this->checkMethod('POST');
		if(is_array($checkMethod)){
			return $checkMethod;
		}
		if(!$this->user){
			http_response_code(401);
			$output['error'] = 'Not Authorized';
			return $output;
		}
		
		try{
			$this->args['data']['user'] = $this->user;
			$flag = $this->model->flagPost($this->args['data']);
		}
		catch(\Exception $e){
			http_response_code(400);
			$output['error'] = $e->getMessage();
			return $output;
		}
		
		$output['result'] = $flag;
		
		return $output;
	}
	
	/**
	* "Like" a post or a thread. If GET, returns {"liked": true|false} if item is already liked.
	*
	* @method GET|POST
	* @return Array
	*/
	protected function like()
	{
		$output = array();
		if(!in_array($this->useMethod, array('GET', 'POST'))){
			http_response_code(400);
			$output['error'] = 'Invalid Request Method';
			$output['methods'] = array('GET', 'POST');
			return $output;
		}
		if(!$this->user){
			http_response_code(401);
			$output['error'] = 'Not Authorized';
			return $output;
		}
		$this->args['data']['user'] = $this->user;
		if($this->useMethod == 'GET'){
			try{
				$check = $this->model->checkLikePost($this->args['data']);
			}
			catch(\Exception $e){
				http_response_code(400);
				$output['error'] = $e->getMessage();
				return $output;
			}
			
			$output['liked'] = $check;
		}
		else{
			try{
				$update = $this->model->likePost($this->args['data']);
			}
			catch(\Exception $e){
				http_response_code(400);
				$output['error'] = $e->getMessage();
				return $output;
			}
			
			$output['result'] = $update;
		}

		return $output;	
	}
	
	/**
	* "Unlikes" a post or thread.
	*
	* @method POST
	* @return Array
	*
	*/
	protected function unlike()
	{
		$output = array();
		if(!in_array($this->useMethod, array('POST'))){
			http_response_code(400);
			$output['error'] = 'Invalid Request Method';
			$output['methods'] = array('POST');
			return $output;
		}		
		if(!$this->user){
			http_response_code(401);
			$output['error'] = 'Not logged in';
			return $output;
		}
		
		try{
			$this->args['data']['user'] = $this->user;
			$unlike = $this->model->unlikePost($this->args['data']);
		}
		catch(\Exception $e){
			http_response_code(400);
			$output['error'] = $e->getMessage();
			return $output;
		}
		
		$output['result'] = $unlike;
		return $output;
		
	}
	
	/**
	* Moves a thread into a different forum board
	*
	* @method POST
	* @return Array
	*/
	protected function moveThread()
	{
		$output = array();
		if(!in_array($this->useMethod, array('POST'))){
			http_response_code(400);
			$output['error'] = 'Invalid Request Method';
			$output['methods'] = array('POST');
			return $output;
		}		
		try{
			$this->args['data']['user'] = $this->user;
			$this->args['data']['perms'] = $this->perms;
			$move = $this->model->moveThread($this->args['data']);
		}
		catch(\Exception $e){
			$output['error'] = $e->getMessage();
			return $output;
		}
		
		$output['result'] = $move;
		return $output;
		
	}
	
	/**
	* Locks a forum thread
	*
	* @method POST
	* @return Array
	*/
	protected function lockThread()
	{
		$output = array();
		if(!in_array($this->useMethod, array('POST'))){
			http_response_code(400);
			$output['error'] = 'Invalid Request Method';
			$output['methods'] = array('POST');
			return $output;
		}		
		try{
			$this->args['data']['user'] = $this->user;
			$this->args['data']['perms'] = $this->perms;
			$lock = $this->model->lockThread($this->args['data']);
		}
		catch(\Exception $e){
			$output['error'] = $e->getMessage();
			return $output;
		}
		$output['result'] = $lock;
		return $output;
	}
	
	/**
	* Removes lock state from forum thread. Functions exact same as "lock" method
	*
	* @method POST
	* @return Array
	*/
	protected function unlockThread()
	{
		$output = array();
		if(!in_array($this->useMethod, array('POST'))){
			http_response_code(400);
			$output['error'] = 'Invalid Request Method';
			$output['methods'] = array('POST');
			return $output;
		}		
		try{
			$this->args['data']['user'] = $this->user;
			$this->args['data']['perms'] = $this->perms;
			$lock = $this->model->lockThread($this->args['data'], 0);
		}
		catch(\Exception $e){
			$output['error'] = $e->getMessage();
			return $output;
		}
		$output['result'] = $lock;
		return $output;
	}
	
	/**
	* Sets a forum thread to sticky status
	*
	* @method POST
	* @return Array
	*/
	protected function stickyThread()
	{
		$output = array();
		if(!in_array($this->useMethod, array('POST'))){
			http_response_code(400);
			$output['error'] = 'Invalid Request Method';
			$output['methods'] = array('POST');
			return $output;
		}		
		try{
			$this->args['data']['user'] = $this->user;
			$this->args['data']['perms'] = $this->perms;
			$sticky = $this->model->stickyThread($this->args['data']);
		}
		catch(\Exception $e){
			$output['error'] = $e->getMessage();
			return $output;
		}
		$output['result'] = $sticky;
		return $output;
	}
	
	/**
	* Removes sticky state from forum thread. Functions exact same as "sticky" method
	*
	* @method POST
	* @return Array
	*/
	protected function unstickyThread()
	{
		$output = array();
		if(!in_array($this->useMethod, array('POST'))){
			http_response_code(400);
			$output['error'] = 'Invalid Request Method';
			$output['methods'] = array('POST');
			return $output;
		}		
		try{
			$this->args['data']['user'] = $this->user;
			$this->args['data']['perms'] = $this->perms;
			$sticky = $this->model->stickyThread($this->args['data'], 0);
		}
		catch(\Exception $e){
			$output['error'] = $e->getMessage();
			return $output;
		}
		$output['result'] = $sticky;
		return $output;
	}
	
	/**
	* Obtains list of user permissions for various actions in the forum app. format array(<permision-key>: true|false)
	*
	* @param 'type' string - (optional) possible options: topic,thread,board,category. Include this along with an ID to check for permissions on a specific area in the forums, which may differ (e.g moderator permissions on specific boards)
	* @param 'id' int - (optional) topicId, boardId or categoryId (depending on 'type' field)
	* @return Array
	*/
	protected function getPerms()
	{
		$output = array();
		if(!in_array($this->useMethod, array('GET'))){
			http_response_code(400);
			$output['error'] = 'Invalid Request Method';
			$output['methods'] = array('GET');
			return $output;
		}		
		$perms = $this->perms;
		$validTypes = array('category', 'board', 'topic', 'thread');
		if($this->user AND isset($this->args['data']['type']) AND isset($this->args['data']['id']) AND in_array($this->args['data']['type'], $validTypes)){
			switch($this->args['data']['type']){
				case 'topic':
				case 'thread':
					$getItem = $this->model->get('forum_topics', $this->args['data']['id']);
					if($getItem){
						$perms = $this->tca->checkPerms($this->user['userId'], $perms, $this->postModule['moduleId'], $getItem['topicId'], 'topic');
					}
					break;
				case 'board':
					$getItem = $this->model->get('forum_boards', $this->args['data']['id']);
					if($getItem){
						$perms = $this->tca->checkPerms($this->user['userId'], $perms, $this->boardModule['moduleId'], $getItem['boardId'], 'board');
						$forumControl = new \App\Forum\Post_Controller;
						$boardAppData = array('perms' => $perms, 'user' => $this->user, 'app' => $this->forumApp);
						$perms = $forumControl->checkModPerms($getItem['boardId'], $boardAppData);		
					}		
					break;
				case 'category':
					$getItem = $this->model->get('forum_categories', $this->args['data']['id']);
					if($getItem){
						$perms = $this->tca->checkPerms($this->user['userId'], $perms, $this->boardModule['moduleId'], $getItem['categoryId'], 'category');
					}							
					break;
			}
		}
		if(isset($perms['isTroll'])){
			unset($perms['isTroll']);
		}
		$output['perms'] = $perms;
		return $output;	
	}
	
	
	/**
	* Quick way to check if correct request method is being used.
	*
	* @param $method string (HTTP request method, e.g GET, POST, PATCH) 
	* @return true|Array if wrong method is being used, function will return an array with error message that can be directly returned. 
	*
	*/
	protected function checkMethod($method)
	{
		$output = true;
		if($this->useMethod != $method){
			http_response_code(400);
			$output = array();
			$output['error'] = 'Invalid Request Method';
			$output['methods'] = array($method);
			return $output;
		}
		return $output;
	}
}
