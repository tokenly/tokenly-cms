<?php
namespace App\API\V1;
use Core, API;
class Blog_Model extends \App\Blog\Submissions_Model
{
	protected function getAllPosts($data, $getExtra = 0, $start = 0, $filled = array())
	{
		$meta = new \App\Meta_Model;
		$blogApp = $this->get('apps', 'blog', array('appId'), 'slug');
		$blogMeta = $meta->appMeta($blogApp['appId']);
		$limit = 15;
		if(isset($blogMeta['postsPerPage'])){
			$limit = intval($blogMeta['postsPerPage']);
		}
		if(isset($data['limit'])){
			$limit = intval($data['limit']);
		}
	
		$origLimit = $limit;
		//$limit += $getExtra;

		if(isset($data['page'])){
			$page = intval($data['page']);
			if($page > 1){
				$start = ($page * $limit) - $limit;
				$start++;
			}
			if($start < 0){
				$start = 0;
			}
		}
	
		if($getExtra > 0){
		//	$start = $start + $limit;
		}
		if(count($filled) >= $limit){
			return $filled;
		}

		$getExtra = 0;
		
		$andCats = '';
		if(isset($data['categories'])){
			$expCats = explode(',', $data['categories']);
			$catList= array();
			foreach($expCats as $expCat){
				$expCat = intval($expCat);
				$catList[] = $expCat;
				$catList = array_merge($catList, $this->container->getChildCategories($expCat));
			}
			$catList = array_unique($catList);
			if(count($catList) > 0){
				$andCats = ' AND p.postId IN((SELECT postId FROM blog_postCategories WHERE categoryId IN('.join(',', $catList).'))) '; 
				//$andCats = ' AND c.categoryId IN('.join(',', $catList).') ';
			}
			else{
				throw new \Exception('Categories not found');
			}
		}
		
	
		
		$siteList = array($data['site']['siteId']);
		if(isset($data['sites'])){
			$expSites = explode(',', $data['sites']);
			if(count($expSites) > 0){
				$newSites = array();
				foreach($expSites as $site){
					$getSite = $this->get('sites', $site);
					if(!$getSite){
						continue;
					}
					$newSites[] = $getSite['siteId'];
					
				}
				if(count($newSites) > 0){
					$siteList = $newSites;
				}
				else{
					throw new \Exception('Sites not found');
				}
			}
		}
		
			
		
		$andUsers = '';
		if(isset($data['users'])){
			$expUsers = explode(',', $data['users']);
			if(count($expUsers) > 0){
				$userList = array();
				foreach($expUsers as $user){
					$getUser = $this->get('users', $user, array('userId'), 'slug');
					if(!$getUser){
						continue;
					}
					$userList[] = $getUser['userId'];
				}
				if(count($userList) > 0){
					$andUsers = ' AND p.userId IN('.join(',', $userList).') ';
				}
				else{
					throw new \Exception('Users not found');
				}
			}
		}
		
		$andFeatured = '';
		if(isset($data['featured'])){
			$data['featured'] = intval($data['featured']);
			$featured = 0;
			if($data['featured'] > 0){
				$featured = 1;
			}
			$andFeatured = ' AND p.featured = '.$featured.' ';
		}
		
		$metaFields = $this->getAll('blog_postMetaTypes', array('siteId' => $data['site']['siteId']));
		$metaFilters = array('true' => array(), 'false' => array());
		$andMeta = '';
		foreach($data as $key => $val){
			foreach($metaFields as $field){
				if($key == $field['slug']){
					$val = strtolower($val);
					if($val == 'true' || $val === true){
						$metaFilters['true'][$field['slug']] = $field['metaTypeId'];
					}
					else{
						$metaFilters['false'][$field['slug']] = $field['metaTypeId'];
					}
					
					
					continue 2;
				}
			}
		}
		
		
		$getPostFields = 'p.postId, p.siteId, p.title, p.url, p.content, p.userId, p.publishDate, p.editTime as modifiedDate, p.image, p.excerpt, p.views, p.featured, p.coverImage, p.commentCount, p.commentCheck, p.formatType ';
		$minimizeData = false;
		if(isset($data['minimize']) AND intval($data['minimize']) === 1){
			$getPostFields = 'p.postId, p.siteId, p.title, p.url, p.excerpt, p.userId, p.publishDate, p.editTime as modifiedDate, p.featured, p.coverImage, p.image, p.formatType';
			$minimizeData = true;
		}
		
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
			$andWhen .= ' AND p.editTime >= "'.$modifiedSince.'" ';
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
			$andWhen .= ' AND p.publishDate <= "'.$postedBefore.'" ';
		}
		
