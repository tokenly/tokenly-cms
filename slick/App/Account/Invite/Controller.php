<?php
namespace App\Account;
use \App\Meta_Model;
class Invite_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Invite_Model;
	}
	
	protected function init()
	{
		$output = parent::init();
		if(!$this->data['user']){
			redirect($this->data['site']['url'].'/account?r='.$_SERVER['REQUEST_URI']);
		}		
		if(!isset($this->args[2])){
			$output['view'] = '404';
		}
		else{
			$output['template'] = 'admin';	
			
			$getInvite = $this->model->get('user_invites', $this->args[2], array(), 'acceptCode');
			if(!$getInvite){
				$output['view'] = '404';
				return $output;
			}
			if($getInvite['accepted'] == 1){
				$output['view'] = 'old-complete';
				$output['title'] = 'Invitation Request';
				return $output;
			}
			if($getInvite['acceptUser'] != $this->data['user']['userId']){
				$output['view'] = '403';
				return $output;
			}
			
			$getInvite = $this->model->getInviteUsers($getInvite);
			$getInvite['info'] = json_decode($getInvite['info'], true);
			
			$output['invite'] = $getInvite;
			$output['view'] = $getInvite['type'];
			$output['title'] = 'Accept Invitation';
			$output['form_html'] = $this->model->getAcceptForm();
			$output['cancelled'] = false;
			$output['message'] = '';
			$output['message_class'] = '';
			if(posted()){
				if($_POST['decision'] == 'Accept'){
					$class = new $getInvite['class'];
					$func = 'complete_'.$getInvite['type'].'_request';
					try{
						$complete = $class->$func($getInvite);
					}
					catch(\Exception $e){
						$output['message'] = $e->getMessage();
						$output['message_class'] = 'text-error';
						$complete = false;
					}
					
					if($complete){
						$edit = $this->model->edit('user_invites', $getInvite['inviteId'], array('accepted' => 1, 'acceptDate' => timestamp()));
						redirect($complete);
					}
										
				}
				else{
					//cancel invite
					$delete = $this->model->delete('user_invites', $getInvite['inviteId']);
					$notify = Meta_Model::notifyUser($getInvite['sendUser'], 'emails.invites.'.$getInvite['type'].'_cancel', $getInvite['inviteId'], 'user-invite-cancel', false, $getInvite);
					$output['cancelled'] = true;
				}
			}
		}
		return $output;
	}
}
