<?php
class Slick_App_Blog_Archive_Model extends Slick_Core_Model
{
	

	
	public function getArchivePosts($siteId, $limit = 10, $year, $month, $day, $useMonth, $useDay)
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
		
		$getPosts = $this->fetchAll('SELECT *
									 FROM blog_posts
									 WHERE siteId = :siteId
									 AND published = 1
									 AND '.$range.'
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
			$getPosts[$key]['commentCount'] = $this->count('blog_comments', 'postId', $post['postId']);
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
	
	public function getArchivePages($siteId, $limit = 10, $year, $month, $day, $useMonth, $useDay)
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
									FROM blog_posts
									WHERE siteId = :siteId
									 AND published = 1
									 AND '.$range,
									 array(':siteId' => $siteId));

		if(!$count){
			return false;
		}
		
		$numPages = ceil($count['total'] / $limit);
		
		return $numPages;
									 
	}
}
