<?php
namespace App\Tokenly;
use Core, UI, Util, API, App\Blog, App\Profile;
class POP_Model extends Core\Model
{
	function __construct()
	{
		parent::__construct();
		$this->weights = $this->container->getScoreWeights();
		$this->coinFieldId = PRIMARY_TOKEN_FIELD;
		$this->fields = array('views', 'register', 'comments', 'posts', 'threads', 'magic-words', 'likes', 'referrals');
		
		$tokenApp = $this->get('apps', 'tokenly', array(), 'slug');
		$meta = new \App\Meta_Model;
		$this->appMeta = $meta->appMeta($tokenApp['appId']);
		
	}

	
	protected function checkFirstView($userId, $moduleId, $itemId = 0)
	{
		$get = $this->getAll('pop_firstView', array('userId' => $userId, 'moduleId' => $moduleId, 'itemId' => $itemId), array('popId'));
		if($get AND count($get) > 0){
			return true;
		}
		return false;
	}
	
	protected static function recordFirstView($userId, $moduleId, $itemId = 0)
	{
		$model = new POP_Model;
		$check = $model->checkFirstView($userId, $moduleId, $itemId);
		if($check){
			return false;
		}
		$data = array('userId' => $userId, 'moduleId' => $moduleId, 'itemId' => $itemId, 'popDate' => timestamp());
		$insert = $model->insert('pop_firstView', $data);
		if(!$insert){
			return false;
		}
		return $insert;
	}
	
	/*
	if timeframe false, get all. else should be array('start' => date, 'end' => date)
	
	*/
	protected function getUserFirstViews($userId, $timeframe = false)
	{
		$values = array(':id' => $userId);
		$sql = 'SELECT popId, popDate FROM pop_firstView WHERE userId = :id';
		if(is_array($timeframe)){
			$sql .= ' AND popDate >= "'.$timeframe['start'].'" AND popDate <= "'.$timeframe['end'].'"';
		}
		$get = $this->fetchAll($sql, $values);
		
		$num = 0;
		$dayNums = array();
		foreach($get as $row){
			$viewDate = date('Y-m-d', strtotime($row['popDate']));
			if(!isset($dayNums[$viewDate])){
				$dayNums[$viewDate] = 1;
			}
			else{
				$dayNums[$viewDate]++;
			}
			$num++;
		}
		$total = count($get);
		
		return array('total' => $total, 'days' => $dayNums);
	}
	
