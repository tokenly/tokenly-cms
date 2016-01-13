<?php
namespace App\Tokenly;
use Core, UI, App\Profile;
class AssetDrop_Model extends Core\Model
{
	public $coinFieldId = 12; //temporarily hardcoded
	
	protected function getDropperForm($appData)
	{
		$form = new UI\Form;
		$asset = new UI\Textbox('asset');
		$asset->setLabel('Asset Name:');
		$asset->addAttribute('required');
		$form->add($asset);
		
		$amount = new UI\Textbox('amount');
		$amount->setLabel('Total Amount:');
		$amount->addAttribute('required');
		$form->add($amount);
		
		$groups = new UI\CheckboxList('groups');
		$getGroups = $this->getAll('groups');
		$profModel = new Profile\User_Model;
		$xcpUsers = $profModel->getUsersWithProfile($this->coinFieldId);
		$groups->addOption(0, 'All ('.count($xcpUsers).')');
		$groupUsers = static_cache('group_users');
		if(!$groupUsers){
			$groupUsers = static_cache('group_users', $this->getAll('group_users'));
		}
		$userIds = array();
		foreach($xcpUsers as $user){
			$userIds[$user['userId']] = $user['userId'];
		}
		$groupCounts = array();
		foreach($groupUsers as $g){
			if(isset($userIds[$g['userId']])){
				if(!isset($groupCounts[$g['groupId']])){
					$groupCounts[$g['groupId']] = 0;
				}
				$groupCounts[$g['groupId']]++;
			}
		}
		foreach($getGroups as $group){
			$numInGroup = 0;
			if(isset($groupCounts[$group['groupId']])){
				$numInGroup = $groupCounts[$group['groupId']];
			}
			if($numInGroup == 0){
				continue;
			}
			$groups->addOption($group['groupId'], $group['name'].' ('.$numInGroup.')');
		}
		$groups->setLabel('Choose Groups:');
		$groups->setLabelDir('R');
		$form->add($groups);
		
		$token = new UI\Textbox('token', 'token');
		$token->setLabel('OR distribute to asset holders');
		$token->addAttribute('placeholder', 'Asset name');
		$form->add($token);
		
		return $form;
		
	}
	
	protected function initDrop($data, $appData)
	{
		$profModel = new Profile\User_Model;
		$xcpUsers = $profModel->getUsersWithProfile($this->coinFieldId);
		
		if(!isset($data['asset']) OR trim($data['asset']) == ''){
			throw new \Exception('Please enter in an asset name');
		}
		
		if(!isset($data['amount']) OR trim($data['amount']) == ''){
			throw new \Exception('Please enter in a total amount to send');
		}
			
		$users = array();
		if(isset($data['token']) AND trim($data['token']) != ''){
			//distribute to token holders
			$scout = new AssetScout_Model;
			$scout_asset = $scout->scoutAsset(array('asset' => $data['token']));
			if(is_array($scout_asset) AND isset($scout_asset['list'])){
				$token_holders = array();
				foreach($scout_asset['list'] as $k => $row){
					$token_holders[$row['userId']] = $k;
				}
				foreach($xcpUsers as $user){
					if(isset($token_holders[$user['userId']])){
						$users[] = $user;
					}
				}
			}
		}
		else{
			//distribute to group members
			if(!isset($data['groups']) OR !is_array($data['groups']) OR count($data['groups']) == 0){
				throw new \Exception('Please select what user groups to send to');
			}			
			$getAll = false;
			$groupIds = array();
			if(in_array(0, $data['groups'])){
				$getAll = true;
			}
			else{
				foreach($data['groups'] as $groupId){
					$groupIds[] = $groupId;
				}
			}			
			$groupUsers = static_cache('group_users_'.md5(json_encode($groupIds)));
			if(!$groupUsers){
				$groupUsers = static_cache('group_users_'.md5(json_encode($groupIds)), $this->fetchAll('SELECT * FROM group_users WHERE groupId IN('.join(',',$groupIds).')'));
			}		
			$useUsers = array();
			foreach($groupUsers as $guser){
				$useUsers[$guser['userId']] = $guser['userId'];
			}
			foreach($xcpUsers as $user){
				if($getAll){
					$users[] = $user;
					continue;
				}
				else{
					if(isset($useUsers[$user['userId']])){
						$users[] = $user;
						continue;
					}
				}
			}			
		}
		
		$used_users = array();
		foreach($users as $k => $user){
			if(isset($used_users[$user['userId']])){
				unset($users[$k]);
				continue;
			}
			$used_users[$user['userId']] = $k;
		}
	
		if(count($users) == 0){
			throw new \Exception('No valid users');
		}
		
		$totalAmount = floatval($data['amount']);
		$numUsers = count($users);
		$perUser = $totalAmount / $numUsers;
		$perUser = round($perUser, 8);
		
		$addressList = array();
		foreach($users as $user){
			$addressList[] = $user['value'].','.$perUser;
		}
		$addressList = join("\n", $addressList);
		
		$distData = array('asset' => $data['asset'], 'addresses' => $addressList);
		if($appData['user']){
			$distData['userId'] = $appData['user']['userId'];
		}
		
		$distModel = new Distribute_Model;
		$init = $distModel->initDistribution($distData);
		
		if($init){
			return $init;
		}
		
		throw new \Exception('Error initializing');
	}
}
