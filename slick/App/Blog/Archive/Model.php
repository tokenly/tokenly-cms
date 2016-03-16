<?php
namespace App\Blog;
use Core, App\Profile;
class Archive_Model extends Core\Model
{
	protected function getArchivePosts($siteId, $limit = 10, $year, $month, $day, $useMonth, $useDay)
	{
		$start = 0;
		if(isset($_GET['page'])){
			$page = intval($_GET['page']);
			if($page > 1){
				$start = ($page * $limit) - $limit;
			}
		}
		
		if($month < 10){
			$month = '0'.$month;
		}
		if($day < 10){
			$day = '0'.$day;
		}
		
		if($useMonth == 1 AND $useDay == 1){
			$newDay = $day+1;
			$newMonth = $month;
			$newYear = $year;
			$numDays = cal_days_in_month(CAL_GREGORIAN, intval($month), $year);
			if($newDay > $numDays){
				$newDay = '01';
				$newMonth = $month + 1;
				if($newMonth > 12){
					$newYear = $year + 1;
				}
			}
			
			$range = 'publishDate >= "'.$year.'-'.$month.'-'.$day.' 00:00:00" AND publishDate < "'.$newYear.'-'.$newMonth.'-'.$newDay.' 00:00:00"';
		}
		elseif($useMonth == 1 AND $useDay == 0){
			$newMonth = $month+1;
			$newYear = $year;
			if($newMonth > 12){
				$newMonth = '01';
				$newYear = $year + 1;
			}
			
			$range = 'publishDate >= "'.$year.'-'.$month.'-01" AND publishDate < "'.$newYear.'-'.$newMonth.'-01"';
		}
		else{
			$range = 'publishDate >= "'.$year.'-01-01" AND publishDate < "'.($year + 1).'-01-01"';
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
									 AND '.$range.'
									 AND pc.approved = 1
									 AND b.active = 1
									 GROUP BY p.postId
									 ORDER BY p.publishDate DESC
									LIMIT '.$start.', '.$limit,
									 array(':siteId' => $siteId));
		
		$profModel = new Profile\User_Model;
		$postModel = new Post_Model;
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
	
	protected function getArchivePages($siteId, $limit = 10, $year, $month, $day, $useMonth, $useDay)
	{
		if($month < 10){
			$month = '0'.$month;
		}
		if($day < 10){
			$day = '0'.$day;
		}
		
		if($useMonth == 1 AND $useDay == 1){
			$newDay = $day+1;
			$newMonth = $month;
			$newYear = $year;
			$numDays = cal_days_in_month(CAL_GREGORIAN, intval($month), $year);
			if($newDay > $numDays){
				$newDay = '01';
				$newMonth = $month + 1;
				if($newMonth > 12){
					$newYear = $year + 1;
				}
			}
			
			$range = 'publishDate >= "'.$year.'-'.$month.'-'.$day.' 00:00:00" AND publishDate < "'.$newYear.'-'.$newMonth.'-'.$newDay.' 00:00:00"';
		}
		elseif($useMonth == 1 AND $useDay == 0){
			$newMonth = $month+1;
			$newYear = $year;
			if($newMonth > 12){
				$newMonth = '01';
				$newYear = $year + 1;
			}
			
			$range = 'publishDate >= "'.$year.'-'.$month.'-01" AND publishDate < "'.$newYear.'-'.$newMonth.'-01"';
		}
		else{
			$range = 'publishDate >= "'.$year.'-01-01" AND publishDate < "'.($year + 1).'-01-01"';
		}

		$count = $this->fetchSingle('SELECT COUNT(*) as total 
									 FROM blog_posts p
									 LEFT JOIN blog_postCategories pc ON pc.postId = p.postId
									 LEFT JOIN blog_categories c ON c.categoryId = pc.categoryId
									 LEFT JOIN blogs b ON b.blogId = c.blogId
									 WHERE p.siteId = :siteId
									 AND p.status = "published"
									 AND p.trash = 0
									 AND p.publishDate <= "'.timestamp().'"
									 AND '.$range.'
									 AND pc.approved = 1
									 AND b.active = 1
									 GROUP BY p.postId
									 ORDER BY p.publishDate DESC',
									 array(':siteId' => $siteId));

		if(!$count){
			return false;
		}
		
		$numPages = ceil($count['total'] / $limit);
		
		return $numPages;							 
	}
}