	protected function getNumUserComments($userId, $timeframe = false, $minLength = 0)
	{
		$disqus = new API\Disqus;
		$username = DISQUS_DEFAULT_FORUM.'-'.md5($userId);

		$getPosts = $disqus->getUserPosts($username, false, 100);
		
		if(!$getPosts){
			return array('total' => 0, 'days' => array());
		}
		
		$dayNums = array();
		$numPosts = 0;
		foreach($getPosts as $post){
			if($minLength != 0 AND strlen(strip_tags($post['message'])) < $minLength){
				continue;
			}
			if($timeframe !== false AND isset($timeframe['end'])){
				
				$diff = strtotime($post['createdAt']) - strtotime($timeframe['end']);
				$diff2 = strtotime($post['createdAt']) - strtotime($timeframe['start']);
				if($diff > 0 OR $diff2 < 0){
					continue;
				}
			}
			$postDate = date('Y-m-d', strtotime($post['createdAt']));
			if(!isset($dayNums[$postDate])){
				$dayNums[$postDate] = 1;
			}
			else{
				$dayNums[$postDate]++;
			}
			$numPosts++;
		}
		
		return array('total' => $numPosts, 'days' => $dayNums);
	}
	
	
	/*
	$andTopics = 0/1/-1
	0 = posts only
	1 = posts and topics
	-1 = topics only
	
	*/
	protected function getNumUserPosts($userId, $timeframe = false, $minLength = 0, $andTopics = 1)
	{
		$numPosts = 0;
		$numTopics = 0;
		$values = array(':id' => $userId);
		$dayNums = array();
		if($andTopics >= 0){
			$sql = 'SELECT postId, content, postTime, topicId FROM forum_posts WHERE userId = :id AND buried = 0';
			if(is_array($timeframe)){
				$sql .= ' AND postTime >= "'.$timeframe['start'].'" AND postTime <= "'.$timeframe['end'].'"';
			}
			$get = $this->fetchAll($sql, $values);
			if($minLength > 0){
				foreach($get as $k => $row){
					if(strlen(strip_tags(trim($row['content']))) < $minLength){
						unset($get[$k]);
					}
				}
			}
			foreach($get as $gk => $row){
				$getTopic = $this->get('forum_topics', $row['topicId'], array('topicId', 'buried', 'boardId'));
				if($getTopic['buried'] == 1){
					unset($get[$gk]);
					continue;
				}
				$getBoard = $this->get('forum_boards', $getTopic['boardId'], array('boardId', 'categoryId'));
				if($getBoard['categoryId'] == $this->appMeta['tca-forum-category']){
					unset($get[$gk]);
					continue;
				}
				$rowDate = date('Y-m-d', strtotime($row['postTime']));
				if(!isset($dayNums[$rowDate])){
					$dayNums[$rowDate] = 1;
				}
				else{
					$dayNums[$rowDate]++;
				}
			}
			$numPosts =  count($get);
		}
		if($andTopics != 0){
			$sql2 = 'SELECT topicId, content, postTime, boardId FROM forum_topics WHERE userId = :id AND buried = 0';
			if(is_array($timeframe)){
				$sql2 .= ' AND postTime >= "'.$timeframe['start'].'" AND postTime <= "'.$timeframe['end'].'"';
			}
			$get2 = $this->fetchAll($sql2, $values);
			if($minLength > 0){
				foreach($get2 as $k => $row){
					if(strlen(strip_tags(trim($row['content']))) < $minLength){
						unset($get2[$k]);
					}
				}
			}			
			foreach($get2 as $k2 => $row){
				$getBoard = $this->get('forum_boards', $row['boardId'], array('boardId', 'categoryId'));
				if($getBoard['categoryId'] == $this->appMeta['tca-forum-category']){
					unset($get2[$k2]);
					continue;
				}
				$rowDate = date('Y-m-d', strtotime($row['postTime']));
				if(!isset($dayNums[$rowDate])){
					$dayNums[$rowDate] = 1;
				}
				else{
					$dayNums[$rowDate]++;
				}
			}			
			$numTopics =  count($get2);
		}
		
		return array('total' => ($numPosts + $numTopics), 'days' => $dayNums);
	}
	
	protected function negateUserBuriedPosts($userId, $timeframe = false)
	{
		$sql = 'SELECT postId, content FROM forum_posts WHERE userId = :id AND buried = 1';
		if(is_array($timeframe)){
			$sql .= ' AND postTime >= "'.$timeframe['start'].'" AND postTime <= "'.$timeframe['end'].'"';
		}
		$get = $this->fetchAll($sql, array(':id' => $userId));
		return count($get) * $this->weights['postScore'];
	}
	
	/*
	returns array with num users and user info list
	
	*/
	protected function getNewUsers($timeframe)
	{
		$values = array();
		$sql = 'SELECT userId, username, regDate FROM users WHERE regDate >= "'.$timeframe['start'].'" AND regDate <= "'.$timeframe['end'].'"';
		$get = $this->fetchAll($sql, $values);
		
		$output = array('numUsers' => count($get), 'users' => $get);
		
		return $output;
		
	}
	
	protected function getNumPublishedPosts($userId, $timeframe = false)
	{
		$values = array(':id' => $userId);
		$sql = 'SELECT postId FROM blog_posts WHERE userId = :id AND published = "published" AND featured = 1';
		if(is_array($timeframe)){
			$sql .= ' AND publishDate >= "'.$timeframe['start'].'" AND publishDate <= "'.$timeframe['end'].'"';
		}
		$get = $this->fetchAll($sql, $values);
		
		return count($get);
	}
	
