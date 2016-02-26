<?php
namespace App\Profile;
use Core, App\Tokenly, Tags, Util;
class User_Model extends Core\Model
{
	public static $profiles = array();
	
	protected function getUserProfile($id, $siteId = 0)
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

		$meta = new \App\Meta_Model;
		$output['pubProf'] = $meta->getUserMeta($get['userId'], 'pubProf');
		$output['showEmail'] = $meta->getUserMeta($get['userId'], 'showEmail');
		$output['avatar'] = $meta->getUserMeta($get['userId'], 'avatar');
		$output['custom_status'] = $meta->getUserMeta($get['userId'], 'custom_status');
		$output['real_avatar'] = $output['avatar'];
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
		
		$output['groups'] = $this->fetchAll('SELECT g.name, g.groupId, g.displayName, g.displayView, g.displayRank, g.isSilent as silent
										   FROM group_users u
										   LEFT JOIN groups g ON g.groupId = u.groupId
										   LEFT JOIN group_sites s ON s.groupId = g.groupId
										   WHERE u.userId = :id AND s.siteId = :siteId
										   ORDER  BY g.displayRank DESC, g.displayName ASC, g.name ASC
										   ', array(':id' => $get['userId'], ':siteId' => $getSite['siteId']));
		$output['primary_group'] = false;
		$primary_found = false;
		foreach($output['groups'] as $gk => $gv){
			if(trim($gv['displayName']) == ''){
				$output['groups'][$gk]['displayName'] = $gv['name'];
			}
			if(!$primary_found AND $gv['silent'] == 0){
				$output['primary_group'] = $gv;
				$primary_found = true;
			}
		}		
		
		return $output;
	}
	
	protected function getUserAvatar($userId)
	{
		$getUser = $this->get('users', $userId, array('userId', 'username', 'slug', 'email'));
		$site = currentSite();
		$meta = new \App\Meta_Model;
		$avatar = $meta->getUserMeta($userId, 'avatar');
		if(trim($avatar) == ''){
			$avatar = 'https://www.gravatar.com/avatar/'.md5(strtolower($getUser['email'])).'?d='.urlencode($site['url'].'/files/avatars/default.jpg');
		}
		return $avatar;
	}
	
	protected function getUsersWithProfile($fieldId)
	{
		$get = static_cache($fieldId.'_profileVals');
		if(!$get){
			$get = static_cache($fieldId.'_profileVals',
					$this->fetchAll('SELECT p.userId, p.value, p.lastUpdate, u.username, u.email
									 FROM user_profileVals p
									 LEFT JOIN users u ON u.userId = p.userId
									 WHERE p.fieldId = :fieldId
									 GROUP BY p.userId', array(':fieldId' => $fieldId)));
		}
		$users = $get;
		return $users;
	}
	
	protected function getUserActivity($userId, $user)
	{
		$output = array();
		$tca = new Tokenly\TCA_Model;
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
			$stats = new Tags\LTBStats;
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
	
	protected function getProfileViews($userId, $update = false)
	{
		$meta = new \App\Meta_Model;
		$views = intval($meta->getUserMeta($userId, 'profile-views'));
		
		if($update){
			$viewed_profiles = Util\Session::get('viewed_profiles', array());
			if(!$viewed_profiles OR !in_array($userId, $viewed_profiles)){
				Util\Session::set('viewed_profiles', $userId, APPEND_ARRAY);
				$meta->updateUserMeta($userId, 'profile-views', ($views+1));
			}
		}
		return $views;
	}
}
