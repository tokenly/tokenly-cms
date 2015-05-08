<?php
namespace App\Tokenly;
use Core, UI, App\Profile;
class AssetDrop_Model extends Core\Model
{
	public $coinFieldId = 12; //temporarily hardcoded
	
	public function getDropperForm($appData)
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
		foreach($getGroups as $group){
			$numInGroup = 0;
			foreach($xcpUsers as $user){
				$checkGroup = $this->getAll('group_users', array('userId' => $user['userId'], 'groupId' => $group['groupId']));
				if($checkGroup AND count($checkGroup) > 0){
					$numInGroup++;
				}
			}
			if($numInGroup == 0){
				continue;
			}
			$groups->addOption($group['groupId'], $group['name'].' ('.$numInGroup.')');
		}
		$groups->setLabel('Choose Groups:');
		$groups->setLabelDir('R');
		$form->add($groups);
		
		return $form;
		
	}
	
	public function initDrop($data, $appData)
	{
		$profModel = new Profile\User_Model;
		$xcpUsers = $profModel->getUsersWithProfile($this->coinFieldId);
		
		if(!isset($data['asset']) OR trim($data['asset']) == ''){
			throw new \Exception('Please enter in an asset name');
		}
		
		if(!isset($data['amount']) OR trim($data['amount']) == ''){
			throw new \Exception('Please enter in a total amount to send');
		}
		
		if(!isset($data['groups']) OR !is_array($data['groups']) OR count($data['groups']) == 0){
			throw new \Exception('Please select what user groups to send to');
		}
		
		$getAll = false;
		if(in_array(0, $data['groups'])){
			$getAll = true;
		}
		
		
		$users = array();
		foreach($xcpUsers as $user){
			if($getAll){
				$users[] = $user;
			}
			else{
				$getGroups = $this->getAll('group_users', array('userId' => $user['userId']));
				foreach($getGroups as $group){
					if(in_array($group['groupId'], $data['groups'])){
						$users[] = $user;
						continue 2;
					}
				}
			}
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
