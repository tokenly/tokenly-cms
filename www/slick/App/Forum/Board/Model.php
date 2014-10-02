<?php
class Slick_App_Forum_Board_Model extends Slick_Core_Model
{
	public function getTopicForm()
	{
		$form = new Slick_UI_Form;
		
		$title = new Slick_UI_Textbox('title');
		$title->setLabel('Post Title');
		$title->addAttribute('required');
		$form->add($title);
		
		$content = new Slick_UI_Textarea('content', 'markdown');
		$content->setLabel('Post Body');
		$form->add($content);
		
		return $form;
	}
	

	public function checkURLExists($url)
	{
		return $this->count('forum_topics', 'url', $url);
	}
	
	public function postTopic($data, $appData)
	{
		$useData = array();
		$req = array('boardId' => true, 'userId' => true, 'title' => true, 'content' => true);
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
			throw new Exception('Post body required');
		}
		
		$useData['content'] = strip_tags($useData['content']);
		
		$useData['url'] = genURL($useData['title']);
		if(trim(str_replace('-', '', $useData['url'])) == ''){
			$useData['url'] = substr(md5($useData['title']), 0, 10);
		}
		
		$checkURL = $this->checkURLExists($useData['url']);
		if($checkURL){
			$useData['url'] .= '-'.$checkURL + 1;
		}
		$useData['postTime'] = timestamp();
		$useData['lastPost'] = timestamp();
		
		if($appData['perms']['isTroll']){
			$useData['trollPost'] = 1;
		}
		
		$post = $this->insert('forum_topics', $useData);
		if(!$post){
			throw new Exception('Error posting topic');
		}
		
		if(!isset($useData['trollPost'])){
			$notifyData = $appData;
			$notifyData['url'] = $useData['url'];
			$notifyData['postContent'] = $useData['content'];
			mention($useData['content'], 'emails.forumTopicMention',
					$useData['userId'], $useData['postId'], 'forum-topic', $notifyData);
		}
		
		//auto subscribe to thread
		$subscribe = $this->insert('forum_subscriptions', array('userId' => $useData['userId'], 'topicId' => $post));

