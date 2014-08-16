<?php
class Slick_App_Account_Reset_Controller extends Slick_App_ModControl
{
	public $args;
	public $data;
	
    function __construct()
    {
        parent::__construct();
        $this->model = new Slick_App_Account_Reset_Model;
			
        
    }
    
    public function init()
    {
		$output = parent::init();
		
		$getUser = Slick_App_Account_Home_Model::userInfo();
		if($getUser){
			$this->redirect($this->data['site']['url'].'/account', 1);
			return false;
		}
		
		if(isset($this->args[2])){
			return $this->completeReset();
		}
		
		$output['message'] = '';
		$output['form'] = $this->model->getResetForm();
		
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$sendReset = $this->model->sendPasswordReset($data, $this->data['site']);
			}
			catch(Exception $e){
				$output['message'] = $e->getMessage();
				$sendReset = false;
			}
			
			if($sendReset){
				$output['message'] = 'Password reset sent!';
			}
			
		}
		
		$output['view'] = 'form';
		$output['title'] = 'Reset Password';

		return $output;
    }
    

    private function completeReset()
    {
		$output = array();
		
		if(!isset($this->args[2])){
			return false;
		}
		
		$url = $this->args[2];
		$getLink = $this->model->get('reset_links', $url, array(), 'url');

		if(!$getLink){
			$this->redirect($this->data['site']['url']);
			return false;
		}
		$reqTime = strtotime($getLink['requestTime']);
		$timeDiff = time() - $reqTime;
		$threshold = 7200;
		if($timeDiff > $threshold){
			$this->model->delete('reset_links', $getLink['resetId']);
			$this->redirect($this->data['site']['url']);
			return false;
		}
		
		$output['title'] = 'Reset Password';
		$output['view'] = 'complete';
		$output['form'] = $this->model->getPassResetForm();
		$output['message'] = '';
		$profModel = new Slick_App_Profile_User_Model;
		$output['user'] = $profModel->getUserProfile($getLink['userId'], $this->data['site']['siteId']);
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['userId'] = $getLink['userId'];
			$data['resetId'] = $getLink['resetId'];
			
			try{
				$update = $this->model->completePassChange($data);
			}
			catch(Exception $e){
				$output['message'] = $e->getMessage();
				$update = false;
			}
			
			if($update){
				$output['view'] = 'success';
				return $output;
			}
			
		}
		
		
		return $output;
	}
    
    
    
    
}
