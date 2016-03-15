<?php
namespace App\Blog;
use UI, Util, App\Profile, Core;
class Post_Model extends Core\Model
{
	public static $postMetaTypes = false;
	
	protected function getPost($url, $siteId)
	{
		$get = $this->fetchSingle('SELECT * FROM blog_posts WHERE url = :url AND siteId = :siteId',
									array(':url' => $url, ':siteId' => $siteId));
		if(!$get){
			$get = $this->fetchSingle('SELECT * FROM blog_posts WHERE postId = :id AND siteId = :siteId',
									array(':id' => $url, ':siteId' => $siteId));
			if(!$get){
				return false;
			}
		}
		
		$get['modifiedDate'] = $get['editTime'];
		unset($get['editTime']);
		$output = $get;
		$profModel = new \App\Profile\User_Model;
		$output['author'] = $profModel->getUserProfile($get['userId'], $siteId);
		$getMeta = $this->getPostMeta($get['postId']);
		foreach($getMeta as $key => $val){
			if(!isset($output[$key])){
				$output[$key] = $val;
			}
		}
		
		$time = time();
		$check_time = strtotime($get['commentCheck']);
		$diff = $time - $check_time;
		if($diff > 600){
			exec('nohup php '.SITE_BASE.'/scripts/updateBlogPostCommentCounts.php '.$get['postId'].' > /dev/null &');
		}
		
		return $output;
	}
	
	protected function getCommentForm()
	{
		$form = new UI\Form;
		
		$message = new UI\Markdown('message', 'markdown');
		$message->setLabel('Message');
		$form->add($message);
		
		return $form;
	}
	
	protected function getPostComments($postId, $editorial = 0)
	{
		$getSite = currentSite();
		$siteId = $getSite['siteId'];
		
		$getComments = $this->getAll('blog_comments', array('postId' => $postId, 'buried' => 0, 'editorial' => $editorial), array(), 'commentId', 'asc');
		$profModel = new Profile\User_Model;
		foreach($getComments as $key => $comment){
			if($comment['buried'] == 1){
				$getComments[$key]['author'] = 'null';
			}
			else{
				$getComments[$key]['author'] = $profModel->getUserProfile($comment['userId'], $siteId);
			}
			unset($getComments[$key]['userId']);
		}
		
		return $getComments;
		
	}
	
	protected function getComment($commentId)
	{
		$getSite = currentSite();
		$siteId = $getSite['siteId'];
		
		$getComment = $this->get('blog_comments', $commentId);
		if(!$getComment){
			return false;
		}
		$profModel = new User_Model;
		if($getComment['buried'] == 1){
			$getComment['author'] = 'null';
		}
		else{
			$getComment['author'] = $profModel->getUserProfile($getComment['userId'], $siteId);
		}
		unset($getComment['userId']);
		
		return $getComment;
	}
	
	protected function postComment($data, $appData, $editorial = 0)
	{
		if(!isset($data['message']) AND trim($data['message']) == ''){
			throw new \Exception('Message required');
		}
		$useData = array();
		$useData['userId'] = $data['userId'];
		$useData['postId'] = $data['postId'];
		$useData['editorial'] = $editorial;
		$useData['message'] = strip_tags($data['message']);
		$useData['commentDate'] = timestamp();
		$post = $this->insert('blog_comments', $useData);
		if(!$data){
			throw new \Exception('Error posting comment');
		}
		
		if($editorial == 1){
			$whitelist = $this->container->getEditorialCommentWhitelist($data['postId']);
			mention($useData['message'], '%username% has mentioned you in a 
					<a href="'.$appData['site']['url'].'/'.$appData['app']['url'].'/'.$appData['module']['url'].'/edit/'.$appData['post']['postId'].'#comment-'.$post.'" target="_blank">editorial blog comment.</a>',
					$useData['userId'], $post, 'blog-editor-reply', array(), $whitelist);					
		}
		else{
			mention($useData['message'], '%username% has mentioned you in a 
					<a href="'.$appData['site']['url'].'/'.$appData['app']['url'].'/'.$appData['module']['url'].'/'.$appData['post']['url'].'#comment-'.$post.'">blog comment.</a>',
					$useData['userId'], $post, 'blog-reply');					
		}

		
		$notifyData = $appData;
		$notifyData['commentId'] = $post;
		$notifyData['post'] = $appData['post'];
		$notifyData['user'] = $appData['user'];
		if($appData['user']['userId'] != $appData['post']['userId']){
			if($editorial == 1){
				$meta = new \App\Meta_Model;
				$getDiscussions = $meta->getUserMeta($appData['user']['userId'], 'editorial_discussions');
				if($getDiscussions){
					$discussList = explode(',', $getDiscussions);
				}
				else{
					$discussList = array();
				}
				if(!in_array($appData['post']['postId'], $discussList)){
					$discussList[] = $appData['post']['postId'];
					$meta->updateUserMeta($appData['user']['userId'], 'editorial_discussions', join(',', $discussList));
				}
				
				\App\Meta_Model::notifyUser($appData['post']['userId'], 'emails.blog.editorial_comment', $post, 'new-editor-reply', false, $notifyData);
			}
			else{
				\App\Meta_Model::notifyUser($appData['post']['userId'], 'emails.blogCommentNotice', $post, 'new-reply', false, $notifyData);
			}
		}
		
		$noticeList = $this->container->getEditorialDiscussionUsers($appData['post']['postId']);
		foreach($noticeList as $extraNotice){
			if($extraNotice != $appData['user']['userId'] AND $extraNotice != $appData['post']['userId']){
				\App\Meta_Model::notifyUser($extraNotice, 'emails.blog.editorial_comment', $post, 'new-editor-reply', false, $notifyData);
			}
		}		
		
		$useData['commentId'] = $post;
		
		return $useData;
		
	}
	
