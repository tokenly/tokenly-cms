<?php
namespace App\CMS;
use Core, UI, Util;
class Groups_Model extends Core\Model
{
	protected function getGroupForm($groupId = 0)
	{
		$form = new UI\Form;
		
		$name = new UI\Textbox('name');
		$name->addAttribute('required');
		$name->setLabel('Group Name');
		$form->add($name);
		
		$slug = new UI\Textbox('slug');
		$slug->setLabel('Slug (blank to auto generate)');
		$form->add($slug);	


		$isDefault = new UI\Checkbox('isDefault');
		$isDefault->setBool(1);
		$isDefault->setValue(1);
		$isDefault->setLabel('Default group?');
		$form->add($isDefault);
		
		$isSilent = new UI\Checkbox('isSilent');
		$isSilent->setBool(1);
		$isSilent->setValue(1);
		$isSilent->setLabel('Hide group name on profiles?');
		$form->add($isSilent);
		
		$displayName = new UI\Textbox('displayName');
		$displayName->setValue(0);
		$displayName->setLabel('Display Name');
		$form->add($displayName);		
		
		$displayRank = new UI\Textbox('displayRank');
		$displayRank->setValue(0);
		$displayRank->addClass('numeric');
		$displayRank->setLabel('Display Rank');
		$form->add($displayRank);
		
		$displayView = new UI\Textbox('displayView');
		$displayView->setLabel('Display Color #');
		$form->add($displayView);		
		

		if($groupId != 0){
			$getSites = $this->getAll('sites');
			$siteAccess = new UI\CheckboxList('siteAccess');
			$siteAccess->setLabel('Site Access');
			
			$options = array();
			foreach($getSites as $site){
				$options[$site['siteId']] = $site['name'];
			}
			$siteAccess->setOptions($options);
			$siteAccess->setLabelDir('R');
			$form->add($siteAccess);	



			$access = new UI\CheckboxList('moduleAccess');
			$access->setLabel('Module Access');
			
			$getModules = $this->fetchAll('SELECT m.*, a.name as appName	
										   FROM modules m
										   LEFT JOIN apps a ON a.appId = m.appId
										   ORDER BY m.appId ASC');
			
			$options = array();
			foreach($getModules as $module){
				$options[$module['moduleId']] = $module['appName'].'\\'.$module['name'];
			}
			$access->setOptions($options);
			$access->setLabelDir('R');
			$form->add($access);
			
			$permHead = new UI\FormHeading('App Permissions', 4);
			$form->add($permHead);
			
			$getApps = $this->getAll('apps');
			foreach($getApps as $app){
				$getPerms = $this->getAll('app_perms', array('appId' => $app['appId']));
				if(count($getPerms) > 0){
					
					$perms = new UI\CheckboxList('perms-'.$app['appId']);
					$perms->setLabel($app['name']);
					$perms->setLabelDir('R');
					$options = array();
					foreach($getPerms as $perm){
						$options[$perm['permId']] = $perm['permKey'];
					}
					$perms->setOptions($options);
					$form->add($perms);
				}
			}
		}
		return $form;
	}
	
	protected function getGroupModules($groupId, $idOnly = 0)
	{
		$get = $this->fetchAll('SELECT g.*, a.name as appName, m.name as moduleName
								FROM group_access g
								LEFT JOIN modules m ON m.moduleId = g.moduleId
								LEFT JOIN apps a ON a.appId = m.appId
								WHERE g.groupId = :id
								ORDER BY m.appId ASC
								', array(':id' => $groupId));
		
		if($idOnly == 0){
			return $get;
		}
		
		$output = array();
		foreach($get as $row){
			$output[] = $row['moduleId'];
		}
		
		return $output;
	}
	
	protected function getGroupSites($groupId, $idOnly = 0)
	{
		$get = $this->fetchAll('SELECT g.*, m.name as siteName
								FROM group_sites g
								LEFT JOIN sites m ON m.siteId = g.siteId
								WHERE g.groupId = :id
								ORDER BY m.name ASC
								', array(':id' => $groupId));
		
		if($idOnly == 0){
			return $get;
		}
		
		$output = array();
		foreach($get as $row){
			$output[] = $row['siteId'];
		}
		
		return $output;
	}


	protected function addGroup($data)
	{
		$req = array('name', 'isDefault', 'slug', 'siteId');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new \Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		if(trim($useData['slug']) == ''){
			$useData['slug'] = genURL($useData['name']);
		}
		
		$useData['isSilent'] = 0;
		if(isset($data['isSilent'])){
			if(intval($data['isSilent']) == 1){
				$useData['isSilent'] = 1;
			}
		}
		if(isset($data['displayName'])){
			$useData['displayName'] = trim($data['displayName']);
		}			
		if(isset($data['displayRank'])){
			$useData['displayRank'] = intval($data['displayRank']);
		}
		if(isset($data['displayView'])){
			$useData['displayView'] = trim($data['displayView']);
		}
		
		$add = $this->insert('groups', $useData);
		if(!$add){
			throw new \Exception('Error adding group');
		}
		
		return $add;
	}
		
	protected function editGroup($id, $data)
	{
		$req = array('name', 'isDefault', 'slug', 'siteId');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new \Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}

		if(trim($useData['slug']) == ''){
			$useData['slug'] = genURL($useData['name']);
		}
		
		$useData['isSilent'] = 0;
		if(isset($data['isSilent'])){
			if(intval($data['isSilent']) == 1){
				$useData['isSilent'] = 1;
			}
		}
		if(isset($data['displayName'])){
			$useData['displayName'] = trim($data['displayName']);
		}		
		if(isset($data['displayRank'])){
			$useData['displayRank'] = intval($data['displayRank']);
		}
		if(isset($data['displayView'])){
			$useData['displayView'] = trim($data['displayView']);
		}		

		$edit = $this->edit('groups', $id, $useData);
		if(!$edit){
			throw new \Exception('Error editing group');
		}
		
		$this->delete('group_sites', $id, 'groupId');
		if(!is_array($data['siteAccess'])){
			$data['siteAccess'] = array($data['siteAccess']);
		}
		foreach($data['siteAccess'] as $siteId){
			$this->insert('group_sites', array('groupId' => $id, 'siteId' => $siteId));
		}
		
		$this->delete('group_access', $id, 'groupId');
		foreach($data['moduleAccess'] as $moduleId){
			$this->insert('group_access', array('groupId' => $id, 'moduleId' => $moduleId));
		}
		
		$this->delete('group_perms', $id, 'groupId');
		$permList = array();
		foreach($data as $key => $val){
			$expKey = explode('-', $key);
			if($expKey[0] == 'perms' AND is_array($val)){
				foreach($val as $v){
					$permList[] = $v;
				}
			}
		}
		foreach($permList as $perm){
			$this->insert('group_perms', array('groupId' => $id, 'permId' => $perm));
		}

		return true;
	}
	
	protected function getGroupMembers($groupId)
	{
		$get = $this->fetchAll('SELECT u.userId, u.username, u.slug, u.email
								FROM group_users g
								LEFT JOIN users u ON u.userId = g.userId
								WHERE g.groupId = :groupId', array(':groupId' => $groupId));
		return $get;
	}
	

}
