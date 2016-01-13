<?php
namespace App\API\V1;
use Core;
class Info_Controller extends \Core\Controller
{
	public $methods = array('GET');
	
	function __construct()
	{
		parent::__construct();
		$this->model = new Core\Model;
	}
	
	protected function init($args = array())
	{
		$output = array();
		$settings = new \App\CMS\Settings_Model;
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
		$output['online'] = \App\Account\Home_Model::getUsersOnline();
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
