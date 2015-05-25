<?php
namespace App\Blog;
use Core, App\Profile, App\Tokenly, UI, Util;
class Category_Model extends Core\Model
{
	public function getHomePosts($siteId, $limit = 10)
	{
		$start = 0;
		if(isset($_GET['page'])){
			$page = intval($_GET['page']);
			if($page > 1){
				$start = ($page * $limit) - $limit;
			}
		}
		
		$getPosts = $this->fetchAll('SELECT p.postId, p.content, p.title, p.url, p.userId, p.siteId, p.postDate, p.publishDate, p.published,
											p.image, p.excerpt, p.views, p.featured, p.coverImage, p.ready, p.commentCount, p.commentCheck,
											p.formatType, p.editTime, p.editedBy, p.status, p.version
									 FROM blog_posts p
									 LEFT JOIN blog_postCategories pc ON pc.postId = p.postId
									 LEFT JOIN blog_categories c ON c.categoryId = pc.categoryId
									 LEFT JOIN blogs b ON b.blogId = c.blogId
									 WHERE p.siteId = :siteId
									 AND p.status = "published"
									 AND p.trash = 0
									 AND p.publishDate <= "'.timestamp().'"
									 AND pc.approved = 1
									 AND b.active = 1
									 GROUP BY p.postId
									 ORDER BY p.publishDate DESC
									 LIMIT '.$start.', '.$limit,
									 array(':siteId' => $siteId));
		
		$profModel = new Profile\User_Model;
		$postModel = new Post_Model;
		$submitModel = new Submissions_Model;
		
		foreach($getPosts as $key => $post){
			$checkApproved = $submitModel->checkPostApproved($post['postId']);
			if(!$checkApproved){
				unset($getPosts[$key]);
				continue;
			}
			$getPosts[$key]['author'] = $profModel->getUserProfile($post['userId'], $siteId);
			$getCats = $this->getAll('blog_postCategories', array('postId' => $post['postId']));
			$cats = array();
			foreach($getCats as $cat){
				$getCat = $this->get('blog_categories', $cat['categoryId']);
				$cats[] = $getCat;
			}
			$getPosts[$key]['categories'] = $cats;
			//$getPosts[$key]['commentCount'] = $this->count('blog_comments', 'postId', $post['postId']);
			$getMeta = $postModel->getPostMeta($post['postId']);
			foreach($getMeta as $mkey => $val){
				if(!isset($getPosts[$key][$mkey])){
					$getPosts[$key][$mkey] = $val;
				}
			}
			if(!isset($getPosts[$key]['audio-url']) AND isset($getPosts[$key]['soundcloud-id'])){
				$getPosts[$key]['audio-url'] = 'http://api.soundcloud.com/tracks/'.$getPosts[$key]['soundcloud-id'].'/stream?client_id='.SOUNDCLOUD_ID;
			}
		}
		
		
		return $getPosts;
		
	}
	
	public function getHomePages($siteId, $limit = 10)
	{
		$count = $this->fetchSingle('SELECT COUNT(*) as total 
									 FROM blog_posts p
									 LEFT JOIN blog_postCategories pc ON pc.postId = p.postId
									 LEFT JOIN blog_categories c ON c.categoryId = pc.categoryId
									 LEFT JOIN blogs b ON b.blogId = c.blogId
									 WHERE p.siteId = :siteId
									 AND p.status = "published"
									 AND p.trash = 0
									 AND p.publishDate <= "'.timestamp().'"
									 AND pc.approved = 1
									 AND b.active = 1
									 GROUP BY p.postId',
									 array(':siteId' => $siteId));
		if(!$count){
			return false;
		}
		
		$numPages = ceil($count['total'] / $limit);
		
		return $numPages;
									 
	}
	
	public function getCategoryPosts($categoryId, $siteId, $limit = 10, $exclude = array(), $page = false)
	{
		$start = 0;
		if($page != false){
			$_GET['page'] = $page;
		}
		if(isset($_GET['page'])){
			$page = intval($_GET['page']);
			if($page > 1){
				$start = ($page * $limit) - $limit;
			}
		}
		$useLimit = $start.', '.$limit;
		
		$api = new \App\API\V1\Blog_Model;
		$childCats = $api->getChildCategories($categoryId);
		$catList = array_merge(array($categoryId), $childCats);

		$getPosts = $this->fetchAll('SELECT p.*
									FROM blog_postCategories pc
									LEFT JOIN blog_posts p ON p.postId = pc.postId
									LEFT JOIN blog_categories c ON c.categoryId = pc.categoryId
									LEFT JOIN blogs b ON b.blogId = c.blogId
									 WHERE p.siteId = :siteId
									 AND pc.categoryId IN('.join(',', $catList).')
									 AND p.status = "published"
									 AND p.trash = 0
									 AND p.publishDate <= "'.timestamp().'"
									 AND b.active = 1
									 AND pc.approved = 1
									 GROUP BY p.postId
									 ORDER BY p.publishDate DESC
									 LIMIT '.$useLimit,
									 array(':siteId' => $siteId));
		
		$profModel = new Profile\User_Model;
		$postModel = new Post_Model;
		foreach($getPosts as $key => $post){
			if(in_array($post['postId'], $exclude)){
				unset($getPosts[$key]);
				continue;
			}
			$getPosts[$key]['author'] = $profModel->getUserProfile($post['userId'], $siteId);
			$getCats = $this->getAll('blog_postCategories', array('postId' => $post['postId']));
			$cats = array();
			foreach($getCats as $cat){
				$getCat = $this->get('blog_categories', $cat['categoryId']);
				$cats[] = $getCat;
			}
			$getPosts[$key]['categories'] = $cats;
			//$getPosts[$key]['commentCount'] = $this->count('blog_comments', 'postId', $post['postId']);
			$getMeta = $postModel->getPostMeta($post['postId']);
			foreach($getMeta as $mkey => $val){
				if(!isset($getPosts[$key][$mkey])){
					$getPosts[$key][$mkey] = $val;
				}
			}
			if(!isset($getPosts[$key]['audio-url']) AND isset($getPosts[$key]['soundcloud-id'])){
				$getPosts[$key]['audio-url'] = 'http://api.soundcloud.com/tracks/'.$getPosts[$key]['soundcloud-id'].'/stream?client_id='.SOUNDCLOUD_ID;
			}
			$exclude[] = $post['postId'];
		}
		
		$getChildren = $this->getAll('blog_categories', array('parentId' => $categoryId, 'siteId' => $siteId));
		foreach($getChildren as $children){
			if(count($exclude) >= $limit){
				continue;
			}
			$getPosts = array_merge($getPosts, $this->getCategoryPosts($children['categoryId'], $siteId, $limit, $exclude));
		}
		

		return $getPosts;
		
	}
	
