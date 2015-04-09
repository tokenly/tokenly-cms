<?php
class Slick_App_API_V1_Info_Controller extends Slick_Core_Controller
{
	public $methods = array('GET');
	
	function __construct()
	{
		parent::__construct();
		$this->model = new Slick_Core_Model;
	}
	
	public function init($args = array())
	{
		$output = array();
		$settings = new Slick_App_CMS_Settings_Model;
		$siteDown = $settings->getSetting('systemDisabled');
		if(intval($siteDown) === 1){
			$output['system-status'] = 'offline';
		}
		else{
			$output['system-status'] = 'online';
		}
		$output['system-name'] = SYSTEM_NAME;
		$output['site-name'] = $args['data']['site']['name'];
		$output['site-url'] = $args['data']['site']['url'];
		$output['online'] = Slick_App_Account_Home_Model::getUsersOnline();
		$output['total-users'] = $this->model->count('users');
		$output['system-time'] = timestamp();
		$output['sites'] = $this->model->getAll('sites', array(), array('siteId', 'name', 'url', 'domain', 'image'), 'name', 'asc');
		foreach($output['sites'] as $k => $v){
			if(trim($v['image']) != ''){
				$output['sites'][$k]['image'] = $v['url'].'/files/sites/'.$v['image'];
			}
		}
		
		return $output;
	}
	
}
