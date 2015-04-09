<?php
/*
 * @module-type = dashboard
 * @menu-label = Referrals
 * 
 * */
class Slick_App_Account_Referral_Controller extends Slick_App_ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Slick_App_Account_Referral_Model;
		
	}
	
	public function init()
	{
		$output = parent::init();

		if(!$this->data['user']){
			$this->redirect($this->data['site']['url']);
			return false;
		}
		
		$meta = new Slick_App_Meta_Model;
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
