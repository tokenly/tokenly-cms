<?php
class Slick_App_Blog_Category_Model extends Slick_Core_Model
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
		
		$getPosts = $this->fetchAll('SELECT *
									 FROM blog_posts
									 WHERE siteId = :siteId
									 AND published = 1
									 AND publishDate <= "'.timestamp().'"
									 ORDER BY publishDate DESC
									 LIMIT '.$start.', '.$limit,
									 array(':siteId' => $siteId));
		
		$profModel = new Slick_App_Profile_User_Model;
		$postModel = new Slick_App_Blog_Post_Model;
		foreach($getPosts as $key => $post){
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
									FROM blog_posts
									WHERE siteId = :siteId
									 AND published = 1
									 AND publishDate <= "'.timestamp().'"',
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

		$getPosts = $this->fetchAll('SELECT p.*
									FROM blog_postCategories c
									LEFT JOIN blog_posts p ON p.postId = c.postId
									 WHERE p.siteId = :siteId
									 AND c.categoryId = :categoryId
									 AND p.published = 1
									 AND p.publishDate <= "'.timestamp().'"
									 GROUP BY p.postId
									 ORDER BY p.publishDate DESC
									 LIMIT '.$useLimit,
									 array(':siteId' => $siteId, ':categoryId' => $categoryId));
		
		$profModel = new Slick_App_Profile_User_Model;
		$postModel = new Slick_App_Blog_Post_Model;
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
									FROM blog_postCategories c
									LEFT JOIN blog_posts p ON p.postId = c.postId
									 WHERE p.siteId = :siteId
									 AND c.categoryId = :categoryId
									 AND p.published = 1
									 AND p.publishDate <= "'.timestamp().'"
									 ',
									 array(':siteId' => $siteId, ':categoryId' => $categoryId));
		if(!$count){
			return false;
		}
		$numPages = ceil($count['total'] / $limit);
		
		return $numPages;
									 
	}
	

}
