<?php
namespace App\Profile;
use App\Tokenly;
class User_Controller extends \App\ModControl
{
    function __construct()
    {
        parent::__construct();
        $this->model = new User_Model;
        $this->tca = new Tokenly\TCA_Model;
    }
    
    protected function init()
    {
		$output = parent::init();
		
		if(!isset($this->args[2])){
			redirect($this->site);
		}
		
		$getProfile = $this->model->getUserProfile($this->args[2], $this->data['site']['siteId']);
		if(!$getProfile){
			$output['view'] = '404';
			return $output;
		}
		
		if(!$this->data['user'] OR ($this->data['user'] AND $this->data['user']['userId'] != $getProfile['userId'])){
			$checkTCA = $this->tca->checkItemAccess($this->data['user'], $this->data['module']['moduleId'], $getProfile['userId'], 'user-profile');
			if(!$checkTCA){
				$output['view'] = '403';
				return $output;
			}
		}
		$meta = new \App\Meta_Model;
		$output['profile_views'] = $this->model->getProfileViews($getProfile['userId'], true);
		$output['profile'] = $getProfile;
		$output['profile'] = array_merge($output['profile'], user($getProfile['userId']));
		$output['activity'] = $this->model->getUserActivity($getProfile['userId'], $this->data['user']);
		$output['view'] = 'profile';
		$output['title'] = 'User Info - '.$getProfile['username'];
		$output['template'] = 'profile';
		
		$output['page_mod'] = false;
		if(isset($this->args[3])){
			switch($this->args[3]){
				case 'blog-posts':
					$output['page_mod'] = 'blog-only';
					break;
				case 'forum-posts':
					$output['page_mod'] = 'forum-only';
					break;
			}
		}
		
		return $output;
	}
}
