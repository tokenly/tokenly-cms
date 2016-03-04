<?php
namespace App;
use Core, App\Tokenly, App\Account, Util;
class AppControl extends Core\Controller
{
    public $module = false;
    public $app = false;
    public $site = false;
    public $args = array();
    public $itemId = null;
    
    function __construct()
    {
        parent::__construct();
        $this->model = new Core\Model;
    }
    
    protected function init()
    {
        $output = array('app' => $this->app, 'module' => $this->module, 'site' => $this->site);
        $output['themeData'] = false;
        $getTheme = $this->model->get('themes', $this->site['themeId'], array(), 'themeId');

		$output['themeData'] = $getTheme;
		
        $className = false;
        $moduleApp = $this->app;
        if(!$this->module){
            //load default module
            $getDefault = $this->model->get('modules', $this->app['defaultModule'], array(), 'moduleId');
            if($getDefault){
				$this->module = $getDefault;
				$output['module'] = $this->module;
				$className = '\\App\\'.$this->app['location'].'\\'.$getDefault['location'].'_Controller';
			}
        }
        else{
			$moduleApp = $this->model->get('apps', $this->module['appId']);
            $className = '\\App\\'.$moduleApp['location'].'\\'.$this->module['location'].'_Controller';
        }

		$tca = new Tokenly\TCA_Model;
        $output['user'] = Account\Auth_Model::userInfo();
        if($output['user']){
			$output['perms'] = array();
			$getPerms = $this->model->getAll('app_perms', array('appId' => $moduleApp['appId']));
			foreach($getPerms as $perm){
				$output['perms'][$perm['permKey']] = false;
				foreach($output['user']['groups'] as $group){
					$getGroupPerms = $this->model->getAll('group_perms', array('groupId' => $group['groupId']));
					foreach($getGroupPerms as $gPerm){
						if($gPerm['permId'] == $perm['permId']){
							$output['perms'][$perm['permKey']] = true;
							break 2;
						}
					}
				}
			}
			$output['perms'] = $tca->checkPerms($output['user']['userId'], $output['perms'], $this->module['moduleId'], 0, '');
			
		}

        if($className != false){
            $class = new $className;
            $class->args = $this->args;
            $class->data = $output;
            $class->site = $this->site['url'].'/';
            $class->itemId = $this->itemId;
            $class->moduleUrl = '/'.$this->app['url'].'/'.$this->module['url'];
            
            $initClass = $class->init();
            if(!is_array($initClass)){
				$initClass = array();
			}
            $output = array_merge($output, $initClass);
		}

        return $output;
    }
    
    protected static function checkModuleAccess($moduleId, $redirect = true, $andCheckTCA = true)
    {
		$model = new Core\Model;
		$tca = new Tokenly\TCA_Model;
		$accountModel = new Account\Auth_Model;
		$getSite = currentSite();
		$sesh_auth = Util\Session::get('accountAuth');
		if(!$sesh_auth){
			if($redirect){
				redirect($getSite['url'].'/account?r='.$_SERVER['REQUEST_URI']);
			}
			return false;
		}
		
		$get = $accountModel->checkSession($sesh_auth);
		if(!$get){
			Util\Session::clear('accountAuth');
			if($redirect){
				redirect($getSite['url'].'/account?r='.$_SERVER['REQUEST_URI']);
			}
			return false;
		}
		
		//get user groups
		$groups = $model->fetchAll('SELECT g.* FROM group_users u LEFT JOIN groups g ON g.groupId = u.groupId WHERE u.userId = :id',
									array(':id' => $get['userId']));
		$access = 0;
		foreach($groups as $group){
			$getPerms = $model->fetchSingle('SELECT * FROM group_access WHERE groupId = :groupId AND moduleId = :moduleId',
											array(':groupId' => $group['groupId'], ':moduleId' => $moduleId));
			if($getPerms){
				$access = 1;
			}
		}
		
		if($andCheckTCA){
			if($access == 1){
				$checkTCA = $tca->checkItemAccess($get['userId'], $moduleId, 0, '', true, 0, true);
			}
			else{
				$checkTCA = $tca->checkItemAccess($get['userId'], $moduleId, 0, '', false, 0, false);
			}
			
			if($checkTCA){
				$access = 1;
			}
			if(!$checkTCA AND $access == 1){
				$access = 0;
			}
		}

		if($access === 1){
			return true;
		}
		if($redirect){
			redirect($getSite['url'].'/403?r='.$_SERVER['REQUEST_URI']);
		}
		return false;
	}
	
	protected function __install($appId)
	{
		$getApp = $this->model->get('apps', $appId);
		if(!$getApp){
			return false;
		}
		
		$getSite = $this->model->getAll('sites', array('isDefault' => 1));
		foreach($getSite as $site){
			$add = $this->model->insert('site_apps', array('siteId' => $site['siteId'], 'appId' => $appId));
			if(!$add){
				return false;
			}
		}
		
		$settingModule = $this->model->get('modules', 'app-settings', array(), 'slug');
		if($settingModule){
			$this->model->insert('dash_menu', array('moduleId' => $settingModule['moduleId'],
													'dashGroup' => $getApp['name'], 'rank' => 0,
													'label' => $getApp['name'].' Settings', 'checkAccess' => 1,
													'params' => '/'.$getApp['slug']));
		}	
		return true;
	}
}
