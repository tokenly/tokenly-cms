<?php
class Slick_App_Profile_User_Model extends Slick_Core_Model
{
	public static $profiles = array();
	
	public function getUserProfile($id, $siteId = 0)
	{
		//depreciate use of passing $siteId everywhere..
		$getSite = currentSite();
		$siteId = $getSite['siteId'];
		
		if(isset(self::$profiles[$id])){
			return self::$profiles[$id];
		}
		
		$uFields = array('userId', 'username', 'slug', 'email', 'regDate', 'lastActive', 'lastAuth');
		$get = $this->get('users', $id, $uFields);
		if(!$get){
			$get = $this->get('users', $id, $uFields, 'slug');
			if(!$get){
				return false;
			}
		}
		
		$output = $get;
		$output['profile'] = $this->fetchAll('SELECT f.fieldId, v.value, f.label, f.type,f.slug
												FROM user_profileVals v
												LEFT JOIN profile_fields f ON f.fieldId = v.fieldId
												WHERE v.userId = :userId AND f.public = 1 AND f.active = 1
												AND v.value != "" AND f.siteId = :siteId
												GROUP BY v.fieldId
												ORDER BY f.rank ASC', array(':userId' => $get['userId'], ':siteId' => $siteId));

		$meta = new Slick_App_Meta_Model;
		$output['pubProf'] = $meta->getUserMeta($get['userId'], 'pubProf');
		$output['showEmail'] = $meta->getUserMeta($get['userId'], 'showEmail');
		$output['avatar'] = $meta->getUserMeta($get['userId'], 'avatar');
		
		
		if(trim($output['avatar']) == ''){
			$output['avatar'] = 'https://www.gravatar.com/avatar/'.md5(strtolower($get['email'])).'?d='.urlencode($getSite['url'].'/files/avatars/default.jpg');
			//$output['avatar'] = 'default.jpg';
		}
		
		$prof = array();
		foreach($output['profile'] as $row){
			if(trim($row['slug']) == ''){
				$prof[genURL($row['label'])] = $row;
			}
			else{
				$prof[$row['slug']] = $row;
			}
		}
		$output['profile'] = $prof;

		if(isset($_SERVER['is_api'])){
			if(!isExternalLink($output['avatar'])){
				$getSite = $this->get('sites', $siteId);
				$output['avatar'] = $getSite['url'].'/files/avatars/'.$output['avatar'];
			}
			if(count($output['profile']) == 0){
				$output['profile'] = null;
			}			
		}
		
		return $output;
		
	}
	
	public function getUserAvatar($userId)
	{
		$getUser = $this->get('users', $userId, array('userId', 'username', 'slug', 'email'));
		$site = currentSite();
		$meta = new Slick_App_Meta_Model;
		$avatar = $meta->getUserMeta($userId, 'avatar');
		if(trim($avatar) == ''){
			$avatar = 'https://www.gravatar.com/avatar/'.md5(strtolower($getUser['email'])).'?d='.urlencode($site['url'].'/files/avatars/default.jpg');
		}
		return $avatar;
	}
	
	public function getUsersWithProfile($fieldId)
	{
		$get = $this->getAll('user_profileVals', array('fieldId' => $fieldId));
		$users = array();
		$used = array();
		foreach($get as $row){
			if(trim($row['value']) != ''){
				$getUser = $this->get('users', $row['userId'], array('userId', 'username', 'email'));
				if($getUser){
					if(in_array($row['userId'], $used)){
						continue;
					}
					
					$getUser['lastUpdate'] = $row['lastUpdate'];
					$getUser['value'] = $row['value'];
					$users[] = $getUser;
					array_push($used, $row['userId']);
				}
				
			}
		}
		
		return $users;
		
	}
	
	public function getUserActivity($userId, $user)
	{
		$output = array();
		$tca = new Slick_App_Tokenly_TCA_Model;
		$output['forums'] = false;
		if(app_enabled('forum')){
			$postModel = app_class('forum.forum-post', 'model');
			$forumPage = 1;
			if(isset($_GET['page']) AND isset($_GET['t']) AND $_GET['t'] == 'forums'){
				$forumPage = intval($_GET['page']);
			}
			$output['forums'] = $postModel->getUserPosts($userId, true, 10, $forumPage);
			$postModule = get_app('forum.forum-post');
			$boardModule = get_app('forum.forum-board');
			foreach($output['forums']['posts'] as $k => $post){
				$catTCA = $tca->checkItemAccess($user['userId'], $boardModule['moduleId'], $post['categoryId'], 'category');
				$boardTCA = $tca->checkItemAccess($user['userId'], $boardModule['moduleId'], $post['boardId'], 'board');
				$postTCA = $tca->checkItemAccess($user['userId'], $postModule['moduleId'], $post['topicId'], 'topic');
				
				if(!$catTCA OR !$boardTCA OR !$postTCA){
					unset($output['forums']['posts'][$k]);
					continue;
				}
			}
		}
		
		$output['blog'] = false;
		if(app_enabled('blog')){
			$blogModel = app_class('blog.blog-post', 'model');
			$blogPage = 1;
			if(isset($_GET['page']) AND isset($_GET['t']) AND $_GET['t'] == 'blog'){
				$blogPage = intval($_GET['page']);
			}			
			$output['blog'] = $blogModel->getUserArticles($userId, true, 10, $blogPage);
			
		}
		
		$output['tokenly'] = false;
		if(app_enabled('tokenly')){
			$stats = new Slick_Tags_LTBStats;
			$popLeaders = $stats->getLeaderboardData('pop', false);
			$contentLeaders = $stats->getLeaderboardData('content', false);
			
			$output['tokenly'] = array('pop' => 'N/A', 'content' => 'N/A', 'addresses' => array());
			$num = 1;
			foreach($popLeaders as $leader){
				if($leader['userId'] == $userId){
					$output['tokenly']['pop'] = $num;
					break;
				}
				$num++;
			}
			$num = 1;
			foreach($contentLeaders as $leader){
				if($leader['userId'] == $userId){
					$output['tokenly']['content'] = $num;
					break;
				}
				$num++;
			}			
			
			$output['tokenly']['addresses'] = $this->getAll('coin_addresses',
															array('userId' => $userId, 'verified' => 1, 'public' => 1));
			
		}
		
		return $output;
	}
	
	public function getProfileViews($userId, $update = false)
	{
		$meta = new Slick_App_Meta_Model;
		$views = intval($meta->getUserMeta($userId, 'profile-views'));
		
		if($update){
			if(!isset($_SESSION['viewed_profiles']) OR !in_array($userId, $_SESSION['viewed_profiles'])){
				if(!isset($_SESSION['viewed_profiles'])){
					$_SESSION['viewed_profiles'] = array();
				}
				$_SESSION['viewed_profiles'][] = $userId;
			
				$meta->updateUserMeta($userId, 'profile-views', ($views+1));
			}
		}
		return $views;
	}
	
	
}