		$andSites = '(';
		$andSiteNum = 0;
		foreach($siteList as $useSite){
			if($andSiteNum > 0){
				$andSites .= ' OR ';
			}
			$andSites .= ' p.siteId = '.$useSite;
			$andSiteNum++;
		}
		$andSites .= ')';
		
		if(count($metaFilters['true']) > 0 || count($metaFilters['false']) > 0){
			
			
			if(count($metaFilters['true']) > 0){
				$andMeta .= ' AND p.postId IN((
									SELECT mv2.postId as metaPostId
								    FROM blog_postMetaTypes mt2
								    LEFT JOIN blog_postMeta mv2 ON mt2.metaTypeId = mv2.metaTypeId
								    WHERE mv2.value != "" AND mt2.metaTypeId IN('.join(',',$metaFilters['true']).') GROUP BY mv2.postId)) ';
			}
			if(count($metaFilters['false']) > 0){
				$andMeta .= ' AND p.postId NOT IN((
									SELECT mv2.postId as metaPostId
								    FROM blog_postMetaTypes mt2
								    LEFT JOIN blog_postMeta mv2 ON mt2.metaTypeId = mv2.metaTypeId
								    WHERE mv2.value != "" AND mt2.metaTypeId IN('.join(',',$metaFilters['false']).') GROUP BY mv2.postId)) ';
				
			}
			

			
			$sql = 'SELECT '.$getPostFields.'
					 FROM blog_posts p
					 WHERE '.$andSites.'
					 AND p.status = "published"
					 AND p.trash = 0
					 AND p.publishDate <= "'.timestamp().'"
					 '.$andCats.'
					 '.$andUsers.'
					 '.$andFeatured.'
					 '.$andMeta.'
					 '.$andWhen.'
					 GROUP BY p.postId
					 ORDER BY p.publishDate DESC
					 LIMIT '.$start.', '.$limit;
		}
		else{
			$sql = 'SELECT '.$getPostFields.'
					 FROM blog_posts p
					 WHERE '.$andSites.'
					 AND p.status = "published"
					 AND p.trash = 0
					 AND p.publishDate <= "'.timestamp().'"
					 '.$andCats.'
					 '.$andUsers.'
					 '.$andFeatured.'
					 '.$andWhen.'
					 ORDER BY p.postId DESC
					 LIMIT '.$start.', '.$limit;
		}
		

		$getPosts = $this->fetchAll($sql);

		$profModel = new \App\Profile\User_Model;
		$postModel = new \App\Blog\Post_Model;
		$submitModel = new \App\Blog\Submissions_Model;
		$tca = new \App\Tokenly\TCA_Model;
		$profileModule = get_app('profile.user-profile');
		$postModule = get_app('blog.blog-post');
		$catModule = get_app('blog.blog-category');
		$isRSS = false;
		if(!isset($data['isRSS'])){
			$disqus = new API\Disqus;
		}
		else{
			$isRSS = true;
		}
		$origExtra = $getExtra;
		if(!isset($data['user'])){
			$data['user'] = false;
		}
		
		$getPosts = $this->container->addAllPostMeta($getPosts);
		foreach($getPosts as $key => $post){
			if(isset($filled[$post['postId']])){
				continue;
			}
			
			$checkApproved = $submitModel->checkPostApproved($post['postId']);
			$postTCA = $tca->checkItemAccess($data['user'], $postModule['moduleId'], $post['postId'], 'blog-post');
			if(!$postTCA OR !$checkApproved){
				unset($getPosts[$key]);
				continue;
			}
			
			
			if(!$minimizeData){
				if(!isset($data['noProfiles']) OR (isset($data['noProfiles']) AND !$data['noProfiles'])){
					$authorTCA = $tca->checkItemAccess($data['user'], $profileModule['moduleId'], $post['userId'], 'user-profile');
					$getPosts[$key]['author'] = $profModel->getUserProfile($post['userId'], $data['site']['siteId']);
					unset($getPosts[$key]['author']['lastActive']);
					unset($getPosts[$key]['author']['lastAuth']);
					if(!$authorTCA){
						$getPosts[$key]['author']['profile'] = array();
					}
				}
			}
		
			$getCats = $this->getAll('blog_postCategories', array('postId' => $post['postId']), array('categoryId'));
			$cats = array();
			foreach($getCats as $cat){
				$getCat = $this->get('blog_categories', $cat['categoryId']);
				if($getCat['image'] != ''){
					$getCat['image'] = $data['site']['url'].'/files/blogs/'.$getCat['image'];
				}
				$cats[] = $getCat;
				$catTCA = $tca->checkItemAccess($data['user'], $catModule['moduleId'], $getCat['categoryId'], 'blog-category');
				$blogTCA = $tca->checkItemAccess($data['user'], $catModule['moduleId'], $getCat['blogId'], 'multiblog');
				if(!$catTCA OR !$blogTCA){
					unset($getPosts[$key]);
					continue 2;
				}
			}		
		
			if(!$isRSS){
				if(!isset($data['noCategories']) OR (isset($data['noCategories']) AND !$data['noCategories'])){
					$getPosts[$key]['categories'] = $cats;
				}
			}
			
			
			$pageIndex = \App\Controller::$pageIndex;
			$getIndex = extract_row($pageIndex, array('itemId' => $post['postId'], 'moduleId' => 28));
			$postURL = $data['site']['url'].'/blog/post/'.$post['url'];
			if($getIndex AND count($getIndex) > 0){
				$postURL = $data['site']['url'].'/'.$getIndex[count($getIndex) - 1]['url'];
			}
			
			if(isset($post['image']) AND trim($post['image']) != ''){
				$getPosts[$key]['image'] = $data['site']['url'].'/files/blogs/'.$post['image'];
			}
			else{
				$getPosts[$key]['image'] = null;
			}
			if(trim($post['coverImage']) != ''){
				$getPosts[$key]['coverImage'] = $data['site']['url'].'/files/blogs/'.$post['coverImage'];
			}
			else{
				$getPosts[$key]['coverImage'] = null;
			}
			
			if(empty($getPosts[$key]['audio-url']) AND !empty($getPosts[$key]['soundcloud-id'])){
				$streamsURL = 'http://api.soundcloud.com/tracks/'.$getPosts[$key]['soundcloud-id'].'/streams?client_id='.SOUNDCLOUD_ID;
				//$getStream = json_decode(file_get_contents($streamsURL), true);
				/*if($getStream AND isset($getStream['http_mp3_128_url'])){
					$getPosts[$key]['audio-url'] = $getStream['http_mp3_128_url'];
				}
				else{*/
					$getPosts[$key]['audio-url'] = 'http://api.soundcloud.com/tracks/'.$getPosts[$key]['soundcloud-id'].'/stream?client_id='.SOUNDCLOUD_ID.'&allow_redirects=true';
				//}
			}
			
			if(isset($getPosts[$key]['audio-url'])){
				if(trim($getPosts[$key]['audio-url']) == ''){
					$getPosts[$key]['audio-url'] = null;
				}
			}
			if(isset($getPosts[$key]['soundcloud-id'])){
				if(trim($getPosts[$key]['soundcloud-id']) == ''){
					$getPosts[$key]['soundcloud-id'] = null;
				}
			}
			
			if($post['formatType'] == 'markdown'){
				$getPosts[$key]['excerpt'] = markdown($post['excerpt']);
				if(isset($post['content'])){
					$getPosts[$key]['content'] = markdown($post['content']);
				}
			}
			unset($getPosts[$key]['formatType']);
			
			if(isset($data['strip-html']) AND ($data['strip-html'] == 'true' || $data['strip-html'] === true)){
				$getPosts[$key]['excerpt'] = strip_tags($post['excerpt']);
				if(isset($post['content'])){
					$getPosts[$key]['content'] = strip_tags($post['content']);
				}
			}
		}
		$getPosts = array_values($getPosts);
		return $getPosts;
	}
	
	protected function addAllPostMeta($posts, $andPrivate = false)
	{
		$idList = array();
		foreach($posts as $post){
			$idList[] = $post['postId'];
		}
		$site = currentSite();
		$postMetaTypes = $this->getAll('blog_postMetaTypes', array('siteId' => $site['siteId']), array('metaTypeId', 'slug', 'rank', 'isPublic'));
		
		if(count($idList) == 0){
			return array();
		}
		
		$getMeta = $this->fetchAll('SELECT postId, value, metaTypeId
									FROM blog_postMeta
									WHERE postId IN('.join(',', $idList).')');
		$postMeta = array();
		foreach($getMeta as $meta){
			foreach($postMetaTypes as $type){
				if($type['metaTypeId'] == $meta['metaTypeId']){
					if(!$andPrivate AND $type['isPublic'] == 0){
						continue 2;
					}
					$meta['slug'] = $type['slug'];
					$meta['rank'] = $type['rank'];
					if(!isset($postMeta[$meta['postId']])){
						$postMeta[$meta['postId']] = array();
					}
					$postMeta[$meta['postId']][] = $meta;
					continue 2;
				}
			}
		}
		foreach($posts as &$post){
			if(!isset($postMeta[$post['postId']])){
				continue;
			}
			$metaList = $postMeta[$post['postId']];
			foreach($metaList as $item){
				$post[$item['slug']] = $item['value'];
			}
		}
		return $posts;
	}
	
	protected function addComment($data, $appData)
	{
		if(!isset($data['postId'])){
			throw new \Exception('postId not set');
		}
		
		if(!isset($data['user'])){
			throw new \Exception('Not logged in');
		}
		
		if(!isset($data['message'])){
			throw new \Exception('Message required');
		}
		
		$model = new \App\Blog\Post_Model;
		$get = $model->get('blog_posts', $data['postId'], array('postId', 'url'), 'url');
		if(!$get){
			$get = $model->get('blog_posts', $data['postId'], array('postId', 'url'));
			if(!$get){
				throw new \Exception('Post not found');
			}
		}
		$data['postId'] = $get['postId'];
		$data['userId'] = $data['user']['userId'];
		
		/* Disqus Comment Code */
		$disqus = new API\Disqus;
		$profModel = new \App\Profile\User_Model;
		$getIndex = $this->getAll('page_index', array('itemId' => $get['postId'], 'moduleId' => 28));
		$postURL = $appData['site']['url'].'/blog/post/'.$get['url'];
		if($getIndex AND count($getIndex) > 0){
			$postURL = $appData['site']['url'].'/'.$getIndex[count($getIndex) - 1]['url'];
			
		}
		$userProf = $profModel->getUserProfile($data['userId'], $appData['site']['siteId']);
		
		if(!$userProf){
			throw new \Exception('Error getting user profile');
		}
		
		$getThread = $disqus->getThread($postURL);
		if(!$getThread){
			throw new \Exception('Comment thread not found');
		}
		$threadId = $getThread['thread']['id'];
		
		$userData = array('id' => $data['userId'], 'username' => $userProf['username'], 'email' => $userProf['email'],
						 'avatar' => $appData['site']['url'].'/files/avatars/'.$userProf['avatar'],'url' => $appData['site']['url'].'/profile/user/'.$userProf['slug']);
		$remote = $disqus->genRemoteAuth($userData);
		$comData = array('remote_auth' => $remote, 'threadId' => $threadId, 'message' => $data['message']);
		
		$postComment = $disqus->makePost($comData);
		if(!$postComment){
			throw new \Exception('Error posting comment');
		}
		
		if(!is_array($postComment)){
			throw new \Exception($postComment);
		}
		
		$com = $postComment;
		
		
		$comment = array();
		$comment['commentId'] = $com['id'];
		$comment['postId'] = $get['postId'];
		
		if(!isset($data['strip_html'])){
			$data['strip_html'] = true;
		}

		if(isset($data['strip_html']) AND ($data['strip_html'] == 'true' || $data['strip_html'] === true)){
			$comment['message'] = strip_tags($com['message']);
		}
		else{
			$comment['message'] = $com['message'];
		}
		
		$comment['commentDate'] = $com['createdAt'];
		$comment['buried'] = 0;
		$comment['editTime'] = null;
		$author = array();
		$author['username'] = $com['author']['name'];
		$author['slug'] = genURL($com['author']['name']);
		$author['regDate'] = $com['author']['joinedAt'];
		$author['profile'] = array();
		$author['avatar'] = $com['author']['avatar']['permalink'];
	
		$getComUser = $model->get('users', $author['username'], array('userId'), 'username');
		if($getComUser){
			$getComProf = $profModel->getUserProfile($getComUser['userId'], $appData['site']['siteId']);
			if($getComProf){
				$author['profile'] = $getComProf['profile'];
				$author['regDate'] = $getComProf['regDate'];
				$author['slug'] = $getComProf['slug'];
			}
		}
		
		$comment['author'] = $author;

		return $comment;
		
		/*
		-- native site comment code --
		$post = $model->postComment($data, $appData);
		
		if($post){
			$output = $model->getComment($post);
			unset($output['author']['pubProf']);
			unset($output['author']['showEmail']);
			return $output;
		}
		throw new \Exception('Error posting comment..');
		*/
	}
	
	protected function getChildCategories($categoryId, $catList = array())
	{
		$get = $this->getAll('blog_categories', array('parentId' => $categoryId), array('categoryId'));
		if(count($get) > 0){
			foreach($get as $row){
				$catList[] = $row['categoryId'];
				$catList = $this->container->getChildCategories($row['categoryId'], $catList);
			}
		}
		return $catList;
	}
}