	protected function setPublishedPosts($timeframe)
	{
		$submitModel = new Blog\Submissions_Model;
		$getSite = currentSite();
		
		$sql = 'SELECT postId, userId, title, url, publishDate, views, commentCount FROM blog_posts WHERE status="published" AND featured = 1 AND siteId = :siteId ';
		if(is_array($timeframe)){
			$sql .= ' AND publishDate >= "'.$timeframe['start'].'" AND publishDate <= "'.$timeframe['end'].'"';
		}
		$get = $this->fetchAll($sql, array(':siteId' => $getSite['siteId']));
		foreach($get as $k => $row){
			$row['contribs'] = $submitModel->getPostContributors($row['postId']);
			$checkApproved = $submitModel->checkPostApproved($row['postId']);
			if(!$checkApproved){
				unset($get[$k]);
				continue;
			}
			$get[$k] = $row;
		}
		$this->publishedPosts = $get;
		return $get;
	}
	
	protected function getUserContributedPosts($userId, $timeframe = false)
	{
		if(!isset($this->publishedPosts)){
			$this->container->setPublishedPosts($timeframe);
		}
		
		$output = array('num' => 0, 'total' => 0, 'posts' => array());
		$weight = $this->weights['publishScore'];
		foreach($this->publishedPosts as $post){
			if($post['userId'] == $userId){
				//is an author
				$leftover = 100;
				foreach($post['contribs'] as $contrib){
					$leftover -= $contrib['share'];
				}
				$output['num']++;
				$post['role'] = 'Author';
				$output['posts'][] = $post;
				if($leftover > 0){
					$output['total'] += $weight * ($leftover / 100);
				}
			}
			else{
				//search contributors
				foreach($post['contribs'] as $contrib){
					if($contrib['userId'] == $userId){
						$output['num']++;
						$output['total'] += $weight * ($contrib['share'] / 100);
						$post['role'] = $contrib['role'];
						$output['posts'][] = $post;
					}
				}
			}
		}
		
		return $output;
	}
	
	/*
	check if user is a new registrant
	
	*/
	protected function checkUserIsNew($userId, $timeframe)
	{
		$get = $this->get('users', $userId, array('regDate'));
		if(!$get){
			return false;
		}
		$start = strtotime($timeframe['start']);
		$end = strtotime($timeframe['end']);
		$reg = strtotime($get['regDate']);
		
		if($reg >= $start AND $reg <= $end){
			return true;
		}
		return false;
	}
	
	
	protected function getPopScore($userId, $timeframe = false, $fields = false)
	{
		$popFields = $this->fields;
		if($fields !== false){
			$popFields = $fields;
		}
		
		$output = array('userId' => $userId, 'info' => array(), 'score' => 0, 'extra' => array(), 'negativeScore' => 0);
		foreach($popFields as $field){
			$score = 0;
			$num = 0;
			$negate = 0;
			switch($field){
				case 'views':
					$num = $this->container->getUserFirstViews($userId, $timeframe);
					//$score = $num * $this->weights['viewScore'];
					foreach($num['days'] as $numDay => $dayPosts){
						$score += $this->container->diminishScore($dayPosts, $this->weights['viewScore']);
					}
					$output['extra'] = $num['days'];
					$num = $num['total'];					
					break;
				case 'register':
					$check = $this->container->checkUserIsNew($userId, $timeframe);
					if($check){
						$score = $this->weights['registerScore'];
						$num = 1;
					}
					break;
				case 'comments':
					$num = $this->container->getNumUserComments($userId, $timeframe);
					foreach($num['days'] as $numDay => $dayPosts){
						$score += $this->container->diminishScore($dayPosts, $this->weights['commentScore']);
					}
					$output['extra'] = $num['days'];
					$num = $num['total'];
					break;
				case 'posts':
					$num = $this->container->getNumUserPosts($userId, $timeframe, 0, 0);					
					foreach($num['days'] as $numDay => $dayPosts){
						$score += $this->container->diminishScore($dayPosts, $this->weights['postScore']);
					}
					$output['extra'] = $num['days'];
					$num = $num['total'];
					$negate = $this->container->negateUserBuriedPosts($userId, $timeframe);
					break;
				case 'threads':
					$num = $this->container->getNumUserPosts($userId, $timeframe, 0, -1);
					foreach($num['days'] as $numDay => $dayPosts){
						$score += $this->container->diminishScore($dayPosts, $this->weights['threadScore']);
					}		
					$output['extra'] = $num['days'];
					$num = $num['total'];				
					break;
				case 'magic-words':
					$num = $this->container->getNumUserWords($userId, $timeframe);
					$score = $num * $this->weights['wordScore'];
					break;
				case 'likes':
					$num = $this->container->getNumUserLikes($userId, $timeframe);
					$totalLikes = 0;
					foreach($num['days'] as $numDay => $dayPosts){
						//$score += $this->container->diminishScore($dayPosts, $this->weights['likeScore']);
						$score += $dayPosts['finalScore'] * $this->weights['likeScore'];
						$totalLikes += $dayPosts['num'];
					}
					$num['days']['total_likes'] = $totalLikes;
					$output['extra']['likes'] = $num['days'];
					$num = $num['total'];						
					break;
				case 'referrals':
					$num = $this->container->getNumUserActiveReferrals($userId, $timeframe);
					$score = $num * $this->weights['referralScore'];
					break;
				case 'blog-posts':
					$getScore = $this->container->getUserContributedPosts($userId, $timeframe);
					$num = $getScore['num'];
					$score = $getScore['total'];					
					$output['extra'] = $getScore['posts'];
					break;
				case 'pov':
					$pov = $this->container->getUserPOV($userId, $timeframe);
					$num = $pov['num'];
					$score = $pov['total'];
					$output['extra'] = $pov['posts'];
					break;
				
			}
			
			$output['info'][$field] = $num;
			$output['score'] += $score;
			$output['negativeScore'] += $negate;
			
		}

		return $output;
		
	}
	