	protected function getEditorialCommentWhitelist($postId)
	{
		$output = array();
		$getPost = $this->get('blog_posts', $postId, array('postId', 'userId'));
		
		//add author to whitelist
		$output[] = $getPost['userId'];
		
		//add contributors + discussion members to list
		$discussionMembers = $this->container->getEditorialDiscussionUsers($postId);
		foreach($discussionMembers as $member){
			if(!in_array($member, $output)){
				$output[] = $member;
			}
		}
		
		//add any relevant blog team members to list
		$teamMembers = $this->container->getPostBlogTeam($postId);
		foreach($teamMembers as $member){
			if(!in_array($member['userId'], $output)){
				$output[] = $member['userId'];
			}
		}
	
		return $output;
	}
	
	protected function getPostBlogTeam($postId)
	{
		$multiblog = new Multiblog_Model;
		$getCats = $this->fetchAll('SELECT c.blogId
									FROM blog_postCategories pc
									LEFT JOIN blog_categories c ON c.categoryId = pc.categoryId
									LEFT JOIN blogs b ON b.blogId = c.blogId
									WHERE pc.postId = :postId AND b.active = 1
									GROUP BY c.categoryId', array(':postId' => $postId));
		$output = array();
		foreach($getCats as $cat){
			$catTeam = $multiblog->getBlogUserRoles($cat['blogId'], true);
			foreach($catTeam as $member){
				if(!isset($output[$member['userId']])){
					$output[$member['userId']] = $member;
				}
			}
		}
		
		return $output;
		
	}
	
	protected function getEditorialDiscussionUsers($postId)
	{
		$output = array();
		
		$submitModel = new Submissions_Model;
		$getContribs = $submitModel->getPostContributors($postId);
		foreach($getContribs as $contrib){
			$output[] = $contrib['userId'];
		}
		
		$getRows = $this->fetchAll('SELECT userId, metaValue as value FROM user_meta WHERE metaKey = "editorial_discussions"');
		
		foreach($getRows as $user){
			if(in_array($user['userId'], $output)){
				continue;
			}
			$exp = explode(',', $user['value']);
			if(in_array($postId, $exp)){
				$output[] = $user['userId'];
			}
		}
		return $output;
	}
	
	protected function getPostMeta($postId, $fullData = false, $private = false, $site = false)
	{
		if(!$site){
			$site = currentSite();
		}
		if(!self::$postMetaTypes){
			self::$postMetaTypes = $this->getAll('blog_postMetaTypes', array('siteId' => $site['siteId']), array(), 'rank' ,'asc');
		}
		$types = self::$postMetaTypes;

		//return array();
		$getMeta = $this->getAll('blog_postMeta', array('postId' => $postId), array('value', 'metaTypeId'));

		$metaList = array();
		foreach($getMeta as $meta){
			foreach($types as $type){
				if($type['metaTypeId'] == $meta['metaTypeId']){
					if(!$private AND $type['isPublic'] == 0){
						continue;
					}
					$meta['rank'] = $type['rank'];
					$meta['slug'] = $type['slug'];
					$metaList[] = $meta;
					continue;
				}
			}
		}
		aasort($metaList, 'rank');
		if($fullData){
			return $metaList;
		}
		
		$output = array();
		foreach($metaList as $meta){
			if(trim($meta['value']) == ''){
				continue;
			}
			$output[$meta['slug']] = $meta['value'];
		}
		
		return $output;
		
	}
	
	protected function getUserArticles($userId, $andContribs = false, $perPage = false, $page = 1)
	{
		$output = array('posts' => array(), 'written' => 0, 'contribs' => 0, 'count' => 0);
		$submitModel = app_class('blog.blog-submissions', 'model');
		$profModel = app_class('profile.user-profile', 'model');
		
		$getPosts = $this->getAll('blog_posts', array('userId' => $userId, 'trash' => 0, 'status' => 'published'));
		$time = time();
		$site = currentSite();
		$usedPosts = array();

		foreach($getPosts as $k => $post){
			$checkApproved = $submitModel->checkPostApproved($post['postId']);
			if(!$checkApproved){
				unset($getPosts[$k]);
				continue;
			}
			$post['time'] = strtotime($post['publishDate']);
			$diff = $post['time'] - $time;
			if($diff > 0){
				unset($getPosts[$k]);
				continue;
			}
			$post['author'] = $profModel->getUserProfile($post['userId'], $site['siteId']);
			$getCats = $this->getAll('blog_postCategories', array('postId' => $post['postId']));
			$cats = array();
			foreach($getCats as $cat){
				$getCat = $this->get('blog_categories', $cat['categoryId']);
				$cats[] = $getCat;
			}
			$post['categories'] = $cats;	
			$post['role'] = 'Author';		
			$getMeta = $this->container->getPostMeta($post['postId']);
			foreach($getMeta as $mkey => $val){
				if(!isset($post[$mkey])){
					$post[$mkey] = $val;
				}
			}						
			$output['count']++;
			$output['written']++;
			$output['posts'][] = $post;
			$usedPosts[] = $post['postId'];
		}
		
		if($andContribs){
			$contribs = $submitModel->getUserContributedPosts(array('site' => $site, 'user' => array('userId' => $userId)));
			foreach($contribs as $k => $post){
				if(in_array($post['postId'], $usedPosts)){
					unset($contribs[$k]);
					continue;
				}
				$checkApproved = $submitModel->checkPostApproved($post['postId']);
				if(!$checkApproved){
					unset($contribs[$k]);
					continue;
				}
				$post['time'] = strtotime($post['publishDate']);
				$diff = $post['time'] - $time;
				if($diff > 0){
					unset($getPosts[$k]);
					continue;
				}
				$post['author'] = $profModel->getUserProfile($post['userId'], $site['siteId']);
				$getCats = $this->getAll('blog_postCategories', array('postId' => $post['postId']));
				$cats = array();
				foreach($getCats as $cat){
					$getCat = $this->get('blog_categories', $cat['categoryId']);
					$cats[] = $getCat;
				}
				$post['categories'] = $cats;			
				$getMeta = $this->container->getPostMeta($post['postId']);
				foreach($getMeta as $mkey => $val){
					if(!isset($post[$mkey])){
						$post[$mkey] = $val;
					}
				}										
				$output['count']++;
				$output['contribs']++;
				$output['posts'][] = $post;
				$usedPosts[] = $post['postId'];
			}
		}
		
		aasort($output['posts'], 'time');
		$output['posts'] = array_reverse($output['posts']);
		$output['count'] = count($output['posts']);
		$output['num_pages'] = false;
		if($perPage !== false){
			$pager = new Util\Paging;
			$pagePosts = $pager->pageArray($output['posts'], $perPage);
			$output['num_pages'] = count($pagePosts);
			
			if(!isset($pagePosts[$page])){
				$output['posts'] = array();
			}
			else{
				$output['posts'] = $pagePosts[$page];
			}
		}
		return $output;
	}
	
	protected function getPostFirstBlog($postId)
	{
		$getBlogs = $this->fetchAll('SELECT b.*
									FROM blog_postCategories pc
									LEFT JOIN blog_categories c ON c.categoryId = pc.categoryId
									LEFT JOIN blogs b ON b.blogId = c.blogId
									WHERE b.active = 1 AND pc.postId = :postId
									GROUP BY b.blogId',
									array(':postId' => $postId));
		if(!isset($getBlogs[0])){
			return false;
		}
		return $getBlogs[0];
	}
}
