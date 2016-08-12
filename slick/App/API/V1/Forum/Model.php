<?php
namespace App\API\V1;
/**
* Forum API Model
* 
* Advanced data retrieval & processing for the API. General forum functions
* @package [App][API][V1][Forum]
*
*/
class Forum_Model extends \App\Forum\Board_Model
{
	/**
	* Gets a list of recent threads based on data parameters
	*
	* @param $data Array - should be data from $this->args['data'] in API controller
		* @param $data['start'] integer - starting row for pagination
		* @param $data['limit'] integer - number of rows to return
		* @param $data['posted-before'] integer|timestamp - accepts UNIX timestamp or MySQL style timestamp to get threads posted before a certain date. 
		* 													Also can use HTTP header: If-Posted-Before
		* @param $data['modified-since'] integer|timestamp - accepts UNIX timestamp or MySQL style timestam to get threads edited or replied in since a certain date.
		*													   Also can use HTTP header: If-Modified-Since
		* @param $data['categories'] string - comma seperated list of specific category IDs to grab threads from. API converts this into a list of individual board IDs
		* @param $data['exclude-categories'] string - comma seperated list of specific category IDs to exclude from list of threads. API converts this into a list of individual board IDs
		* @param $data['boards'] string - comma seperated list of board IDs to specifically grab threads from
		* @param $data['exclude-boards'] string - comma seperated listof board IDs to exclude threads from list
		* @param $data['min-views'] integer - minimum number of thread views to be included in list (e.g only threads with at least 500 views)
		* @param $data['max-views'] integer - maximum number of thread views to include thread in list (e.g only threads with less than 500 views)
		* @param $data['stickies'] true|false - used to specify grabbing ONLY sticky threads (true) or excluding all sticky threads
		* @param $data['locked'] true|false - used to specify grabbing ONLY locked threads or excluding all locked threads from list.
		* @param $data['users']  string - comma seperated list of user IDs or slugs to grab threads only from specific users
		* @param $data['exclude-users'] string - comma seperated list of user IDs or slugs to exclude any threads by certain users
		* @param $data['strip-html'] true|false  - set to true to strip out any possible HTML in output data
		* @param $data['parse-markdown'] true|false - set to true to parse markdown content from threads into HTML
		* @param $data['no-content'] true|false - set to true to exclude post content from listings (faster)
		* @param $data['sort'] string - choose sorting mode... options are: recent, oldest, time-desc, time-asc, alph-asc, alph-desc
		* @param @data['no-profiles'] true|false - set to true to exclude user profiles for thread OP and most recent reply
	* @return Array
	*
	*/
	protected function getThreadList($data)
	{
		//limits
		$start = 0;
		$max = 25;
		if(isset($data['start'])){
			$start = intval($data['start']);
		}
		if(isset($data['limit'])){
			$max = intval($data['limit']);
		}
		$limit = 'LIMIT '.$start.', '.$max;
		//posted before / modified since options
		$andWhen = $this->container->checkBeforeInput($data);
		//board filters
		$andFilters = $this->container->checkBoardFilters($data);
		//# of views filters
		if(isset($data['min-views'])){
			$data['min-views'] = intval($data['min-views']);
			$andFilters .= ' AND t.views >= '.$data['min-views'];
		}
		if(isset($data['max-views'])){
			$data['max-views'] = intval($data['max-views']);
			$andFilters .= ' AND t.views <= '.$data['max-views'];
		}
		//toggle sticky/non-sticky posts
		if(isset($data['stickies'])){
			if(intval($data['stickies']) === 1 OR $data['stickies'] == 'true'){
				$andFilters .= ' AND t.sticky = 1';
			}
			elseif(intval($data['stickies']) === 0 OR $data['stickies'] == 'false'){
				$andFilters .= ' AND t.sticky = 0';
			}
		}
		//toggle locked/unlocked
		if(isset($data['locked'])){
			if(intval($data['locked']) === 1 OR $data['locked'] == 'true'){
				$andFilters .= ' AND t.locked = 1';
			}
			elseif(intval($data['locked']) === 0 OR $data['locked'] == 'false'){
				$andFilters .= ' AND t.locked = 0';
			}
		}
		//user filters
		$andFilters .= $this->container->checkUserFilters($data);
		//check for content stripping
		$andContent = ',t.content';
		if(isset($data['no-content']) AND (intval($data['no-content']) === 1 OR $data['no-content'] == 'true')){
			$andContent = '';
		}
		//sorting options
		$sort = 't.lastPost DESC, t.postTime DESC';
		if(isset($data['sort'])){
			switch($data['sort']){
				case 'time-desc':
					$sort = 't.postTime DESC';
					break;
				case 'time-asc':
					$sort = 't.postTime ASC';
					break;
				case 'recent':
					$sort = 't.lastPost DESC, t.postTime DESC';
					break;
				case 'oldest':
					$sort = 't.lastPost ASC, t.postTime ASC';
					break;
				case 'alph-asc':
					$sort = 't.title ASC';
					break;
				case 'alph-desc':
					$sort = 't.title DESC';
					break;
				case 'sticky':
					$sort = 't.sticky DESC, t.lastPost DESC';
					break;
			}
		}
		$sql = 'SELECT t.topicId, t.userId, t.title, t.url'.$andContent.', t.boardId, b.name as boardName,
					b.slug as boardSlug, b.categoryId, c.name as categoryName, c.slug as categorySlug,
					 t.locked, t.postTime, t.editTime, t.lastPost, t.sticky, t.views, t.lockTime, 
					 t.lockedBy, t.editedBy, cnt.total as count		
				FROM forum_topics t
				LEFT JOIN forum_boards b ON b.boardId = t.boardId
				LEFT JOIN forum_categories c ON c.categoryId = b.categoryId
				LEFT JOIN (SELECT count(*) as total, topicId FROM forum_posts WHERE trollPost = 0 AND buried = 0 GROUP BY topicId) cnt ON cnt.topicId = t.topicId
				WHERE t.trollPost = 0 AND t.buried = 0 AND b.active = 1
				'.$andWhen.'
				'.$andFilters.'
				ORDER BY '.$sort.'
				'.$limit;
		$getThreads = $this->fetchAll($sql);

		if(count($getThreads) < $max){
			$output['next'] = null;
		}
		else{
			$output['next'] = $start + $max;
		}
		$profile = new \App\Profile\User_Model;
		//check for no-profiles field
		$noProfiles = false;
		if(isset($data['no-profiles']) AND (intval($data['no-profiles']) === 1 OR $data['no-profiles'] == 'true')){
			$noProfiles = true;
		}
		foreach($getThreads as $key => &$thread){
			//do TCA checking
			$checkTCA = $this->container->checkTopicTCA($data['user'], $thread);
			if(!$checkTCA){
				unset($getThreads[$key]);
				continue;
			}
			//reply count
			$thread['replies'] = $thread['count'];
			//get profile and recent post info
			if($andContent != ''){
				if(!$noProfiles){
					$thread['author'] = $profile->getUserProfile($thread['userId'], $data['site']['siteId'], array('groups' => false));
					unset($thread['author']['pubProf']);
					unset($thread['author']['showEmail']);
					unset($thread['author']['email']);
					unset($thread['author']['lastAuth']);
					if(isset($thread['author']['avatar'])){
						if(strpos($thread['author']['avatar'], '://') === false){
							$thread['author']['avatar'] = $data['site']['url'].'/files/avatars/'.$thread['author']['avatar'];
						}
					}
				}
				$thread['mostRecent'] = false;
				$getRecent = false;
				if(isset($thread['recent_postId']) AND intval($thread['recent_postId']) > 0){
					$getRecent = array('postId' => $thread['recent_postId'],
												  'content' => $thread['recent_content'],
												  'userId' => $thread['recent_userId'],
												  'postTime' => $thread['recent_postTime'],
												  'editTime' => $thread['recent_editTime']);
				}
				else{
					$getRecent = $this->fetchSingle('SELECT *
										FROM forum_posts
										WHERE topicId = :id AND buried = 0
										ORDER BY postId DESC
										LIMIT 1', array(':id' => $thread['topicId']));
				}

				if($getRecent){
					$thread['mostRecent'] = $getRecent;
					if(!$noProfiles){
						$thread['mostRecent']['author'] = $profile->getUserProfile($getRecent['userId'], $data['site']['siteId'], array('groups' => false));
						unset($thread['mostRecent']['author']['pubProf']);
						unset($thread['mostRecent']['author']['showEmail']);				
						unset($thread['mostRecent']['author']['email']);			
						unset($thread['mostRecent']['author']['lastAuth']);			
						if(isset($thread['mostRecent']['author']['avatar'])){
							if(strpos($thread['mostRecent']['author']['avatar'], '://') === false){
								$thread['mostRecent']['author']['avatar'] = $data['site']['url'].'/files/avatars/'.$thread['mostRecent']['author']['avatar'];
							}
						}						
					}		
				}
			}			
			//HTML stripping
			if(isset($data['strip-html']) AND (intval($data['strip-html']) === 1 OR $data['strip-html'] == 'true')){
				$thread['boardName'] = strip_tags($thread['boardName']);
				$thread['categoryName'] = strip_tags($thread['categoryName']);
				$thread['title'] = strip_tags($thread['title']);
				if(isset($thread['content'])){
					$thread['content'] = strip_tags($thread['content']);
				}
				if(isset($thread['author']) AND is_array($thread['author'])){
					foreach($thread['author']['profile'] as &$profileItem){
						foreach($profileItem as &$profileValue){
							$profileValue = strip_tags($profileValue);
						}
					}
				}
				if(isset($thread['mostRecent']) AND $thread['mostRecent'] != null){
					$thread['mostRecent']['content'] = strip_tags($thread['mostRecent']['content']);
					if(is_array($thread['mostRecent']['author']['profile'])){
						foreach($thread['mostRecent']['author']['profile'] as &$profileItem){
							foreach($profileItem as &$profileValue){
								$profileValue = strip_tags($profileValue);
							}
						}
					}
				}
			}
			//markdown parsing
			if(isset($data['parse-markdown']) AND isset($thread['content']) AND (intval($data['parse-markdown']) === 1 OR $data['parse-markdown'] == 'true')){
				$thread['content'] = markdown($thread['content']);
				if(isset($thread['author']) AND is_array($thread['author']['profile'])){
					foreach($thread['author']['profile'] as &$profileItem){
						if($profileItem['type'] == 'textarea'){
							$profileItem['value'] = markdown($profileItem['value']);
						}
					}
				}
				if(isset($thread['mostRecent']) AND $thread['mostRecent'] != null){
					$thread['mostRecent']['content'] = markdown($thread['mostRecent']['content']);
					if(isset($thread['mostRecent']['author']) AND is_array($thread['mostRecent']['author']['profile'])){
						foreach($thread['mostRecent']['author']['profile'] as &$profileItem){
							if($profileItem['type'] == 'textarea'){
								$profileItem['value'] = markdown($profileItem['value']);
							}
						}
					}
				}
			}
		}
		$output['threads'] = $getThreads;
		return $output;
	}
	
	/**
	*
	* Checks for "If Modified Since" and "Posted Before" parameters for query building in getThreadList()
	*
	* @param $data Array input data from API controller or getThreadList()
	* @return string
	* 
	*/
	protected function checkBeforeInput($data)
	{
		$andWhen = '';
		$modifiedSince = false;
		$modTime = false;
		if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
			if(is_int($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
				$modTime = intval($_SERVER['HTTP_IF_MODIFIED_SINCE']);
			}
			else{
				$modTime = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
			}
		}
		elseif(isset($data['modified-since']) AND trim($data['modified-since']) != ''){
			if(is_int($data['modified-since'])){
				$modTime = intval($data['modified-since']);
			}
			else{
				$modTime = strtotime($data['modified-since']);
			}
		}
		if($modTime !== false){
			$modifiedSince = date('Y-m-d H:i:s', $modTime);
		}
		if($modifiedSince !== false){
			$andWhen .= ' AND (t.editTime >= "'.$modifiedSince.'" OR t.lastPost >= "'.$modifiedSince.'") ';
		}
		$postedBefore = false;
		$beforeTime = false;
		if(isset($_SERVER['HTTP_IF_POSTED_BEFORE'])){
			if(is_numeric($_SERVER['HTTP_IF_POSTED_BEFORE'])){
				$beforeTime = intval($_SERVER['HTTP_IF_POSTED_BEFORE']);
			}
			else{
				$beforeTime = strtotime($_SERVER['HTTP_IF_POSTED_BEFORE']);
			}
		}
		elseif(isset($data['posted-before']) AND trim($data['posted-before']) != ''){
			if(is_numeric($data['posted-before'])){
				$beforeTime = intval($data['posted-before']);
			}
			else{
				$beforeTime = strtotime($data['posted-before']);
			}
		}
		if($beforeTime !== false){
			$postedBefore = date('Y-m-d H:i:s', $beforeTime);
		}
		if($postedBefore !== false){
			$andWhen .= ' AND t.postTime <= "'.$postedBefore.'" ';
		}		
		return $andWhen;
	}
	
	/**
	* Checks for and processes data for categories, boards, exclude-categories and exclude-boards parameters
	* 
	* @param $data Array - data from API controller or getThreadList()
	* @return string
	*
	*/
	protected function checkBoardFilters($data)
	{
		$andFilters = '';
		if(isset($data['categories'])){
			if(isset($data['boards'])){
				$catBoardList = explode(',', $data['boards']);
			}
			else{
				$catBoardList = array();
			}
			$expCats = explode(',', $data['categories']);
			foreach($expCats as $ck => &$cat){
				$cat = intval($cat);
				$getCat = $this->get('forum_categories', $cat, array('categoryId'));
				if(!$getCat){
					unset($expCats[$ck]);
					continue;
				}
				$getBoards = $this->getAll('forum_boards', array('categoryId' => $cat, 'active' => 1), array('boardId'));
				foreach($getBoards as $board){
					if(!in_array($board['boardId'], $catBoardList)){
						$catBoardList[] = $board['boardId'];
					}
				}
			}
			$data['boards'] = join(',', $catBoardList);
		}
		if(isset($data['exclude-categories'])){
			if(isset($data['exclude-boards'])){
				$catAntiBoardList = explode(',', $data['exclude-boards']);
			}
			else{
				$catAntiBoardList = array();
			}
			$expAntiCats = explode(',', $data['exclude-categories']);
			foreach($expAntiCats as $ck => &$cat){
				$cat = intval($cat);
				$getCat = $this->get('forum_categories', $cat, array('categoryId'));
				if(!$getCat OR (isset($catBoardList) AND in_array($cat, $catBoardList))){
					unset($expAntiCats[$ck]);
					continue;
				}
				$getAntiBoards = $this->getAll('forum_boards', array('categoryId' => $cat, 'active' => 1), array('boardId'));
				foreach($getAntiBoards as $board){
					if(!in_array($board['boardId'], $catAntiBoardList)){
						$catAntiBoardList[] = $board['boardId'];
					}
				}
			}
			$data['exclude-boards'] = join(',', $catAntiBoardList);
		}
		if(isset($data['boards'])){
			$boardList = explode(',', $data['boards']);
			foreach($boardList as $bk => &$b){
				$b = intval($b);
				$getBoard = $this->get('forum_boards', $b, array('boardId', 'active'));
				if(!$getBoard OR $getBoard['active'] == 0){
					unset($boardList[$bk]);
					continue;
				}
			}
			if(count($boardList) > 0){
				$andFilters .= ' AND t.boardId IN('.join(',', $boardList).') ';
			}
		}
		if(isset($data['exclude-boards'])){
			$antiboardList = explode(',', $data['exclude-boards']);
			foreach($antiboardList as $bk => &$b){
				$b = intval($b);
				$getBoard = $this->get('forum_boards', $b, array('boardId', 'active'));
				if(!$getBoard OR $getBoard['active'] == 0 OR (isset($boardList) AND in_array($b, $boardList))){
					unset($antiboardList[$bk]);
					continue;
				}
				$andFilters .= ' AND t.boardId != '.$b.' ';
			}
		}
		if(!isset($data['user'])){
			$data['user'] = false;
		}
		$userFilters = $this->container->getBoardFilters($data['user']);
		if(count($userFilters['antifilters']) > 0){
			foreach($userFilters['antifilters'] as &$filter){
				$filter = intval($filter);
				$andFilters .= ' AND';
				$andFilters .= ' t.boardId != '.$filter.' ';
			}
		}
		return $andFilters;
	}
	
	/**
	* Checks for and processes data for users and exclude-users parameters
	* 
	* @param $data Array - data from API controller or getThreadList()
	* @return string
	*
	*/
	protected function checkUserFilters($data)
	{
		$andFilters = '';
		if(isset($data['users']) AND is_string($data['users'])){
			$userList = explode(',', $data['users']);
			foreach($userList as $bk => &$b){
				$getUser = $this->get('users', $b, array('userId'));
				if(!$getUser){
					$getUser = $this->get('users', $b, array('userId'), 'slug');
					if(!$getUser){
						unset($userList[$bk]);
						continue;
					}
				}
				$b = $getUser['userId'];
			}
			if(count($userList) > 0){
				$andFilters .= ' AND t.userId IN('.join(',', $userList).') ';
			}
		}
		if(isset($data['exclude-users'])){
			$antiuserList = explode(',', $data['exclude-users']);
			foreach($antiuserList as $bk => &$b){
				$getUser = $this->get('users', $b, array('userId'));
				if(!$getUser){
					$getUser = $this->get('users', $b, array('userId'), 'slug');
				}
				if(!$getUser OR (isset($userList) AND in_array($b, $userList))){
					unset($antiuserList[$bk]);
					continue;
				}
				$andFilters .= ' AND t.userId != '.$getUser['userId'].' ';
			}
		}
		return $andFilters;
	}
	
	/**
	* Gets thread data + a list of replies for a specific thread
	*
	*
	* @param $thread Array - forum_topics entry
	* @param $data Array - data from API controller
		* @param $data['start'] integer - starting row for pagination
		* @param $data['limit'] integer - number of rows to retrieve
		* @param $data['strip-html'] true|false - strips all possible HTML out of titles and content
		* @param $data['parse-markdown'] true|false - parses any markdown in content etc.
		* @param $data['no-profiles'] true|false - excludes user profiles from return data
		* @param $data['sort'] string - can be set to either "asc" to sort by date in ascending order, or "desc" to sort in descending order (newest to oldest)
		* @param $data['thread-only'] true|false - set to true to return only thread data, no replies
		* @param $data['replies-only'] true|false - set to true to return only list of replies
	*
	*/
	protected function getThreadData($thread, $data)
	{
		$output = array();
		//unset some uneccessary data
		unset($thread['trollPost']);
		unset($thread['buried']);
		unset($thread['buriedBy']);
		unset($thread['buryTime']);
		//get category and board data
		$getBoard = $this->get('forum_boards', $thread['boardId']);
		$thread['boardName'] = $getBoard['name'];
		$thread['boardSlug'] = $getBoard['slug'];
		$getCat = $this->get('forum_categories', $getBoard['categoryId']);
		$thread['categoryId'] = $getBoard['categoryId'];
		$thread['categoryName'] = $getCat['name'];
		$thread['categorySlug'] = $getCat['slug'];
		//reply count
		$countReplies = $this->fetchSingle('SELECT count(*) as total FROM forum_posts WHERE topicId = :topicId AND buried = 0 AND trollPost = 0',
										   array(':topicId' => $thread['topicId']));
		$thread['replies'] = $countReplies['total'];				
		//get OP profile
		if(!isset($data['no-profiles']) OR ($data['no-profiles'] != 'true' AND intval($data['no-profiles']) !== 1)){
			$profile = new \App\Profile\User_Model;
			$thread['author'] = $profile->getUserProfile($thread['userId'], $data['site']['siteId'], array('groups' => false));
			unset($thread['author']['pubProb']);
			unset($thread['author']['showEmail']);
			unset($thread['author']['email']);
			unset($thread['author']['lastAuth']);
		}
		//HTML stripping
		if(isset($data['strip-html']) AND (intval($data['strip-html']) === 1 OR $data['strip-html'] == 'true')){
			$thread['content'] = strip_tags($thread['content']);
			$thread['title'] = strip_tags($thread['title']);
			$thread['boardName'] = strip_tags($thread['boardName']);
			$thread['categoryName'] = strip_tags($thread['categoryName']);
			if(isset($thread['author'])){
				foreach($thread['author']['profile'] as &$profileItem){
					foreach($profileItem as &$profileValue){
						$profileValue = strip_tags($profileValue);
					}
				}
			}
		}
		//markdown parsing
		if(isset($data['parse-markdown']) AND (intval($data['parse-markdown']) === 1 OR $data['parse-markdown'] == 'true')){
			$thread['content'] = markdown($thread['content']);
			if(isset($thread['author'])){
				foreach($thread['author']['profile'] as &$profileItem){
					if($profileItem['type'] == 'textarea'){
						$profileItem['value'] = markdown($profileItem['value']);
					}
				}
			}
		}
		//setup output
		if(!isset($data['replies-only']) OR (isset($data['replies-only']) AND ($data['replies-only'] != 'true' AND $data['replies-only'] != '1'))){
			$output['thread'] = $thread;
		}
		if(!isset($data['thread-only']) OR (isset($data['thread-only']) AND ($data['thread-only'] != 'true' AND $data['thread-only'] != '1'))){
			$output['replies'] = $this->container->getThreadReplies($thread, $data);
		}
		return $output;
	}
	
	/**
	* Grabs list of replies for getThreadData() function
	*
	* @return Array
	*
	*/
	protected function getThreadReplies($thread, $data)
	{
		$output = array();
		//limits
		$start = 0;
		$max = 20;
		if(isset($data['start'])){
			$start = intval($data['start']);
		}
		if(isset($data['limit'])){
			$max = intval($data['limit']);
		}
		$limit = 'LIMIT '.$start.', '.$max;
		$sort = 'postTime ASC';
		if(isset($data['sort'])){
			switch($data['sort']){
				case 'asc':
					$sort = 'postTime ASC';
					break;
				case 'desc':
					$sort = 'postTime DESC';
					break;
			}
		}
		$sql = 'SELECT postId, userId, content, postTime, editTime, editedBy
				FROM forum_posts
				WHERE topicId = :topicId
				AND buried = 0 AND trollPost = 0
				ORDER BY '.$sort.'
				'.$limit;
		$getPosts = $this->fetchAll($sql, array(':topicId' => $thread['topicId']));
		$profile = new \App\Profile\User_Model;
		foreach($getPosts as &$post){
			//get profile data
			if(!isset($data['no-profiles']) OR ($data['no-profiles'] != 'true' AND intval($data['no-profiles']) !== 1)){
				$post['author'] = $profile->getUserProfile($post['userId'], $data['site']['siteId'], array('groups' => false));
				unset($post['author']['pubProf']);
				unset($post['author']['showEmail']);
				unset($post['author']['email']);
				unset($post['author']['lastAuth']);
			}
			//HTML stripping
			if(isset($data['strip-html']) AND (intval($data['strip-html']) === 1 OR $data['strip-html'] == 'true')){
				$post['content'] = strip_tags($post['content']);
				if(isset($post['author'])){
					foreach($post['author']['profile'] as &$profileItem){
						foreach($profileItem as &$profileValue){
							$profileValue = strip_tags($profileValue);
						}
					}
				}				
			}
			//markdown parsing
			if(isset($data['parse-markdown']) AND (intval($data['parse-markdown']) === 1 OR $data['parse-markdown'] == 'true')){
				$post['content'] = markdown($post['content']);
				if(isset($post['author'])){
					foreach($post['author']['profile'] as &$profileItem){
						if($profileItem['type'] == 'textarea'){
							$profileItem['value'] = markdown($profileItem['value']);
						}
					}
				}				
			}
		}
		$output = $getPosts;
		return $output;
	}
	
	/**
	* Sets up and passes data on to main postTopic function in parent model.
	*
	* @param $data Array data from API controller
	* @param $data['boardId'] integer - board ID to place topic in
	* @param $data['title'] string - topic title
	* @param $data['content'] string - thread content
	* @param $data['parse-markdown'] true|false - set to true to return post data in parsed markdown
	* @return Array newly created thread data
	*
	*/
	protected function postThread($data)
	{
		$appData = array();
		$appData['user'] = $data['user'];
		$appData['site'] = $data['site'];
		$appData['app'] = get_app('forum');
		$appData['module'] = get_app('forum.forum-board');
		$appData['perms'] = $data['user']['perms'];
		$useData = $data;
		$useData['userId'] = $data['user']['userId'];
		$post = $this->container->postTopic($useData, $appData);
		if(isset($data['parse-markdown']) AND ($data['parse-markdown'] == 'true' OR intval($data['parse-markdown']) === 1)){
			$post['content'] = markdown($post['content']);
		}
		return $post;
	}
	
	/**
	* Passes data into main editTopic function from Forum Post Model
	*
	* @param $data Array data from API controller
	* @param $data['title'] string - topic title
	* @param $data['content'] string - thread content
	* @param $data['parse-markdown'] true|false - set to true to return post data in parsed markdown
	* @return Array edited thread data
	*
	*/
	protected function editThread($data)
	{
		$postModel = new \App\Forum\Post_Model;
		$appData = array();
		$appData['user'] = $data['user'];
		$appData['site'] = $data['site'];
		$appData['app'] = $this->get('apps', 'forum', array(), 'slug');
		$appData['module'] = $this->get('modules', 'forum-post', array(), 'slug');
		$appData['perms'] = $data['user']['perms'];
		$post = $postModel->editTopic($data['thread']['topicId'], $data, $appData);
		if(isset($data['parse-markdown']) AND ($data['parse-markdown'] == 'true' OR intval($data['parse-markdown']) === 1)){
			$post['content'] = markdown($post['content']);
		}
		unset($post['trollPost']);
		unset($post['buried']);
		unset($post['buriedBy']);
		unset($post['buryTime']);
		return $post;
	}
	
	/**
	* Posts a reply to a thread, passing data into main postReply method
	*
	* @param $data Array - data from API controller
	* @param $data['content'] string - reply content
	* @param $data['parse-markdown'] true|false - set to true to return content as parsed HTML
	* @return Array - new post data
	* 
	*/
	protected function postReply($data)
	{
		$postModel = new \App\Forum\Post_Model;
		$meta = new \App\Meta_Model;
		if(!isset($data['content'])){
			throw new \Exception('content required');
		}
		$useData = array();
		$useData['topicId'] = $data['thread']['topicId'];
		$useData['userId'] = $data['user']['userId'];
		$useData['content'] = $data['content'];
		$appData = array();
		$appData['user'] = $data['user'];
		$appData['site'] = $data['site'];
		$appData['app'] = $this->get('apps', 'forum', array(), 'slug');
		$appData['app']['meta'] = $meta->appMeta($appData['app']['appId']);
		$appData['module'] = $this->get('modules', 'forum-post', array(), 'slug');
		$appData['perms'] = $data['user']['perms'];
		$appData['topic'] = $data['thread'];
		$post = $postModel->postReply($useData, $appData);
		if(isset($data['parse-markdown']) AND ($data['parse-markdown'] == 'true' OR intval($data['parse-markdown']) === 1)){
			$post['content'] = markdown($post['content']);
		}
		if(isset($post['trollPost'])){
			unset($post['trollPost']);
		}
		return $post;
	}
	
	/**
	* Edits an individual reply
	*
	* @param $data Array - data from API controller
	* @param $data['content'] string - new post content
	* @param $data['parse-markdown'] true|false - set to true to return content as parsed HTML
	* @return Array - new edited post data
	*
	*/
	protected function editReply($data)
	{
		$postModel = new \App\Forum\Post_Model;
		$meta = new \App\Meta_Model;
		if(!isset($data['content'])){
			throw new \Exception('content required');
		}
		$useData = array();
		$useData['content'] = $data['content'];		
		
		$appData = array();
		$appData['user'] = $data['user'];
		$appData['site'] = $data['site'];
		$appData['app'] = $this->get('apps', 'forum', array(), 'slug');
		$appData['app']['meta'] = $meta->appMeta($appData['app']['appId']);
		$appData['module'] = $this->get('modules', 'forum-post', array(), 'slug');
		$appData['perms'] = $data['user']['perms'];
		$appData['topic'] = $data['thread'];
		$post = $postModel->editPost($data['post']['postId'], $useData, $appData);
		if(isset($data['parse-markdown']) AND ($data['parse-markdown'] == 'true' OR intval($data['parse-markdown']) === 1)){
			$post['content'] = markdown($post['content']);
		}
		unset($post['trollPost']);
		unset($post['buried']);
		unset($post['buriedBy']);
		unset($post['buryTime']);
		return $post;
	}
	
	/**
	* Flags a forum thread or post as spam / against rules. Notifications sent to moderators.
	* 
	* @param $data Array - data from API controller
	* @param $data['type'] - type of post to flag.. options: thread, post
	* @param $data['id'] - postId or topicId of item in question
	* @return bool
	*/
	protected function flagPost($data)
	{
		$req = array('type', 'id');
		foreach($req as $required){
			if(!isset($data[$required]) OR trim($data[$required]) == ''){
				throw new \Exception($required.' required');
			}
		}

		$meta = new \App\Meta_Model;
		$postModel = new \App\Forum\Post_Model;
		$reportedPosts = $meta->getUserMeta($data['user']['userId'], 'reportedPosts');
		if(!$reportedPosts){
			$reportedPosts = array();
		}
		else{
			$reportedPosts = json_decode($reportedPosts, true);
		}
		
		$app = $this->get('apps', 'forum', array(), 'slug');
		$app['meta'] = $meta->appMeta($app['appId']);
		$module = $this->get('modules', 'forum-post', array(), 'slug');
		$data['app'] = $app;
		$data['module'] = $module;
		$validTypes = array('post', 'thread', 'topic');
		
		if(!isset($data['id']) OR !isset($data['type']) OR !in_array($data['type'], $validTypes)){
			throw new \Exception('Invalid parameters');
		}
		
		$getItem = false;
		$reportMessage = 'a post';
		switch($data['type']){
			case 'thread':
			case 'topic':
				$getItem = $this->get('forum_topics', $data['id']);
				break;
			case 'post':
				$getItem = $this->get('forum_posts', $data['id']);
				if($getItem){
					$getTopic = $this->get('forum_topics', $getItem['topicId']);
					$getPoster = $this->get('users', $getItem['userId'], array('userId', 'slug', 'username'));
					$postPage = $postModel->getPostPage($getItem['postId'], $app['meta']['postsPerPage']);
					$getItem['topic'] = $getTopic;
					$getItem['poster'] = $getPoster;
					$getItem['postPage'] = $postPage;
					$getItem['boardId'] = $getTopic['boardId'];
				}

				break;
		}
		
		if(!$getItem){
			throw new \Exception('Item not found');
		}
		
		if($getItem['userId'] == $data['user']['userId']){
			throw new \Exception('Cannot flag your own content');
		}
		
		foreach($reportedPosts as $report){
			$hasReported = false;
			switch($report['type']){
				case 'topic':
				case 'thread':
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
				throw new \Exception('Item already reported');
			}
		}
		
		//notify users
		$getPerms = $this->getAll('app_perms', array('appId' => $app['appId']));
		$getPerm = extract_row($getPerms, array('permKey' => 'canReceiveReports'));
		if($getPerm){
			$getPerm = $getPerm[0];
			$notifyList = array();
			
			//check for forum mods
			$getMods = $this->getAll('forum_mods', array('boardId' => $getItem['boardId']));
			if(count($getMods) > 0){
				foreach($getMods as $mod){
					if(!in_array($mod['userId'], $notifyList)){
						$notifyList[] = $mod['userId'];
					}
				}
			}
			else{
				$permGroups = $this->getAll('group_perms', array('permId' => $getPerm['permId']));
				foreach($permGroups as $permGroup){
					$groupUsers = $this->getAll('group_users', array('groupId' => $permGroup['groupId']));
					foreach($groupUsers as $gUser){
						if(!in_array($gUser['userId'], $notifyList)){
							$notifyList[] = $gUser['userId'];
						}
					}
				}
			}
			
			foreach($notifyList as $notifyUser){
				if($notifyUser == $data['user']['userId']){
					continue;
				}
				$notifyData = $data;
				$nofityData['reportMessage'] = $reportMessage;
				$notifyData['item'] = $getItem;
				$notifyData['notifyUser'] = $notifyUser;
				$notify = \App\Meta_Model::notifyUser($notifyUser, 'emails.flagPostNotice', $data['id'], 'report-'.$data['type'], true, $notifyData);
			}
		}
		
		$reportedPosts[] = array('type' => $data['type'], 'itemId' => $data['id']);
		$update = $meta->updateUserMeta($data['user']['userId'], 'reportedPosts', json_encode($reportedPosts));
		if(!$update){
			throw new \Exception('Error reporting item');

		}
		
		return true;
	}
	
	/**
	* "Likes" a post or thread
	*
	* @param $data Array - data from API controller
	* @param $data['type'] - options: thread,post
	* @param $data['id'] - postId or topicId of item
	* @return bool
	*/
	protected function likePost($data)
	{
		if(!isset($data['id'])){
			throw new \Exception('id required');
		}
		
		$validTypes = array('topic', 'thread', 'post');
		if(!isset($data['type']) OR !in_array($data['type'], $validTypes)){
			throw new \Exception('Invalid post type');
		}
		$type = $data['type'];
		$getItem = false;
		$itemId = false;
		$typeCat = '';
		switch($type){
			case 'topic':
			case 'thread':
				$getItem = $this->get('forum_topics', $data['id']);
				if($getItem){
					$itemId = $getItem['topicId'];
				}
				$typeCat = 'topic';
				break;
			case 'post':
				$getItem = $this->get('forum_posts', $data['id']);
				if($getItem){
					$itemId = $getItem['postId'];
				}
				$typeCat = 'post';
				break;
		}
		if(!$itemId){
			throw new \Exception('Item not found');
		}
		

		$getLike = $this->fetchSingle('SELECT *
											  FROM user_likes
											  WHERE userId = :userId AND itemId = :id AND type = :type',
											 array(':userId' => $data['user']['userId'], ':id' => $itemId, ':type' => $typeCat));
		if($getLike){
			throw new \Exception('Already liked');
		}
		

		$meta = new \App\Meta_Model;
		$app = $this->get('apps', 'forum', array(), 'slug');
		$app['meta'] = $meta->appMeta($app['appId']);
		$module = $this->get('modules', 'forum-post', array(), 'slug');
		$data['app'] = $app;
		$data['module'] = $module;
		
		$notifyData = $data;
		$postModel = new \App\Forum\Post_Model;
		switch($type){
			case 'topic':
			case 'thread':
				$emailView = 'emails.likeThreadNotice';
				$notifyData['topic'] = $getItem;
				break;
			case 'post':
				$postPage = $postModel->getPostPage($itemId, $data['app']['meta']['postsPerPage']);
				$andPage = '';
				if($postPage > 1){
					$andPage = '?page='.$postPage;
				}
				
				$getTopic = $this->get('forum_topics', $getItem['topicId']);
				
				if($getItem['userId'] != $data['user']['userId']){
					$notifyData['topic'] = $getTopic;
					$notifyData['page'] = $andPage;
					$notifyData['post'] = $getItem;
					$emailView = 'emails.likePostNotice';
					$typeCat = 'post';
				}				
				break;
		}
		
		$like = $this->insert('user_likes', array('userId' => $data['user']['userId'],
														'itemId' => $itemId, 'type' => $typeCat, 'likeTime' => timestamp()));											
		if(!$like){
			throw new \Exception('Error adding like');
		}
		
		\App\Meta_Model::notifyUser($getItem['userId'], $emailView, $itemId, 
										 'like-'.$typeCat.'-'.$data['user']['userId'], false, $notifyData);
		
		return true;
		
	}
	/**
	* Checks to see if a post or thread is liked or not already
	*
	* @param $data Array - data from API controller
	* @param $data['type'] - options: thread,post
	* @param $data['id'] - postId or topicId of item
	* @param $returnID - set this to true to return the "likeId" if the item has indeed been liked already
	* @return bool
	*/
	protected function checkLikePost($data, $returnID = false)
	{
		if(!isset($data['type'])){
			throw new \Exception('type required');
		}
		if(!isset($data['id'])){
			throw new \Exception('id required');
		}
		$getItem = false;
		$itemId = false;
		$useType = '';
		switch($data['type']){
			case 'topic':
			case 'thread':
				$getItem = $this->get('forum_topics', $data['id']);
				if($getItem){
					$itemId = $getItem['topicId'];
				}
				$useType = 'topic';
				break;
			case 'post':
				$getItem = $this->get('forum_posts', $data['id']);
				if($getItem){
					$itemId = $getItem['postId'];
				}
				$useType = 'post';
				break;
		}
		if(!$getItem){
			throw new \Exception('Item not found');
		}
		$getLike = $this->getAll('user_likes', array('userId' => $data['user']['userId'], 'itemId' => $itemId, 'type' => $useType));
		if(is_array($getLike) AND count($getLike) > 0){
			if($returnID){
				return $getLike[0]['likeId'];
			}
			return true;
		}
		return false;
	}
	
	/**
	* Removes a "like" entry on a post
	*
	* @param $data Array - data from API controller
	* @param $data['type'] - options: thread,post
	* @param $data['id'] - postId or topicId of item
	* @return bool
	*/
	protected function unlikePost($data)
	{
		$getLike = $this->container->checkLikePost($data, true);
		if(!$getLike){
			throw new \Exception('Item not liked');
		}
		$unlike = $this->delete('user_likes', $getLike);
		if(!$unlike){
			throw new \Exception('Error removing like on item');
		}		
		return true;
	}
	
	/**
	* Moves a forum thread into a different board
	*
	* @param $data Array - data from API controller
	* @param $data['from-type'] - type of forum item you want to move.. valid options: [thread]
	* @param $data['from-id'] - ID of item you want to move. e.g the topicId
	* @param $data['to-type'] - type of area that the item is moving to. valid options: [board]
	* @param $data['to-id'] - ID of area that item is moving to, e.g the boardId
	* @return bool
	*/
	protected function moveThread($data)
	{
		if(!$data['user']){
			http_response_code(401);
			throw new \Exception('Not authorized');
		}		
		$req = array('from-type', 'from-id', 'to-type', 'to-id');
		foreach($req as $required){
			if(!isset($data[$required]) OR trim($data[$required]) == ''){
				http_response_code(400);
				throw new \Exception($required.' required');
			}
		}
		$validFrom = array('topic', 'thread');
		$validTo = array('board');
		if(!in_array($data['from-type'], $validFrom)){
			http_response_code(400);
			throw new \Exception('Invalid from-type');
		}
		if(!in_array($data['to-type'], $validTo)){
			http_response_code(400);
			throw new \Exception('Invalid to-type');
		}
		$getItem = false;
		$getItem2 = false;
		switch($data['from-type']){
			case 'topic':
			case 'thread':
				$getItem = $this->get('forum_topics', $data['from-id']);
				break;
		}
		switch($data['to-type']){
			case 'board':
				$getItem2 = $this->get('forum_boards', $data['to-id']);
				if($getItem2){
					if($getItem AND isset($getItem['boardId']) AND $getItem['boardId'] == $getItem2['boardId']){
						http_response_code(400);
						throw new \Exception('Item already exists in this board');
					}
					$boardModule = get_app('forum.forum-board');
					$tca = new \App\Tokenly\TCA_Model;
					$checkCat = $tca->checkItemAccess($data['user'], $boardModule['moduleId'], $getItem2['categoryId'], 'category');
					$checkBoard = $tca->checkItemAccess($data['user'], $boardModule['moduleId'], $getItem2['boardId'], 'board');
					
					if(!$checkCat OR !$checkBoard){
						http_response_code(403);
						throw new \Exception('You do not have permission to move into that board');
					}					
				}
				break;
		}
		if(!$getItem){
			http_response_code(400);
			throw new \Exception($data['from-type'].' not found');
		}
		if(!$getItem2){
			http_response_code(400);
			throw new \Exception($data['to-type'].' not found');
		}
		if((($getItem['userId'] != $data['user']['userId'] AND !$data['perms']['canMoveOther'])
			OR ($getItem['userId'] == $data['user']['userId'] AND !$data['perms']['canMoveSelf']))){
			http_response_code(403);
			throw new \Exception('You do not have permission for this');
		}
		$edit = false;
		switch($data['from-type']){
			case 'topic':
			case 'thread':
				switch($data['to-type']){
					case 'board':
						$edit = $this->edit('forum_topics', $getItem['topicId'], array('boardId' => $getItem2['boardId']));
						break;
				}
				break;
		}
		if(!$edit){
			http_response_code(400);
			throw new \Exception('Error moving '.$data['from-type']);
		}		
		return true;
	}
	
	/**
	* Sets the "lock" state on a forum thread
	*
	* @param $data - data from API controller
	* @param $data['id'] - topicId of thread you want to lock
	* @param $state - set to 1 to lock thread, 0 to unlock thread.
	* @return bool
	*/
	protected function lockThread($data, $state = 1)
	{
		if(!$data['user']){
			http_response_code(401);
			throw new \Exception('Not authorized');
		}
		if(!isset($data['id'])){
			http_response_code(400);
			throw new \Exception('id required');
		}
		$validStates = array(0, 1);
		if(!in_array($state, $validStates)){
			http_response_code(400);
			throw new \Exception('Invalid state');
		}
		$getItem = $this->get('forum_topics', $data['id']);
		if(!$getItem){
			http_response_code(400);
			throw new \Exception('Thread not found');
		}
		if((($getItem['userId'] != $data['user']['userId'] AND !$data['perms']['canLockOther'])
			OR ($getItem['userId'] == $data['user']['userId'] AND !$data['perms']['canLockSelf']))){
			http_response_code(403);
			throw new \Exception('You do not have permission for this');
		}
		$stateMessage = '';
		$responseMessage = '';
		$lockStamp = null;
		$lockBy = 0;
		switch($state){
			case 0:
				$stateMessage = 'unlocked';
				$responseMessage = 'unlocking';
				break;
			case 1:
				$stateMessage = 'locked';
				$responseMessage = 'locking';
				$lockStamp = timestamp();
				$lockBy = $data['user']['userId'];
				break;
		}
		if($getItem['locked'] == $state){
			http_response_code(400);
			throw new \Exception('Thread already '.$stateMessage);
		}
		$lock = $this->edit('forum_topics', $getItem['topicId'], array('locked' => $state, 'lockTime' => $lockStamp, 'lockedBy' => $lockBy));	
		if(!$lock){
			http_response_code(400);
			throw new \Exception('Error '.$responseMessage.' thread');
		}
		return true;
	}
	
	/**
	* Sets the "sticky" state on a forum thread
	*
	* @param $data - data from API controller
	* @param $data['id'] - topicId of thread you want to sticky/unsticky
	* @param $state - set to 1 to sticky thread, 0 to unsticky thread.
	* @return bool
	*/
	protected function stickyThread($data, $state = 1)
	{
		if(!$data['user']){
			http_response_code(401);
			throw new \Exception('Not authorized');
		}
		if(!isset($data['id'])){
			http_response_code(400);
			throw new \Exception('id required');
		}
		$validStates = array(0, 1);
		if(!in_array($state, $validStates)){
			http_response_code(400);
			throw new \Exception('Invalid state');
		}
		$getItem = $this->get('forum_topics', $data['id']);
		if(!$getItem){
			http_response_code(400);
			throw new \Exception('Thread not found');
		}
		if((($getItem['userId'] != $data['user']['userId'] AND !$data['perms']['canStickyOther'])
			OR ($getItem['userId'] == $data['user']['userId'] AND !$data['perms']['canStickySelf']))){
			http_response_code(403);
			throw new \Exception('You do not have permission for this');
		}
		$stateMessage = '';
		$responseMessage = '';
		switch($state){
			case 0:
				$stateMessage = 'unstickied';
				$responseMessage = 'Error removing sticky status from thread';
				break;
			case 1:
				$stateMessage = 'stickied';
				$responseMessage = 'Error setting sticky status on thread';
				break;
		}
		if($getItem['sticky'] == $state){
			http_response_code(400);
			throw new \Exception('Thread already '.$stateMessage);
		}
		$lock = $this->edit('forum_topics', $getItem['topicId'], array('sticky' => $state));	
		if(!$lock){
			http_response_code(400);
			throw new \Exception($responseMessage);
		}
		return true;
	}	
}
