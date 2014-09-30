<?php
/**
* Forum API Model
* 
* Advanced data retrieval & processing for the API. General forum functions
* @package [App][API][V1][Forum]
*
*/
class Slick_App_API_V1_Forum_Model extends Slick_App_Forum_Board_Model
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
	public function getThreadList($data)
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
		$andWhen = $this->checkBeforeInput($data);
		//board filters
		$andFilters = $this->checkBoardFilters($data);
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
		$andFilters .= $this->checkUserFilters($data);
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
			}
		}
		$sql = 'SELECT t.topicId, t.userId, t.title, t.url'.$andContent.', t.boardId, b.name as boardName, b.slug as boardSlug, b.categoryId, c.name as categoryName, c.slug as categorySlug, t.locked, t.postTime, t.editTime, t.lastPost, t.sticky, t.views, t.lockTime, t.lockedBy, t.editedBy
				FROM forum_topics t
				LEFT JOIN forum_boards b ON b.boardId = t.boardId
				LEFT JOIN forum_categories c ON c.categoryId = b.categoryId
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
		$profile = new Slick_App_Profile_User_Model;
		//check for no-profiles field
		$noProfiles = false;
		if(isset($data['no-profiles']) AND (intval($data['no-profiles']) === 1 OR $data['no-profiles'] == 'true')){
			$noProfiles = true;
		}
		foreach($getThreads as $key => &$thread){
			//do TCA checking
			$checkTCA = $this->checkTopicTCA($data['user'], $thread);
			if(!$checkTCA){
				unset($getThreads[$key]);
				continue;
			}
			//reply count
			$countReplies = $this->fetchSingle('SELECT count(*) as total FROM forum_posts WHERE topicId = :topicId AND buried = 0 AND trollPost = 0',
											   array(':topicId' => $thread['topicId']));
			$thread['replies'] = $countReplies['total'];
			//get profile and recent post info
			if($andContent != ''){
				if(!$noProfiles){
					$thread['author'] = $profile->getUserProfile($thread['userId'], $data['site']['siteId']);
					unset($thread['author']['pubProf']);
					unset($thread['author']['showEmail']);
				}
				$thread['mostRecent'] = null;
				$getRecent = $this->fetchSingle('SELECT postId, userId, content, postTime, editTime, editedBy FROM forum_posts WHERE topicId = :topicId AND buried = 0 AND trollPost = 0 ORDER BY postId DESC LIMIT 1',
												array(':topicId' => $thread['topicId']));
				if($getRecent){
					$thread['mostRecent'] = $getRecent;
					if(!$noProfiles){
						$thread['mostRecent']['author'] = $profile->getUserProfile($getRecent['userId'], $data['site']['siteId']);
						unset($thread['mostRecent']['author']['pubProf']);
						unset($thread['mostRecent']['author']['showEmail']);			
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
		if(isset($data['users'])){
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
	*
	*/
	public function getThreadData($thread, $data)
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
			$profile = new Slick_App_Profile_User_Model;
			$thread['author'] = $profile->getUserProfile($thread['userId'], $data['site']['siteId']);
			unset($thread['author']['pubProb']);
			unset($thread['author']['showEmail']);
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
		$output['thread'] = $thread;
		$output['replies'] = $this->getThreadReplies($thread, $data);
		return $output;
	}
	
	/**
	* Grabs list of replies for getThreadData() function
	*
	* @return Array
	*
	*/
	public function getThreadReplies($thread, $data)
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
		$profile = new Slick_App_Profile_User_Model;
		foreach($getPosts as &$post){
			//get profile data
			if(!isset($data['no-profiles']) OR ($data['no-profiles'] != 'true' AND intval($data['no-profiles']) !== 1)){
				$post['author'] = $profile->getUserProfile($post['userId'], $data['site']['siteId']);
				unset($post['author']['pubProf']);
				unset($post['author']['showEmail']);
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
}
