<?php
namespace App\Account;
/*
 * @module-type = dashboard
 * @menu-label = Referrals
 * 
 * */
class Referral_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Referral_Model;
	}
	
	protected function init()
	{
		$output = parent::init();

		if(!$this->data['user']){
			redirect($this->site);
		}
		
		$meta = new \App\Meta_Model;
		$getRefLink = $meta->getUserMeta($this->data['user']['userId'], 'ref-link');
		if(!$getRefLink){
			$getRefLink = substr(hash('sha256', $this->data['user']['userId'].$this->data['user']['regDate']), 0, 8);
			$meta->updateUserMeta($this->data['user']['userId'], 'ref-link', $getRefLink);
		}
			
		$output['refs'] = $this->model->getUserRefs($this->data['user']['userId']);
	
		$output['refLink'] = $getRefLink;
		$output['view'] = 'index';
		$output['template'] = 'admin';
		$output['title'] = 'Referrals';

		return $output;	
	}
}
