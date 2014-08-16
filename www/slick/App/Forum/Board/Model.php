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
		
		$post = $this->insert('forum_topics', $useData);
		if(!$post){
			throw new Exception('Error posting topic');
		}
		
		mention($useData['content'], '%username% has mentioned you in a 
				<a href="'.$appData['site']['url'].'/'.$appData['app']['url'].'/post/'.$useData['url'].'">forum thread.</a>',
				$useData['userId'], $useData['postId'], 'forum-topic');
				
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
			if(count($filters) > 0){
				foreach($filters as &$filter){
					$filter = intval($filter);
				}
				$andFilters = ' WHERE boardId IN('.join(',', $filters).') ';
			}
			
			$topics = $this->fetchAll('SELECT * 
									   FROM forum_topics
									   '.$andFilters.'
									   ORDER BY lastPost DESC
									   '.$limit);
		}
		else{
			$topics = $this->fetchAll('SELECT * 
									   FROM forum_topics
									   WHERE boardId = :boardId AND sticky != 1
									   ORDER BY
									   lastPost DESC
									   '.$limit, array(':boardId' => $boardId) );
		}
								   
		
		$topics = $this->parseTopics($topics, $data, $all);
		
		return $topics;
		
	}
	
	public function getAllStickyPosts($data)
	{
		$topics = $this->getAll('forum_topics', array('sticky' => 1));
		$topics = $this->parseTopics($topics, $data, true);
		return $topics;
		
	}
    
    public function getBoardStickyPosts($data, $boardId)
    {
		$topics = $this->getAll('forum_topics', array('sticky' => 1, 'boardId' => $boardId));
		$topics = $this->parseTopics($topics, $data, true);
		return $topics;
    }
	
	
	public function parseTopics($topics, $data, $all = false)
	{
		
		$profModel = new Slick_App_Profile_User_Model;

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
				
				$topics[$key]['link'] .= '<div class="post-category">Board: <a href="'.$data['site']['url'].'/'.$data['app']['url'].'/'.$data['module']['url'].'/'.$getBoard['slug'].'">'.$getBoard['name'].'</a></div>';
			}
			$avatar = '';
			
			$avImage = $author['avatar'];
			if(!isExternalLink($author['avatar'])){
				$avImage = $data['site']['url'].'/files/avatars/'.$author['avatar'];
			}
			$avatar = '<span class="mini-avatar"><a href="'.$data['site']['url'].'/profile/user/'.$author['slug'].'"><img src="'.$avImage.'" alt="" /></a></span>';
		

			$topics[$key]['started'] = $avatar.'<a href="'.$data['site']['url'].'/profile/user/'.$author['slug'].'">'.$author['username'].'</a>
										<br>
										<span class="post-date">'.formatDate($row['postTime']).'</span>';
			$topics[$key]['numReplies'] = $this->count('forum_posts', 'topicId', $row['topicId']);
			
			$topics[$key]['lastPost'] = '';
			if($topics[$key]['numReplies'] > 0){
				$lastPost = $this->fetchSingle('SELECT * 
												FROM forum_posts
												WHERE topicId = :id AND buried = 0
												ORDER BY postId DESC
												LIMIT 1', array(':id' => $row['topicId']));
				if($lastPost){
					$lastAuthor = $profModel->getUserProfile($lastPost['userId'], $data['site']['siteId']);
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
					$avatar = '<span class="mini-avatar"><a href="'.$data['site']['url'].'/profile/user/'.$lastAuthor['slug'].'"><img src="'.$avImage.'" alt="" /></a></span>';
					
					
					$topics[$key]['lastPost'] = $avatar.'<a href="'.$data['site']['url'].'/profile/user/'.$lastAuthor['slug'].'">'.$lastAuthor['username'].'</a>
												
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
				$output = explode(',', $_COOKIE['boardFilters']);
			}
		}
		else{
			$meta = new Slick_App_Meta_Model;
			$userFilters = $meta->getUserMeta($user['userId'], 'boardFilters');
			if($userFilters){
				$output = explode(',', $userFilters);
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
		if(!$user){
			//set for 60 days
			$set = setcookie('boardFilters', $filterList, time()+5184000, '/', $_SERVER['HTTP_HOST']);
		}
		else{
			$meta = new Slick_App_Meta_Model;
			$set = $meta->updateUserMeta($user['userId'], 'boardFilters', $filterList);
		}
		if(!$set){
			return false;
		}
		return true;
	}
	
	public function countFilteredTopics($filters = array())
	{
		if(count($filters) == 0){
			return 0;
		}
		
		foreach($filters as &$filter){
			$filter = intval($filter);
		}
		
		$count = $this->fetchSingle('SELECT count(*) as total FROM forum_topics WHERE boardId IN('.join(',',$filters).')');
		if(!$count){
			return 0;
		}
		
		return $count['total'];
		
	}
}

?>