	public function getCategoryPages($categoryId, $siteId, $limit = 10)
	{
		$count = $this->fetchSingle('SELECT COUNT(*) as total 
									FROM blog_postCategories pc
									LEFT JOIN blog_posts p ON p.postId = pc.postId
									LEFT JOIN blog_categories c ON c.categoryId = pc.categoryId
									LEFT JOIN blogs b ON b.blogId = c.blogId
									 WHERE p.siteId = :siteId
									 AND pc.categoryId = :categoryId
									 AND p.status = "published"
									 AND p.trash = 0
									 AND p.publishDate <= "'.timestamp().'"
									 AND b.active = 1
									 AND pc.approved = 1
									 ',
									 array(':siteId' => $siteId, ':categoryId' => $categoryId));
		if(!$count){
			return false;
		}
		$numPages = ceil($count['total'] / $limit);
		
		return $numPages;						 
	}
	
	public function getBlogHomePosts($blogId, $limit = 10)
	{
		$start = 0;
		if(isset($_GET['page'])){
			$page = intval($_GET['page']);
			if($page > 1){
				$start = ($page * $limit) - $limit;
			}
		}
		
		$getPosts = $this->fetchAll('SELECT p.postId, p.content, p.title, p.url, p.userId, p.siteId, p.postDate, p.publishDate, p.published,
											p.image, p.excerpt, p.views, p.featured, p.coverImage, p.ready, p.commentCount, p.commentCheck,
											p.formatType, p.editTime, p.editedBy, p.status, p.version
									 FROM blog_posts p
									 LEFT JOIN blog_postCategories pc ON pc.postId = p.postId
									 LEFT JOIN blog_categories c ON c.categoryId = pc.categoryId
									 LEFT JOIN blogs b ON b.blogId = c.blogId
									 WHERE c.blogId = :blogId
									 AND p.status = "published"
									 AND p.trash = 0
									 AND p.publishDate <= "'.timestamp().'"
									 AND pc.approved = 1
									 AND b.active = 1
									 GROUP BY p.postId
									 ORDER BY p.publishDate DESC
									 LIMIT '.$start.', '.$limit,
									 array(':blogId' => $blogId));
		$site = currentSite();
		
		$profModel = new Profile\User_Model;
		$postModel = new Post_Model;
		$submitModel = new Submissions_Model;
		
		foreach($getPosts as $key => $post){
			$checkApproved = $submitModel->checkPostApproved($post['postId']);
			if(!$checkApproved){
				unset($getPosts[$key]);
				continue;
			}
			$getPosts[$key]['author'] = $profModel->getUserProfile($post['userId'], $site['siteId']);
			$getCats = $this->getAll('blog_postCategories', array('postId' => $post['postId']));
			$cats = array();
			foreach($getCats as $cat){
				$getCat = $this->get('blog_categories', $cat['categoryId']);
				$cats[] = $getCat;
			}
			$getPosts[$key]['categories'] = $cats;
			//$getPosts[$key]['commentCount'] = $this->count('blog_comments', 'postId', $post['postId']);
			$getMeta = $postModel->getPostMeta($post['postId']);
			foreach($getMeta as $mkey => $val){
				if(!isset($getPosts[$key][$mkey])){
					$getPosts[$key][$mkey] = $val;
				}
			}
			if(!isset($getPosts[$key]['audio-url']) AND isset($getPosts[$key]['soundcloud-id'])){
				$getPosts[$key]['audio-url'] = 'http://api.soundcloud.com/tracks/'.$getPosts[$key]['soundcloud-id'].'/stream?client_id='.SOUNDCLOUD_ID;
			}
		}
		
		
		return $getPosts;
		
	}
	
	public function getBlogHomePages($blogId, $limit = 10)
	{
		$count = $this->fetchSingle('SELECT COUNT(*) as total 
									 FROM blog_posts p
									 LEFT JOIN blog_postCategories pc ON pc.postId = p.postId
									 LEFT JOIN blog_categories c ON c.categoryId = pc.categoryId
									 LEFT JOIN blogs b ON b.blogId = c.blogId
									 WHERE c.blogId = :blogId
									 AND p.status = "published"
									 AND p.trash = 0
									 AND p.publishDate <= "'.timestamp().'"
									 AND pc.approved = 1
									 AND b.active = 1
									 GROUP BY p.postId',
									 array(':blogId' => $blogId));
		if(!$count){
			return false;
		}
		
		$numPages = ceil($count['total'] / $limit);
		
		return $numPages;
									 
	}	
	
}