	protected function getPopScoreList($timeframe = false, $fields = false)
	{
		$profModel = new Profile\User_Model;
		$getUsers = $profModel->getUsersWithProfile($this->coinFieldId);
		$output = array();
		$scores = array();
		$totalScore = 0;
		$usedAddresses = array();
		foreach($getUsers as $user){
			if(isset($usedAddresses[$user['value']])){
				continue;
			}
			$usedAddresses[$user['value']] = 1;
			$getScore = $this->container->getPopScore($user['userId'], $timeframe, $fields);
			$getScore['address'] = $user['value'];
			$getScore['username'] = $user['username'];
			$getScore['trueScore'] = $getScore['score'];
			$getScore['score'] -= $getScore['negativeScore'];
			$totalScore += $getScore['score'];
			$scores[] = $getScore;
		}
		
		foreach($scores as $score){
			if($score['score'] <= 0){
				continue;
			}
			$score['percent'] = ($score['score'] / $totalScore) * 100;
			$output[] = $score;
		}
		
		return array('totalPoints' => $totalScore, 'data' => $output);
	}
	
	protected function getScoreWeights()
	{
		$getApp = $this->get('apps', 'tokenly', array(), 'slug');
		if(!$getApp){
			return false;
		}
		$appId = $getApp['appId'];
		$meta = new \App\Meta_Model;
		$output = array();
		
		$output['commentScore'] = $meta->getAppMeta($appId, 'pop-comment-weight');
		$output['postScore'] = $meta->getAppMeta($appId, 'pop-forum-post-weight');
		$output['threadScore'] = $meta->getAppMeta($appId, 'pop-forum-topic-weight');
		$output['viewScore'] = $meta->getAppMeta($appId, 'pop-view-weight');
		$output['registerScore'] = $meta->getAppMeta($appId, 'pop-register-weight');
		$output['wordScore'] = $meta->getAppMeta($appId, 'pop-listen-weight');
		$output['likeScore'] = $meta->getAppMeta($appId, 'pop-like-weight');
		$output['referralScore'] = $meta->getAppMeta($appId, 'pop-referral-weight');
		$output['publishScore'] = $meta->getAppMeta($appId, 'pop-publish-weight');
		$output['editorCut'] = $meta->getAppMeta($appId, 'pop-editor-cut');
		$output['writerCut'] = 100 - $output['editorCut'];
		
		foreach($output as $key => $val){
			$output[$key] = floatval($val);
		}
		
		return $output;
	}
	
