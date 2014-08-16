<?php
class Slick_App_Controller extends Slick_Core_Controller
{
	public static $pageIndex = array();
	
	function __construct()
	{
		parent::__construct();
		if(!isset($_REQUEST['params'])){
			$this->args = array(0 => '');
		}
		else{
			$this->args = explode('/', $_REQUEST['params']);	
		}
        $this->model = new Slick_Core_Model;
        $this->view = new SLick_App_View;

	}
	
	public function init()
	{
        //first, check what site we are on
        $getSite = $this->model->get('sites', $_SERVER['HTTP_HOST'], array(), 'domain');
       
        if(!$getSite){
			die('Invalid domain name - no site found');
		}
        $siteId = $getSite['siteId'];
		$data = array('pageRequest' => $_REQUEST, 'template' => 'default', 'siteName' => $getSite['name'], 'site' => $getSite);
		$data['title'] = '';
		$data['url'] = '';
		$data['scripts'] = '';

        $settings = new Slick_App_Dashboard_Settings_Model;
        $disabled = $settings->getSetting('systemDisabled');
        $getUser = Slick_App_Account_Home_Model::userInfo();
        if(intval($disabled) === 1){
			if($getUser){
				$settingModule = $this->model->get('modules', 'settings', array(), 'slug');
				$accessFound = false;
				foreach($getUser['groups'] as $group){
					$checkAccess = $this->model->getAll('group_access', array('moduleId' => $settingModule['moduleId'],
																			  'groupId' => $group['groupId']));
					if(count($checkAccess) > 0){
						$accessFound = true;
						break;
					}
				}
				if(!$accessFound){
					$data['view'] = 'disabled';
					$getTheme = $this->model->get('themes', $getSite['themeId']);
					$data['theme'] = $getTheme['location'];
					$data['disabledMessage'] = $settings->getSetting('disabledMessage');
					$this->view->load($data);
					return;
				}
			}
			else{
				$accountApp = $this->model->get('apps', 'account', array(), 'slug'); 

				if(isset($this->args[0]) AND $this->args[0] == $accountApp['url'] AND isset($_GET['forceLogin'])){
					
				}
				else{
					$data['view'] = 'disabled';
					$getTheme = $this->model->get('themes', $getSite['themeId']);
					$data['theme'] = $getTheme['location'];
					$data['disabledMessage'] = $settings->getSetting('disabledMessage');
					$this->view->load($data);
					return;
				}
			}
		}
        
        $getApps = $this->model->fetchAll('SELECT a.*
										   FROM site_apps s
										   LEFT JOIN apps a ON a.appId = s.appId
										   WHERE s.siteId = :id', array(':id' => $siteId));

        $pageIndex = $this->model->getAll('page_index', array('siteId' => $siteId));
        self::$pageIndex = $pageIndex;
        $thisApp = false;
        $thisModule = false;
        $itemId = null;
        foreach($pageIndex as $index){
            if($index['url'] == join('/', $this->args)){
                $getModule = $this->model->get('modules', $index['moduleId']);
                if($getModule AND $getModule['active'] == 1){
					
                    $getApp = $this->model->get('apps', $getModule['appId']);
                    if($getApp AND $getApp['active'] == 1){
						
						$appInSite = 0;
						foreach($getApps as $siteApp){
							if($siteApp['appId'] == $getApp['appId']){
								$appInSite = 1;
							}
						}

						if($appInSite == 1){
							$thisApp = $getApp;
							$thisModule = $getModule;
							$itemId = $index['itemId'];
						}
                    }
                }
            }
        }
        
       
        if(!$thisApp){
            foreach($getApps as $app){
                if($app['url'] == $this->args[0] AND $app['active'] == 1){
                    $thisApp = $app;
                    if(isset($this->args[1])){
                        $getModule = $this->model->fetchSingle('SELECT * FROM modules WHERE appId = :appId AND url = :url AND active = 1',
																array(':appId' => $app['appId'], ':url' => $this->args[1]));
						if($getModule){
							$thisModule = $getModule;
						}
                    }
                    break;
                }
            }
        }
       
        if($thisApp){
			$meta = new Slick_App_Meta_Model;
			$appMeta = $meta->appMeta($thisApp['appId']);
			$thisApp['meta'] = $appMeta;
			
            $className = 'Slick_App_'.$thisApp['location'].'_Controller';
            $class = new $className;
            $class->module = $thisModule;
            $class->app = $thisApp;
            $class->args = $this->args;
            $class->site = $getSite;
            $class->itemId = $itemId; //used for page_index
            $data = array_merge($data, $class->init());
            $data['module'] = $class->module;
            $data['app'] = $thisApp;
            
        }
        else{
			if($getUser){
				$data['user'] = $getUser;
			}
			
			if(join('/', $this->args) == '403'){
				$data['view'] = '403';
			}
			else{
				//nothing found, give 404
				$data['view'] = '404';
			}
        }
        
        if(!$getUser AND isset($data['view']) AND $data['view'] == '403'){
			$this->redirect($getSite['url'].'/account?r='.$_SERVER['REQUEST_URI'], 1);
			die();
		}
        
        if(!isset($data['theme'])){
            $getTheme = $this->model->get('themes', $getSite['themeId']);
			$data['theme'] = $getTheme['location'];

        }
        
        $this->view->load($data);
        
	}
}

?>
