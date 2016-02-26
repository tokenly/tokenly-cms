<?php
namespace App\Blog;
use Core, App\Profile, App\Tokenly, UI, Util;
class Category_Model extends Core\Model
{
	protected function getHomePosts($siteId, $limit = 10)
	{
		$start = 0;
		if(isset($_GET['page'])){
			$page = intval($_GET['page']);
			if($page > 1){
				$start = ($page * $limit) - $limit;
			}
		}
		
		$orderDir = 'DESC';
		$extraOrder = '';
		$sesh_sort = Util\Session::get('blog-sort');
		if(isset($_GET['sort']) OR $sesh_sort){
			$useSort = false;
			
			if($sesh_sort){
				$useSort = $sesh_sort;
			}				
			if(isset($_GET['sort'])){
				$useSort = $_GET['sort'];
			}		
			if($useSort == 'old'){
				$orderDir = 'ASC';
			}
			elseif($useSort == 'top'){
				$extraOrder = 'popular_score DESC, ';
			}
			
			Util\Session::set('blog-sort', $useSort);
		}		
		$time = time();		
		
		$getPosts = $this->fetchAll('SELECT p.postId, p.content, p.title, p.url, p.userId, p.siteId, p.postDate, p.publishDate, p.published,
											p.image, p.excerpt, p.views, p.featured, p.coverImage, p.ready, p.commentCount, p.commentCheck,
											p.formatType, p.editTime, p.editedBy, p.status, p.version,
									 ((((IFNULL(p.views,0) * 10) + (IFNULL(p.commentCount, 0) * 50)) * 10000000) / ('.$time.' - UNIX_TIMESTAMP(p.publishDate))) as popular_score
											
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
									 AND p.featured = 1
									 AND p.coverImage != ""
									 GROUP BY p.postId
									 ORDER BY '.$extraOrder.' p.publishDate '.$orderDir.'
									 LIMIT '.$start.', '.$limit,
									 array(':siteId' => $siteId));
		
		$profModel = new Profile\User_Model;
		$postModel = new Post_Model;
		$submitModel = new Submissions_Model;
		$postModule = get_app('blog.blog-post');
		$catModule = get_app('blog.blog-category');
		
		$tca = new Tokenly\TCA_Model;
		$user = user();
		
		foreach($getPosts as $key => $post){
			$checkApproved = $submitModel->checkPostApproved($post['postId']);
			if(!$checkApproved){
				unset($getPosts[$key]);
				continue;
			}
			
			$postTCA = $tca->checkItemAccess($user, $postModule['moduleId'], $post['postId'], 'blog-post');
			if(!$postTCA){
				unset($getPosts[$key]);
				continue;
			}
			
			$getCats = $this->getAll('blog_postCategories', array('postId' => $post['postId']));
			$cats = array();
			foreach($getCats as $cat){
				$getCat = $this->get('blog_categories', $cat['categoryId']);
				$cats[] = $getCat;
				$catTCA = $tca->checkItemAccess($user, $catModule['moduleId'], $getCat['categoryId'], 'blog-category');
				$blogTCA = $tca->checkItemAccess($user, $catModule['moduleId'], $getCat['blogId'], 'multiblog');
				if(!$catTCA OR !$blogTCA){
					unset($getPosts[$key]);
					continue 2;
				}
			}
			
			$getPosts[$key]['author'] = $profModel->getUserProfile($post['userId'], $siteId);
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
	
	protected function getHomePages($siteId, $limit = 10)
	{
		$count = $this->fetchSingle('SELECT COUNT(DISTINCT(p.postId)) as total 
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
									 AND p.featured = 1
									 GROUP BY b.blogId',
									 array(':siteId' => $siteId));
		if(!$count){
			return false;
		}
		
		$numPages = ceil($count['total'] / $limit);
		
		return $numPages;
									 
	}
	
	protected function getCategoryPosts($categoryId, $siteId, $limit = 10, $exclude = array(), $page = false)
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
		
		$orderDir = 'DESC';
		$extraOrder = '';
		$sesh_sort = Util\Session::get('blog-sort');
		if(isset($_GET['sort']) OR $sesh_sort){
			$useSort = false;
			if($sesh_sort){
				$useSort = $sesh_sort;
			}				
			if(isset($_GET['sort'])){
				$useSort = $_GET['sort'];
			}		
			if($useSort == 'old'){
				$orderDir = 'ASC';
			}
			elseif($useSort == 'top'){
				$extraOrder = 'popular_score DESC, ';
			}
			
			Util\Session::set('blog-sort', $useSort);
		}		
		$time = time();
		$getPosts = $this->fetchAll('SELECT p.*,
									 ((((IFNULL(p.views,0) * 10) + (IFNULL(p.commentCount, 0) * 50)) * 10000000) / ('.$time.' - UNIX_TIMESTAMP(p.publishDate))) as popular_score
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
									 ORDER BY '.$extraOrder.' p.publishDate '.$orderDir.'
									 LIMIT '.$useLimit,
									 array(':siteId' => $siteId));
		
		$profModel = new Profile\User_Model;
		$postModel = new Post_Model;
		$postModule = get_app('blog.blog-post');
		$catModule = get_app('blog.blog-category');
		
		$tca = new Tokenly\TCA_Model;
		$user = user();		
		foreach($getPosts as $key => $post){
			if(in_array($post['postId'], $exclude)){				
				unset($getPosts[$key]);
				continue;
			}
			$postTCA = $tca->checkItemAccess($user, $postModule['moduleId'], $post['postId'], 'blog-post');
			if(!$postTCA){
				unset($getPosts[$key]);
				continue;
			}			
			$getPosts[$key]['author'] = $profModel->getUserProfile($post['userId'], $siteId);
			$getCats = $this->getAll('blog_postCategories', array('postId' => $post['postId']));
			$cats = array();
			foreach($getCats as $cat){
				$getCat = $this->get('blog_categories', $cat['categoryId']);
				$cats[] = $getCat;
				$catTCA = $tca->checkItemAccess($user, $catModule['moduleId'], $getCat['categoryId'], 'blog-category');
				$blogTCA = $tca->checkItemAccess($user, $catModule['moduleId'], $getCat['blogId'], 'multiblog');
				if(!$catTCA OR !$blogTCA){
					unset($getPosts[$key]);
					continue 2;
				}				
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
			$getPosts = array_merge($getPosts, $this->container->getCategoryPosts($children['categoryId'], $siteId, $limit, $exclude));
		}
		

		return $getPosts;
		
	}
	
	protected function getCategoryPages($categoryId, $siteId, $limit = 10)
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
	
	protected function getBlogHomePosts($blogId, $limit = 10)
	{
		$start = 0;
		if(isset($_GET['page'])){
			$page = intval($_GET['page']);
			if($page > 1){
				$start = ($page * $limit) - $limit;
			}
		}
		
		$orderDir = 'DESC';
		$extraOrder = '';
		$sesh_sort = Util\Session::get('blog-sort');
		if(isset($_GET['sort']) OR $sesh_sort){
			$useSort = false;
			if($sesh_sort){
				$useSort = $sesh_sort;
			}				
			if(isset($_GET['sort'])){
				$useSort = $_GET['sort'];
			}		
			if($useSort == 'old'){
				$orderDir = 'ASC';
			}
			elseif($useSort == 'top'){
				$extraOrder = 'popular_score DESC, ';
			}
			
			Util\Session::set('blog-sort', $useSort);
		}		
		$time = time();	
		
		$getPosts = $this->fetchAll('SELECT p.postId, p.content, p.title, p.url, p.userId, p.siteId, p.postDate, p.publishDate, p.published,
											p.image, p.excerpt, p.views, p.featured, p.coverImage, p.ready, p.commentCount, p.commentCheck,
											p.formatType, p.editTime, p.editedBy, p.status, p.version,
									((((IFNULL(p.views,0) * 10) + (IFNULL(p.commentCount, 0) * 50)) * 10000000) / ('.$time.' - UNIX_TIMESTAMP(p.publishDate))) as popular_score

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
									 ORDER BY '.$extraOrder.' p.publishDate '.$orderDir.'
									 LIMIT '.$start.', '.$limit,
									 array(':blogId' => $blogId));
		$site = currentSite();
		
		$profModel = new Profile\User_Model;
		$postModel = new Post_Model;
		$submitModel = new Submissions_Model;
		$postModule = get_app('blog.blog-post');
		$catModule = get_app('blog.blog-category');
		
		$tca = new Tokenly\TCA_Model;
		$user = user();				
		
		foreach($getPosts as $key => $post){
			$checkApproved = $submitModel->checkPostApproved($post['postId']);
			if(!$checkApproved){
				unset($getPosts[$key]);
				continue;
			}
			$postTCA = $tca->checkItemAccess($user, $postModule['moduleId'], $post['postId'], 'blog-post');
			if(!$postTCA){
				unset($getPosts[$key]);
				continue;
			}					
			$getPosts[$key]['author'] = $profModel->getUserProfile($post['userId'], $site['siteId']);
			$getCats = $this->getAll('blog_postCategories', array('postId' => $post['postId']));
			$cats = array();
			foreach($getCats as $cat){
				$getCat = $this->get('blog_categories', $cat['categoryId']);
				$cats[] = $getCat;
				$catTCA = $tca->checkItemAccess($user, $catModule['moduleId'], $getCat['categoryId'], 'blog-category');
				$blogTCA = $tca->checkItemAccess($user, $catModule['moduleId'], $getCat['blogId'], 'multiblog');
				if(!$catTCA OR !$blogTCA){
					unset($getPosts[$key]);
					continue 2;
				}					
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
	
	protected function getBlogHomePages($blogId, $limit = 10)
	{
		$count = $this->fetchSingle('SELECT count(DISTINCT(p.postId)) as total
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
									 GROUP BY b.blogId
									 ',
									 array(':blogId' => $blogId));
		if(!$count){
			return false;
		}
		
		$numPages = ceil($count['total'] / $limit);
		
		return $numPages;
									 
	}	
	
}