	protected function diminishScore($numTimes, $weight)
	{
		$total = 0;
		if($numTimes == 0){
			return $total;
		}
		for($i = 1; $i <= $numTimes; $i++){
			$total += $weight / $i;
		}
		return $total;
	}
	
	
	protected function getNumUserWords($userId, $timeframe = false)
	{
		$values = array(':id' => $userId);
		$sql = 'SELECT submitId FROM pop_words WHERE userId = :id';
		if(is_array($timeframe)){
			$sql .= ' AND submitDate >= "'.$timeframe['start'].'" AND submitDate <= "'.$timeframe['end'].'"';
		}
		$get = $this->fetchAll($sql, $values);
		
		return count($get);
	}
	
	protected function getNumUserLikes($userId, $timeframe = false)
	{
		if(!isset($this->likeData)){
			$sql = 'SELECT * FROM user_likes ';
			if(is_array($timeframe)){
				$sql .= ' WHERE likeTime >= "'.$timeframe['start'].'" AND likeTime <= "'.$timeframe['end'].'"';
			}
			$this->likeData = $this->fetchAll($sql);
			if(!$this->likeData){
				array('total' => 0, 'days' => array());	
			}
		}
		$num = 0;
		$dayNums = array();
		foreach($this->likeData as $like){
			if($like['opUser'] != 0){
				$opUser = $like['opUser'];
			}
			else{
				switch($like['type']){
					case 'post':
						$getItem = $this->get('forum_posts', $like['itemId']);
						break;
					case 'topic':
						$getItem = $this->get('forum_topics', $like['itemId']);
						break;
					default:
						$getItem = false;
						break;
				}
				if(!$getItem OR !isset($getItem['userId'])){
					continue;
				}
				$opUser = $getItem['userId'];
			}
			
			if($opUser == $userId AND $like['userId'] != $userId){
				$likeDate = date('Y-m-d', strtotime($like['likeTime']));
				if(!isset($dayNums[$likeDate])){
					$dayNums[$likeDate] = array('num' => 1, 'users' => array());
				}
				else{
					$dayNums[$likeDate]['num']++;
				}
				if(!isset($dayNums[$likeDate]['users'][$like['userId']])){
					$dayNums[$likeDate]['users'][$like['userId']] = array('num' => 1, 'score' => $like['score']);
				}
				else{
					$dayNums[$likeDate]['users'][$like['userId']]['num']++;
					$dayNums[$likeDate]['users'][$like['userId']]['score'] += $like['score'];
				}
				$num++;
			}
		}
		
		foreach($dayNums as &$day){
			foreach($day['users'] as &$dayUser){
				$perPoint = $dayUser['score'] / $dayUser['num'];
				$dayUser['finalScore'] = $this->container->diminishScore($dayUser['num'], $perPoint);
			}
		}
		foreach($dayNums as &$day){
			$day['finalScore'] = 0;
			foreach($day['users'] as $dayUser){
				$day['finalScore'] += $dayUser['finalScore'];
			}
		}
		
		return array('total' => $num, 'days' => $dayNums);	
	}
	
	protected function getNumUserActiveReferrals($userId, $timeframe = false)
	{
		$sql = 'SELECT userId FROM user_referrals
				WHERE affiliateId = :userId';
		$get = $this->fetchAll($sql, array(':userId' => $userId));
		
		$minPop = floatval($this->appMeta['referral-min-active-pop']);
		
		$output = array();
		foreach($get as $row){
			$userPop = $this->container->getPopScore($row['userId'], $timeframe, array('views','comments','posts','threads'));
			$score = $userPop['score'];
			if($score >= $minPop){
				$output[] = $row;
			}
		}
		
		return count($output);
	}
	
