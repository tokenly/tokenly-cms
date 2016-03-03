<?php
namespace App\Account;
use Core, UI, Util, API, App\Profile;
class Home_Model extends Core\Model
{

	protected static function getUsersOnline()
	{
		$model = new Core\Model;
		$sql= 'SELECT COUNT(*) as total FROM users
									WHERE  ('.time().' - UNIX_TIMESTAMP(lastActive)) < 7200';
		$get = $model->fetchSingle($sql);
		if(!$get){
			return false;
		}
		
		$meta = new \App\Meta_Model;
		$mostOnline = $meta->getStat('mostOnline');
		if($get['total'] > $mostOnline){
			$meta->updateStat('mostOnline', $get['total']);
		}
		return $get['total'];
		
	}
	
	protected static function getMostOnline()
	{
		$meta = new \App\Meta_Model;
		$mostOnline = $meta->getStat('mostOnline');
		return $mostOnline;
	}
	
	protected static function getOnlineUsers()
	{
		$model = new Profile\User_Model;

		$getUsers = $model->fetchAll('SELECT userId FROM user_sessions
									WHERE auth != "" AND ('.time().' - UNIX_TIMESTAMP(lastActive)) < 7200');
		
		$site = currentSite();
		$used = array();
		foreach($getUsers as $key => $user){
			if(isset($used[$user['userId']])){
				continue;
			}
			$used[$user['userId']] = 1;
			$user = $model->getUserProfile($user['userId'], $site['siteId']);
			$user['link'] = '<a href="'.$site['url'].'/profile/user/'.$user['slug'].'">'.$user['username'].'</a>';

			$getUsers[$key] = $user;
		}
		
		return $getUsers;
		
	}
	
	protected static function getUserPostCount($userId)
	{
		$model = new Core\Model;
		$totalPosts = 0;
		$forumApp = get_app('forum');
		if($forumApp){
			$numTopics = $model->fetchSingle('SELECT count(*) as total FROM forum_topics WHERE userId = :userId AND buried = 0',
											array(':userId' => $userId));
			$numTopics = $numTopics['total'];
			$numReplies = $model->fetchSingle('SELECT count(*) as total FROM forum_posts WHERE userId = :userId AND buried = 0',
											array(':userId' => $userId));
			$numReplies = $numReplies['total'];
			$totalPosts += $numTopics + $numReplies;
		}
		
		return $totalPosts;
	}
	
}