		return $useData;
		
	}
	
	public function getBoardTopics($boardId, $data, $page = 1, $all = false)
	{
		$start = 0;
		$max = intval($data['app']['meta']['topicsPerPage']);
		if($page > 1){
			$start = ($page * $max) - $max;
		}
		$limit = 'LIMIT '.$start.', '.$max;
		
		if($all){
			
			$filters = $this->getBoardFilters($data['user']);
			$andFilters = '';
			$filterNum = 0;
			if(count($filters['antifilters']) > 0){
				$andFilters = ' WHERE ';
				foreach($filters['antifilters'] as &$filter){
					$filter = intval($filter);
					if($filterNum > 0){
						$andFilters .= ' AND';
					}
					$andFilters .= ' boardId != '.$filter.' ';
					$filterNum++;
				}
			}
			
			if(isset($_GET['trollVision'])){
				$andTroll = '';
			}
			else{
				if($data['user'] AND $data['perms']['isTroll']){
					if($andFilters != ''){
						$andTroll = ' AND (trollPost = 0 OR (trollPost = 1 AND userId = '.$data['user']['userId'].')) ';
					}
					else{
						$andTroll = ' WHERE (trollPost = 0 OR (trollPost = 1 AND userId = '.$data['user']['userId'].')) ';
					}
				}
				else{
					if($andFilters != ''){
						$andTroll = ' AND trollPost = 0 ';
					}
					else{
						$andTroll = ' WHERE trollPost = 0 ';
					}
				}
			}
			
			$topics = $this->fetchAll('SELECT * 
									   FROM forum_topics
									   '.$andFilters.'
									   '.$andTroll.'
									   AND buried = 0
									   ORDER BY lastPost DESC
									   '.$limit);
		}
		else{
			
			$andTroll = ' AND trollPost = 0 ';
			if(isset($_GET['trollVision'])){
				$andTroll = '';
			}
			else{
				if($data['user'] AND $data['perms']['isTroll']){
					$andTroll = ' AND (trollPost = 0 OR (trollPost = 1 AND userId = '.$data['user']['userId'].')) ';
				}
			}
			
			$topics = $this->fetchAll('SELECT * 
									   FROM forum_topics
									   WHERE boardId = :boardId AND sticky != 1 AND buried = 0
									   '.$andTroll.'
									   ORDER BY
									   lastPost DESC
									   '.$limit, array(':boardId' => $boardId) );
		}
		
		$topics = $this->checkTopicsTCA($topics, $data);
								
		$topics = $this->parseTopics($topics, $data, $all);
		
		return $topics;
		
	}
	
	public function checkTopicsTCA($topics, $data)
	{
		foreach($topics as $k => $row){
			$checkTopic = $this->checkTopicTCA($data['user'], $row);
			if(!$checkTopic){
				unset($topics[$k]);
				continue;
			}
		}
		return $topics;
	}
	
	public function checkTopicTCA($user, $row)
	{
		$tca = new Slick_App_LTBcoin_TCA_Model;
		$boardModule = $this->get('modules', 'forum-board', array(), 'slug');
		$postModule = $this->get('modules', 'forum-post', array(), 'slug');
		$getBoard = $this->get('forum_boards', $row['boardId']);
		$checkCat = $tca->checkItemAccess($user, $boardModule['moduleId'], $getBoard['categoryId'], 'category');
		if(!$checkCat){
			return false;
		}
		$checkBoard = $tca->checkItemAccess($user, $boardModule['moduleId'], $row['boardId'], 'board');
		if(!$checkBoard){
			return false;
		}
		$checkTCA = $tca->checkItemAccess($user, $postModule['moduleId'], $row['topicId'], 'topic');
		if(!$checkTCA){
			return false;
		}
		return true;
	}
	
	public function getAllStickyPosts($data)
	{
		$topics = $this->getAll('forum_topics', array('sticky' => 1, 'buried' => 0));
		$topics = $this->checkTopicsTCA($topics, $data);
		$topics = $this->parseTopics($topics, $data, true);
		return $topics;
		
	}
    
    public function getBoardStickyPosts($data, $boardId)
    {
		$topics = $this->getAll('forum_topics', array('sticky' => 1, 'boardId' => $boardId, 'buried' => 0));
		$topics = $this->checkTopicsTCA($topics, $data);
		$topics = $this->parseTopics($topics, $data, true);
		return $topics;
    }
	
	
	public function parseTopics($topics, $data, $all = false)
	{
		$tca = new Slick_App_LTBcoin_TCA_Model;
		$profModel = new Slick_App_Profile_User_Model;
		$profileModule = $this->get('modules', 'user-profile', array(), 'slug');
		$meta = new Slick_App_Meta_Model;
		$tokenApp = $this->get('apps', 'ltbcoin', array(), 'slug'); 
		$tokenSettings = $meta->appMeta($tokenApp['appId']); 
		
		$andTroll = ' AND trollPost = 0 ';
		if(isset($_GET['trollVision'])){
			$andTroll = '';
		}
		else{
			if($data['user'] AND $data['perms']['isTroll']){
				$andTroll = ' AND (trollPost = 0 OR (trollPost = 1 AND userId = '.$data['user']['userId'].')) ';
			}
		}

		foreach($topics as $key => $row){
			
			$author = $profModel->getUserProfile($row['userId'], $data['site']['siteId']);

			$topics[$key]['author'] = $author;
			$linkClass = '';
			$linkExtra = '';
			if($row['locked'] == 1){
				$linkClass = 'locked';
				$linkExtra = '<i class="fa fa-lock"></i> ';
			}
			if($row['sticky'] == 1){
				$linkClass .= ' sticky';
				$linkExtra .= ' <i class="fa fa-bullhorn" title="Sticky Thread"></i> ';
			}
			$topics[$key]['link'] = '<a href="'.$data['site']['url'].'/'.$data['app']['url'].'/post/'.$row['url'].'" title="'.str_replace('"', '', shorten(strip_tags($row['content']), 150)).'" class="'.$linkClass.'">'.$linkExtra.$row['title'].'</a>';
			if($all){
				$getBoard = $this->get('forum_boards', $row['boardId']);
				$extraBoardClass = '';
				$boardImage = '';
				if($getBoard['categoryId'] == $tokenSettings['tca-forum-category']){
					$extraBoardClass = 'tcv-category';
					//check for access_token link on board
					$access_token = $this->getAll('forum_boardMeta', array('boardId' => $getBoard['boardId'], 'metaKey' => 'access_token'));
					if(count($access_token) > 0){
						$access_token = $access_token[0];
						$getAsset = $this->get('xcp_assetCache', $access_token['value'], array(), 'asset');
						if($getAsset){
							if(trim($getAsset['image']) != ''){
								$boardImage = '<img class="mini-board-img" src="'.$data['site']['url'].'/files/tokens/'.$getAsset['image'].'" alt="" /> ';
							}
						}
					}
				}
				$topics[$key]['link'] .= '<div class="post-category '.$extraBoardClass.'"><a href="'.$data['site']['url'].'/'.$data['app']['url'].'/'.$data['module']['url'].'/'.$getBoard['slug'].'">'.$boardImage.$getBoard['name'].'</a></div>';
			}
			
			$checkAuthorTCA = $tca->checkItemAccess($data['user'], $profileModule['moduleId'], $author['userId'], 'user-profile'); 
			
			$avatar = '';
			$avImage = $author['avatar'];
			if(!isExternalLink($author['avatar'])){
				$avImage = $data['site']['url'].'/files/avatars/'.$author['avatar'];
			}
			$avImage = '<img src="'.$avImage.'" alt="" />';
			if($checkAuthorTCA){
				$avImage = '<a href="'.$data['site']['url'].'/profile/user/'.$author['slug'].'">'.$avImage.'</a>';
			}
			$avatar = '<span class="mini-avatar">'.$avImage.'</span>';
		
			$authorLink = $author['username'];
			if($checkAuthorTCA){
				$authorLink = '<a href="'.$data['site']['url'].'/profile/user/'.$author['slug'].'">'.$authorLink.'</a>';
			}
			
			$topics[$key]['started'] = $avatar.$authorLink.'
										<br>
										<span class="post-date">'.formatDate($row['postTime']).'</span>';
										
			$countReplies = $this->fetchSingle('SELECT count(*) as total
												FROM forum_posts
												WHERE topicId = :topicId AND buried = 0
												'.$andTroll, array(':topicId' => $row['topicId']));
												
			$topics[$key]['numReplies'] = $countReplies['total'];
			
			$topics[$key]['lastPost'] = '';
			if($topics[$key]['numReplies'] > 0){
				$lastPost = $this->fetchSingle('SELECT * 
												FROM forum_posts
												WHERE topicId = :id AND buried = 0
												'.$andTroll.'
												ORDER BY postId DESC
												LIMIT 1', array(':id' => $row['topicId']));
				if($lastPost){
					$lastAuthor = $profModel->getUserProfile($lastPost['userId'], $data['site']['siteId']);
					$lastAuthorTCA = $tca->checkItemAccess($data['user'], $profileModule['moduleId'], $lastAuthor['userId'], 'user-profile');
					$andPage = '';
					$numPages = ceil($topics[$key]['numReplies'] / $data['app']['meta']['postsPerPage']);
					if($numPages > 1){
						$andPage = '?page='.$numPages;
						$topics[$key]['link'] .= '<div class="paging"><strong>Pages:</strong> ';
						for($i = 1; $i <= $numPages; $i++){
							if($numPages > 10){
								if($i == 5){
									$topics[$key]['link'] .= ' ... ';
								}
								if($i < ($numPages - 3)){
									continue;
								}
							}
							$topics[$key]['link'] .= '<a href="'.$data['site']['url'].'/'.$data['app']['url'].'/post/'.$row['url'].'?page='.$i.'">'.$i.'</a>';
						}
						$topics[$key]['link'] .= '</div>';
					}
					$avatar = '';
					$avImage = $lastAuthor['avatar'];
					if(!isExternalLink($lastAuthor['avatar'])){
						$avImage = $data['site']['url'].'/files/avatars/'.$lastAuthor['avatar'];
					}
					$avImage = '<img src="'.$avImage.'" alt="" />';
					if($lastAuthorTCA){
						$avImage = '<a href="'.$data['site']['url'].'/profile/user/'.$lastAuthor['slug'].'">'.$avImage.'</a>';
					}
					$avatar = '<span class="mini-avatar">'.$avImage.'</span>';
					
					$lastAuthorLink = $lastAuthor['username'];
					if($lastAuthorTCA){
						$lastAuthorLink = '<a href="'.$data['site']['url'].'/profile/user/'.$lastAuthor['slug'].'">'.$lastAuthorLink.'</a>';
					}
					
					$topics[$key]['lastPost'] = $avatar.$lastAuthorLink.'
												
												<span class="post-date">'.formatDate($lastPost['postTime']).'</span>';
				}
			}
		}

		return $topics;
		
	}
	
	public function getBoardFilters($user = false)
	{
		$output = array();
		if(!$user){
			//use cookies
			if(isset($_COOKIE['boardFilters'])){
				$output['filters'] = explode(',', $_COOKIE['boardFilters']);
			}
			if(isset($_COOKIE['boardAntiFilters'])){
				$output['antifilters'] = explode(',', $_COOKIE['boardAntiFilters']);
			}
			else{
				//turn this into a setting at some point
				$output['antifilters'] = array(31,47,57,48,50,49,51); //non english boards	
			}
		}
		else{
			$meta = new Slick_App_Meta_Model;
			$userFilters = $meta->getUserMeta($user['userId'], 'boardFilters');
			$userAntiFilters = $meta->getUserMeta($user['userId'], 'boardAntiFilters');
			if($userFilters){
				$output['filters'] = explode(',', $userFilters);
			}
			if($userAntiFilters){
				$output['antifilters'] = explode(',', $userAntiFilters);
			}
			else{
				$output['antifilters'] = array(31,47,57,48,50,49,51); //non english boards
			}
		}
		
		return $output;
	}
	
	public function updateBoardFilters($user = false, $filters = array())
	{
		if(!is_array($filters)){
			$filters = array($filters);
		}
		$filterList = join(',', $filters);
		$getBoards = $this->getAll('forum_boards', array(), array('boardId'));
		$antiFilters = array();
		foreach($getBoards as $board){
			if(!in_array($board['boardId'], $filters)){
				$antiFilters[] = $board['boardId'];
			}
		}
		$antiFilterList = join(',', $antiFilters);
		if(!$user){
			//set for 60 days
			$set = setcookie('boardFilters', $filterList, time()+5184000, '/', $_SERVER['HTTP_HOST']);
			$set = setcookie('boardAntiFilters', $antiFilterList, time()+5184000, '/', $_SERVER['HTTP_HOST']);
		}
		else{
			$meta = new Slick_App_Meta_Model;
			$set = $meta->updateUserMeta($user['userId'], 'boardFilters', $filterList);
			$set2 = $meta->updateUserMeta($user['userId'], 'boardAntiFilters', $antiFilterList);
		}
		if(!$set OR !$set2){
			return false;
		}
		return true;
	}
	
	public function countFilteredTopics($filters = array())
	{
		$andSQL = 'WHERE';
		$num = 0;
		foreach($filters['antifilters'] as &$filter){
			$filter = intval($filter);
			if($num > 0){
				$andSQL .= ' AND';
			}
			$andSQL .= ' boardId != '.$filter.' ';
			
			$num++;
		}
		if(count($filters['antifilters']) == 0){
			$andSQL = '';
		}
		
		$count = $this->fetchSingle('SELECT count(*) as total FROM forum_topics '.$andSQL);
		if(!$count){
			return 0;
		}
		
		return $count['total'];
		
	}
}

?>