	protected function getNumUserPublishedPosts($userId, $timeframe = false)
	{
		$sql = 'SELECT userId FROM blog_posts
				WHERE userId = :userId AND status="published" AND featured = 1';
		if(is_array($timeframe)){
			$sql .= ' AND publishDate >= "'.$timeframe['start'].'" AND publishDate <= "'.$timeframe['end'].'"';
		}				
		
		$get = $this->fetchAll($sql, array(':userId' => $userId));
		
		return count($get);
	}
	
	protected function getNumUserEditedPosts($userId, $timeframe = false)
	{
		$sql = 'SELECT userId FROM blog_posts
				WHERE (editedBy = :editId OR (userId = :userId AND editedBy = 0)) AND status="published" AND featured = 1';
		if(is_array($timeframe)){
			$sql .= ' AND publishDate >= "'.$timeframe['start'].'" AND publishDate <= "'.$timeframe['end'].'"';
		}				
		
		$get = $this->fetchAll($sql, array(':userId' => $userId, 'editId' => $userId));
		
		return count($get);
	}	
	
	protected function getUserPOV($userId, $timeframe = false)
	{
		$score = 0;
		if(!isset($this->disqus)){
			$this->disqus = new API\Disqus;
		}
		if(!isset($this->postModel)){
			$this->postModel = new Blog\Post_Model;
		}
		
		if(!isset($this->publishedPosts)){
			$this->container->setPublishedPosts($timeframe);
		}
		$blogModule = $this->get('modules', 'blog-post', array(), 'slug');
		$pageIndex = \App\Controller::$pageIndex;
		$getSite = currentSite();
		$popScores = array();
		$output = array('num' => 0, 'posts' => array(), 'total' => 0);
		foreach($this->publishedPosts as $k => $post){
			
			if(!isset($post['total_score'])){
				//get view count
				$thisScore =  $post['views'] * $this->weights['viewScore'];
				
				//get magic word count
				$getWords = $this->fetchSingle('SELECT count(*) as total FROM pop_words WHERE moduleId = :blogModule AND itemId = :postId',
											array('blogModule' => $blogModule['moduleId'], 'postId' => $post['postId']));
				$post['wordSubmits'] = $getWords['total'];
				$thisScore += intval($getWords['total']) * $this->weights['wordScore'];
				
				//get disqus comment count
				$getIndex = extract_row($pageIndex, array('itemId' => $post['postId'], 'moduleId' => 28));
				$postURL = $getSite['url'].'/blog/post/'.$post['url'];
				if($getIndex AND count($getIndex) > 0){
					$postURL = $getSite['url'].'/'.$getIndex[count($getIndex) - 1]['url'];
				}
				
				$commentThread = $this->disqus->getThread($postURL, false);
				$post['comments'] = $post['commentCount'];
				if($commentThread){
					$numReplies = $commentThread['thread']['posts'];
					$thisScore += intval($numReplies) * $this->weights['commentScore'];
					$post['comments'] = $numReplies;
				}
				
				$post['total_score'] = $thisScore;	
				$this->publishedPosts[$k] = $post;
			}
			
			$myScore = $post['total_score'];
			$found = false;
			if($post['userId'] == $userId){
				//author, get leftover shares
				$leftover = 100;
				foreach($post['contribs'] as $contrib){
					$leftover -= $contrib['share'];
				}
				$found = true;
				if($leftover > 0){
					$myScore = $myScore * ($leftover / 100);
				}
				$post['role'] = 'Author';
				
			}
			else{
				foreach($post['contribs'] as $contrib){
					if($contrib['userId'] == $userId){
						$found = true;
						$myScore = $myScore * ($contrib['share'] / 100);
						$post['role'] = $contrib['role'];
					}
				}
			}
			
			if($found){
				$post['my_score'] = $myScore;
				$output['posts'][] = $post;
				$score += $myScore;
				$output['num']++;
			}
		}
		$output['total'] = $score;
		return $output;
		
	}
}
