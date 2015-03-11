<?php
class Slick_App_AppControl extends Slick_Core_Controller
{
    public $module = false;
    public $app = false;
    public $site = false;
    public $args = array();
    public $itemId = null;
    
    function __construct()
    {
        parent::__construct();
        $this->model = new Slick_Core_Model;
        
        
    }
    
    public function init()
    {
        $output = array('app' => $this->app, 'module' => $this->module, 'site' => $this->site);
        $output['themeData'] = false;
        $getTheme = $this->model->get('themes', $this->site['themeId'], array(), 'themeId');

		$output['themeData'] = $getTheme;
		
        $className = false;
        if(!$this->module){
            //load default module
            $getDefault = $this->model->get('modules', $this->app['defaultModule'], array(), 'moduleId');
            if($getDefault){
				$this->module = $getDefault;
				$output['module'] = $this->module;
				$className = 'Slick_App_'.$this->app['location'].'_'.$getDefault['location'].'_Controller';
			}
        }
        else{
            $className = 'Slick_App_'.$this->app['location'].'_'.$this->module['location'].'_Controller';
        }

		$tca = new Slick_App_LTBcoin_TCA_Model;
        $output['user'] = Slick_App_Account_Home_Model::userInfo();
        if($output['user']){
			$output['perms'] = array();
			$getPerms = $this->model->getAll('app_perms', array('appId' => $this->app['appId']));
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
		else{
			if(isset($_GET['ref'])){
				$_SESSION['affiliate-ref'] = $_GET['ref'];
			}
		}

        if($className != false){
            $class = new $className;
            $class->args = $this->args;
            $class->data = $output;
            $class->site = $this->site['url'];
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
    
    public static function checkModuleAccess($moduleId, $redirect = true, $andCheckTCA = true)
    {
		$model = new Slick_Core_Model;
		$tca = new Slick_App_LTBcoin_TCA_Model;
		$accountModel = new Slick_App_Account_Home_Model;
		$getSite = currentSite();
		$controller = new Slick_Core_Controller;
		
		if(!isset($_SESSION['accountAuth'])){
			if($redirect){
				$controller->redirect($getSite['url'].'/account?r='.$_SERVER['REQUEST_URI'], 1);
				die();
			}
			return false;
		}
		
		$get = $accountModel->checkSession($_SESSION['accountAuth']);
		if(!$get){
			unset($_SESSION['accountAuth']);
			if($redirect){
				$controller->redirect($getSite['url'].'/account?r='.$_SERVER['REQUEST_URI'], 1);
				die();
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
		
		$defaultReturn = true;
		if($redirect AND $access === 0){
			$defaultReturn = false;
		}
		
		if($andCheckTCA){
			$checkTCA = $tca->checkItemAccess($get['userId'], $moduleId, 0, '', $defaultReturn);
			if($checkTCA){
				$access = 1;
			}
			if(!$checkTCA AND $access === 1){
				$access = 0;
			}
		}

		if($access === 1){
			return true;
		}

		if($redirect){
			$controller->redirect($getSite['url'].'/403?r='.$_SERVER['REQUEST_URI'], 1);
			die();
		}
		
		return false;
	}
	
	public function __install($appId)
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
