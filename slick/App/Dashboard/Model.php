<?php
namespace App\Dashboard;
use Core;
class Model extends Core\Model
{
	protected function getModuleFromArgs($args)
	{
		$module = false;
		$app = false;
		if(!isset($args[1]) OR $args[1] == 'dashboard' AND isset($args[2]) AND $args[2] == 'home'){
			$app = get_app('dashboard');
			if($app){
				$module = get_app('dashboard.dash-home');

			}
		}
		else{
			$app = get_app(strtolower($args[1]), 'url');
			
			if($app AND isset($args[2])){
				$module = get_app(strtolower($args[1]).'.'.strtolower($args[2]), 'url');	
			}
		}
		
		if($module){
			$checkDash = $this->container->checkModuleIsDash($module['moduleId']);
			if(!$checkDash){
				$module = false;
			}
		}
		$this->container->getDashModules();
		return array('app' => $app, 'module' => $module);
	}
	
	protected function checkModuleIsDash($moduleId, $returnParsed = false)
	{
		$getModule = $this->get('modules', $moduleId);
		if(!$getModule){
			return false;
		}
		$path = app_path($getModule['appId'].'.'.$getModule['moduleId'], 'controller');
		if(!$path){
			return false;
		}
		$parseController = parse_fileComments($path);
		if(!$parseController OR !isset($parseController['module-type']) OR $parseController['module-type'] != 'dashboard'){
			return false;
		}
		if($returnParsed){
			return $parseController;
		}
		return true;
	}
	
	protected function getModuleDashName($moduleId)
	{
		$getModule = $this->get('modules', $moduleId);
		if(!$getModule){
			return false;
		}
		$get = $this->container->checkModuleIsDash($moduleId, true);
		if(isset($get['menu-label']) AND trim($get['menu-label']) != ''){
			return $get['menu-label'];
		}
		return $getModule['name'];
	}
	
	protected function getDashModules($siteId = 0)
	{
		if($siteId == 0){
			$site = currentSite();
			$siteId = $site['siteId'];
		}
				
		$meta = new \App\Meta_Model;
		$cms = get_app('cms');
		$prevHash = $meta->getAppMeta($cms['appId'], 'dashboard-hash');
		$getPrevData = $meta->getAppMeta($cms['appId'], 'dashboard-modules-'.$siteId);
		$curHash = $this->container->getDashHash();
		
		if($curHash == $prevHash AND $getPrevData){
			$getPrevData = json_decode($getPrevData, true);
			return $getPrevData;
		}
		else{
			
			$getApps = $this->fetchAll('SELECT s.appId
										FROM site_apps s
										LEFT JOIN apps a ON a.appId = s.appId
										WHERE s.siteId = :siteId AND a.active = 1
										GROUP BY s.appId
										ORDER BY a.name ASC, a.appId ASC',
										array(':siteId' => $siteId));
			$output = array();
			
			foreach($getApps as $app){
				$getModules = $this->getAll('modules', array('appId' => $app['appId'], 'active' => 1));
				foreach($getModules as $module){
					$checkDash = $this->container->checkModuleIsDash($module['moduleId'], true);
					if($checkDash){
						if(!isset($output[$app['appId']])){
							$output[$app['appId']] = array();
						}
						if(is_array($checkDash)){
							foreach($checkDash as $k => $v){
								if(!isset($module[$k])){
									$module[$k] = $v;
								}
							}
						}
						$module['label'] = $module['name'];
						if(isset($module['menu-label'])){
							$module['label'] = $module['menu-label'];
						}
						$output[$app['appId']][] = $module;
						aasort($output[$app['appId']], 'label');	
					}
				}
			}
			$meta->updateAppMeta($cms['appId'], 'dashboard-modules-'.$siteId, json_encode($output));
			$meta->updateAppMeta($cms['appId'], 'dashboard-hash', $curHash);
			return $output;
		}
	}
	
	protected function getDashHash()
	{
		$get = $this->fetchSingle('SELECT moduleId FROM modules ORDER BY moduleId DESC LIMIT 1');
		if(!$get){
			return false;
		}
		return hash('sha256', $get['moduleId']);
	}
}
