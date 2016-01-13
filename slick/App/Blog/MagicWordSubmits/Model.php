<?php
namespace App\Blog;
use Core;
class MagicWordSubmits_Model extends Core\Model
{
	protected function getWordSubmissions()
	{
		$get = $this->getAll('pop_words', array(), array(), 'submitId');
		$modules = array();
		$apps = array();
		$users = array();
		
		foreach($get as &$row){
			if(!isset($modules[$row['moduleId']])){
				$modules[$row['moduleId']] = $this->get('modules', $row['moduleId']);
			}
			$module = $modules[$row['moduleId']];
			if(!isset($apps[$module['appId']])){
				$apps[$module['appId']] = $this->get('apps', $module['appId']);
			}
			if(!isset($users[$row['userId']])){
				$users[$row['userId']] = $this->get('users', $row['userId'], array('username', 'email'));
			}
			$user = $users[$row['userId']];
			$app = $apps[$module['appId']];
			$row['itemName'] = '';
			$row['itemType'] = '';
			$row['itemUrl'] = '';
			$row['username'] = $user['username'];
			$row['userEmail'] = $user['email'];
			switch($module['slug']){
				case 'blog-post':
					$row['itemType'] = 'Blog Post';
					$getItem = $this->get('blog_posts', $row['itemId']);
					if($getItem){
						$row['itemName'] = $getItem['title'];
						$row['itemUrl'] = $app['url'].'/'.$module['url'].'/'.$getItem['url'];
					}
					break;
			}
		}
		return $get;
	}
}
