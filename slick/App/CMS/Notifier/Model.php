<?php
namespace App\CMS;
use Core, UI, Util, App\Tokenly\AssetScout_Model;
class Notifier_Model extends Core\Model
{
	protected function getNotifyForm()
	{
		$form = new UI\Form;
		
		$groups = new UI\CheckboxList('groups');
		$getGroups = $this->getAll('groups');
		$groups->addOption(0, 'All');
		foreach($getGroups as $group){
			$groups->addOption($group['groupId'], $group['name']);
		}
		$groups->setLabel('Groups to Notify');
		$groups->setLabelDir('R');
		$form->add($groups);
		
		$token = new UI\Textbox('token');
		$token->setLabel('OR notify token holders, bitcoin address or username');
		$token->addAttribute('placeholder', 'Asset name, bitcoin address or username');
		$form->add($token);
		
		$message = new UI\Textarea('message');
		$message->addAttribute('required');
		$message->setLabel('Message:');
		$form->add($message);
		
		$form->setSubmitText('Send Notification');
		
		return $form;
	}
	
	protected function sendNotification($data)
	{
		$data['message'] = strip_tags(trim($data['message']), '<a><img><em><b><strong><u><s><i><sup>');	
		if(trim($data['message']) == ''){
			throw new \Exception('Please enter a message');
		}
		
		$sendUsers = array();
		if(isset($data['token']) AND trim($data['token']) != ''){
			$scout = new AssetScout_Model;
			$scoutAsset = $scout->scoutAsset(array('asset' => $data['token']));
			if(is_array($scoutAsset)){
				if($scoutAsset['isUser']){
					$sendUsers[] = $scoutAsset['user']['userId'];
				}
				elseif($scoutAsset['isAddress']){
					foreach($scoutAsset['users'] as $row){
						$sendUsers[] = $row['userId'];
					}
				}
				else{
					//send to token holders
					foreach($scoutAsset['list'] as $row){
						$sendUsers[] = $row['userId'];
					}
				}
			}
		}
		else{
			if(!isset($data['groups']) OR !is_array($data['groups']) OR count($data['groups']) == 0){
				throw new \Exception('Please select at least one group');
			}
					
			if(in_array(0, $data['groups'])){
				$sendAll = true;
			}
			else{
				$sendAll = false;
			}
			
			foreach($data['groups'] as $gKey => $groupId){
				$group = array('groupId' => $groupId);
				$groupUsers = $this->getAll('group_users', array('groupId' => $groupId));
				$group['users'] = array();
				foreach($groupUsers as $guser){
					$group['users'][] = $guser['userId'];
				}
				$data['groups'][$gKey] = $group;
				
			}		
			
			$users = $this->getAll('users', array(), array('userId'));
			foreach($users as $user){
				if($sendAll){
					$sendUsers[] = $user['userId'];
				}
				else{
					foreach($data['groups'] as $group){
						if(in_array($user['userId'], $group['users'])){
							$sendUsers[] = $user['userId'];
						}
					}
				}
			}
					
		}

		if(count($sendUsers) == 0){
			throw new \Exception('No users found');
		}
		
		foreach($sendUsers as $userId){
			$notify = \App\Meta_Model::notifyUser($userId, $data['message'], substr(time(), -5), 'push-notify');
		}
		return true;
	}
}
