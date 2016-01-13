<?php
namespace App\Dashboard;
use App\Tokenly;
class DashMenu_Model extends Model
{

	protected static function getDashMenu()
	{
		$output = array();
		$model = new DashMenu_Model;
		$tca = new Tokenly\TCA_Model;
		$dashApp = get_app('dashboard');
		$site = currentSite();
		$user = user();
		if(!$user){
			return false;
		}
		$access = $model->getUserAccessModules($user);
		$getModules = $model->getDashModules();
		
		foreach($getModules as $appId => $modules){
			$getApp = $model->get('apps', $appId);
			if(!isset($output[$getApp['name']])){
				$output[$getApp['name']] = array();
			}
			foreach($modules as $module){
				if($module['checkAccess'] == 1){
					$itemTCA = $tca->checkItemAccess($user['userId'], $module['moduleId'], 0, '', false, 0, true);
					if(!in_array($module['moduleId'], $access) AND !$itemTCA){
						continue;
					}
				}
				$label = $module['name'];
				if(isset($module['menu-label'])){
					$label = $module['menu-label'];
				}
				$url = $site['url'].'/'.$dashApp['url'].'/'.$getApp['url'].'/'.$module['url'];
				$output[$getApp['name']][] = array('label' => $label, 'url' => $url);
			}
			if(count($output[$getApp['name']]) == 0){
				unset($output[$getApp['name']]);
				continue;
			}
		}
		return $output;
	}
	
	protected function getUserAccessModules($user)
	{
		if(!$user){
			return array();
		}
		$groupList = array();
		foreach($user['groups'] as $group){
			$groupList[] = $group['groupId'];
		}
		$access = array();
		$getPerms = array();
		if(count($groupList) > 0){
			$getPerms = $this->fetchAll('SELECT * FROM group_access WHERE groupId IN('.join(',', $groupList).')');
		}
		foreach($getPerms as $perm){
			$access[] = $perm['moduleId'];
		}		
		return $access;
	}
}
